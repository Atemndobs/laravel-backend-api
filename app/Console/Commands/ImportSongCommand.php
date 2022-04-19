<?php

namespace App\Console\Commands;

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
    protected $signature = 'song:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Songs from storag/audio directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $unClassified = [];
        $data = [];
        $allFiles = Storage::disk('public')->allFiles();

        $uploadService = new UploadService();
        $tracks = $this->cleanFiles($allFiles);
        $uploadedSongs = $uploadService->importSongs($tracks);

        DB::table('jobs')->delete();
        DB::table('failed_jobs')->delete();

        (new StrapiSongService())->importStrapiUploads();
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

        $deletablesHeader = ['deletables'];
        $deletablesBody = [];
        $deletablesBody = $this->cleanDb($uploadService);


        $this->output->table($headers, $data);
        $this->output->table($deletablesHeader, $deletablesBody);

        $total = sizeof($unClassified);
        $this->output->info("imported $total songs");
        info("=========================================IMPORT_DONE==========================================");
        return 0;
    }

    public function cleanDb(UploadService $uploadService)
    {
        $deletablesBody = [];
        /** @var  string $deletable */
        foreach ($uploadService->getDeletables() as $deletable){
            $deletablesBody[] = ['deletable' => $deletable];
        }
        return $deletablesBody;
    }

    public function cleanFiles($files)
    {
        $result = [];
        foreach ($files as $file){
            if (str_contains($file, 'audio')){
                $result[] = trim($file,'audio/');
            }
        }
       return $result;
    }
}
