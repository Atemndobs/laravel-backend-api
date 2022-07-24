<?php

namespace App\Console\Commands\Song;

use App\Jobs\ClassifySongJob;
use App\Models\Catalog;
use App\Models\Song;
use App\Services\MoodAnalysisService;
use App\Services\Strapi\StrapiSongService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function example\int;

class ImportSongCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:import {source?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Songs from storage/audio directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = $this->argument('source');
        $source = $source ?? 'storage/audio';
        $unClassified = [];
        $data = [];
        $allFiles = Storage::disk('public')->allFiles();
        $this->info('Found ' . count($allFiles) . ' files');
        $uploadService = new UploadService();
        $tracks = $this->cleanFiles($allFiles);

        $uploadService->importSongs($tracks);
        $this->downloadStrapiSong();

        if ($source === 'strapi') {
            $songs = (new StrapiSongService())->importStrapiUploads();
            foreach ($songs as $song) {
                $data[] = $song->title;
            }

            return 0;
        }

        // call command : rabbitmq:queue-delete classify to delete all classify queues
        $this->call('rabbitmq:queue-delete', ['name' => 'classify']);
        $this->call('rabbitmq:queue-delete', ['name' => 'default']);
        $queuedSongs = $this->classifySongs();

        foreach ($queuedSongs as $title) {
            $unClassified[] = $title;
            $data[] = [
                'title' => $title,
                'status' => 'imported',
            ];
            info("$title : has been imported");
        }

        $headers = [
            'title',
            'status',
        ];
        $this->output->table($headers, $data);
        $this->info('Unclassified songs:');
        $total = count($unClassified);
        $this->output->info("imported $total songs from $source");
        info('=========================================IMPORT_DONE==========================================');

        info('Updating BPMS');
        $this->call('song:bpm');
        info('=========================================BPMS_DONE==========================================');

        $delSongs = count($this->getDeleted());
        $this->warn("$delSongs songs have been deleted from audio folder. Remember to download them from Blogs");
        return 0;
    }

    /**
     * @param UploadService $uploadService
     * @return array
     */
    public function cleanDb(UploadService $uploadService)
    {
        $deletableBody = [];
        /** @var string $deletable */
        foreach ($uploadService->getDeletables() as $deletable) {
            $deletableBody[] = ['deletable' => $deletable];
        }

        return $deletableBody;
    }

    /**
     * @param $files
     * @return array
     */
    public function cleanFiles($files): array
    {
        $result = [];
        $skipped = [];
        $images = [];
        $others = [];
        foreach ($files as $file) {
            // collect files containing .jpg extension in an $images array
            if (str_contains($file, '.jpg')) {
                $images[] = $file;
            }
            // collect files not containing "models" in an $others array
            if (! str_contains($file, '.jpg') && ! str_contains($file, '.mp3')) {
                $others[] = $file;
            }
            if (strpos($file, '.mp3') === false) {
                $skipped[] = $file;
                continue;
            }

            if (str_contains($file, 'audio')) {
                $file = str_replace('audio/', '', $file);
                $result[] = $file;
            }
        }

        $countSkipped = count($skipped);
        $countImages = count($images);
        $countOthers = count($others);
        $this->info(json_encode(
            [
                "Skipped $countSkipped files",
                "Found $countImages images",
                "Found $countOthers other files",
            ]
        ));

        return $result;
    }

    /**
     * @return void
     */
    public function downloadStrapiSong()
    {
        $allSongs = Song::all();
        $strapiService = new StrapiSongService();

        /** @var Song $song */
        foreach ($allSongs as $song) {
            if (str_contains($song->path, '1337')) {
                $done = $strapiService->dowloadStrapiSong($song);
                dump($done);
            }
        }
    }

    /**
     * @return array
     */
    public function classifySongs(): array
    {
        $songs = Song::all();
        $unClassified = [];
        $skipped = [];
        $deleted = [];

        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();
        /** @var Song $song */
        foreach ($songs as $song) {
            $bar->advance();

            // Remember to download these songs from Blogs
            if (!$this->checkAudioFile($song)) {
                if ($song->status === 'deleted' || $song->status === 'skipped' || $song->status === 'spotify-not-found') {
                    continue;
                }
                $song->status = 'deleted';
                $song->save();
                $deleted[] = $song->title;
            }elseif ((int)$song->analyzed == null && $song->duration >= 600) {
                $song->status = 'skipped';
                $song->analyzed = false;
                $song->save();
                $skipped[] = [
                    'song' => $song->slug,
                    'duration' => $song->duration,
                    'status' => $song->status,
                    'analyzed' => $song->analyzed,
                ];
            }elseif ((int)$song->analyzed == null) {
                $song->status = 'queued';
                $song->save();
                $unClassified[] = $song->slug;
//                $this->newLine(1);
//                $this->info("$song->slug : is unclassified");
            }
        }

        $bar->finish();


//        $bar2 = $this->output->createProgressBar(count($unClassified));
//        $bar2->start();
//        foreach ($unClassified as $slug) {
//            $bar2->advance();
//            ClassifySongJob::dispatch($slug);
////            $this->newLine(1);
////            info("$slug : has been queued");
//        }
//
//        $bar2->finish();

        return $unClassified;
    }

    /**
     * @param Song $song
     * @return bool
     */
    public function checkAudioFile(Song $song)
    {
        $path = $song->path;
        $path = str_replace('http://mage.tech:8899/storage/', '', $path);
        $path = storage_path('app/public/' .  $path) ;
        if (file_exists($path)) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getDeleted()
    {
        $songs = Song::all();
        $deleted = [];
        foreach ($songs as $song) {
            if ($song->status === 'deleted') {
                $deleted[] = $song->slug;
            }
        }
        return $deleted;
    }
}
