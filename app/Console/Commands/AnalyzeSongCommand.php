<?php

namespace App\Console\Commands;

use App\Services\MoodAnalysisService;
use Illuminate\Console\Command;

class AnalyzeSongCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:analyze {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze / Classify Single Song';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $title =$this->argument('title');
        (new MoodAnalysisService())->getAnalysis($title);

        $this->output->info("$title : analysis is in progress");
        info("$title : analysis is in progress");

        return "job has been queued";
    }
}
