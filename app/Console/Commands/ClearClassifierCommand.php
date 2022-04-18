<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ClearClassifierCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the Classifier Audio Dirrectory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $url = "http://localhost:3000/music/delete";
        $response = Http::get($url)->body();
        $this->output->info("Audio directory Cleaned");
        return 0;
    }
}
