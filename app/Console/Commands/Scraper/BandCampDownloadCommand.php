<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\BandcampService;
use Illuminate\Console\Command;

class BandCampDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:bandcamp {url?} {--a|artist=} {--t|title=}';

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
        $url = $this->argument('url');
        $artist = $this->option('artist');
        $this->info("Downloading from $url");
        $service = new BandcampService();
        $service->getArtistByName($artist);

        return 0;
    }
}
