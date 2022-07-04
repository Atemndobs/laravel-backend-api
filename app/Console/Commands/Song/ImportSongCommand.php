<?php

namespace App\Console\Commands\Song;

use App\Models\File;
use App\Models\Song;
use App\Services\MoodAnalysisService;
use App\Services\Strapi\StrapiSongService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

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
        $unClassified = [];
        $data = [];
        $allFiles = Storage::disk('public')->allFiles();

        $uploadService = new UploadService();
        $tracks = $this->cleanFiles($allFiles);
        $uploadService->importSongs($tracks);
        $this->downloadStrapiSong();

        DB::table('jobs')->delete();
        DB::table('failed_jobs')->delete();

        if($source === 'strapi'){
            $songs = (new StrapiSongService())->importStrapiUploads();
        foreach ($songs as $song){
            $data[] = $song->title;
        }
        dump($data);
            return 0;
        }
        //(new StrapiSongService())->importStrapiUploads();
        $queuedSongs = (new MoodAnalysisService())->classifySongs();

        foreach ($queuedSongs as $title) {
            $unClassified[] = $title;
            $data[] = [
                'title' => $title,
                'status' =>'imported',
            ];
            info("$title : has been imported");
        }

        $headers = [
            'title',
            'status'
        ];

        $deletableBody = [];
        $deletableHeader = ['deletable'];
        $deletableBody[] = $this->cleanDb($uploadService);
        // $this->output->table($deletableHeader, $deletableBody);

        $this->output->table($headers, $data);

        $total = count($unClassified);
        $this->output->info("imported $total songs from $source");
        info("=========================================IMPORT_DONE==========================================");
        return 0;
    }

    public function cleanDb(UploadService $uploadService)
    {
        $deletableBody = [];
        /** @var  string $deletable */
        foreach ($uploadService->getDeletables() as $deletable){
            $deletableBody[] = ['deletable' => $deletable];
        }
        return $deletableBody;
    }

    public function cleanFiles($files): array
    {
        $result = [];
        foreach ($files as $file){
            if (str_contains($file, 'audio')){
                $file= str_replace('audio/', '', $file);
                $result[] = $file;
            }
        }
       return $result;
    }

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
}
