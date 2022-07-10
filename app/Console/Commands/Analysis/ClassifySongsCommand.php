<?php

namespace App\Console\Commands\Analysis;

use App\Services\MoodAnalysisService;
use Illuminate\Console\Command;

class ClassifySongsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $unClassified = (new MoodAnalysisService())->classifySongs();

        $this->output->info('Queued tracks');
        $headers = [
            'title',
            'status',
        ];

        $data = [];
        foreach ($unClassified as $title) {
            $data[] = [
                'title' => $title,
                'status' => 'imported',
            ];
            info("$title : has been imported");
        }

        $this->output->table($headers, $data);
        info('=========================================CLASSIFY_COMPLETE====================================');

        return 'job has been queued';
    }
}
