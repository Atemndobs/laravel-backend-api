<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\Blogs\Clacified;
use Illuminate\Console\Command;

class BlogMusicDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:blog {site?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Curated Music From Blogs like Clacified';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('site') === 'null') {
            $this->info('Downloading latest music from all blogs');
            return 0;
        }
        $site = $this->argument('site');
        $artist = $this->option('artist');

        $service = new Clacified();
        $top20 = $service->getTop20();

        dd($top20);
        return 0;
    }
}
