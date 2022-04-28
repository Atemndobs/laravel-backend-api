<?php

namespace App\Console\Commands;

use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;
use App\Services\Scraper\MusicBlogScraper;

class ScrapeMusicCommandCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:song {site?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape music from Chosen website ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scrapedMusic = [];
        $data = [];
        $site = $this->argument('site');
        $musicScraper = new MusicBlogScraper();
        $soundCloundService  = new  SoundcloudService();

        dd($soundCloundService->getCuratedPlaylist($site));



        if ($site !== null){
            $scrapedMusic = $musicScraper->getMusicFromSource("https://{$site}.com/", 'main/download-mp3/');
        }else{
            $tooxclusive = $musicScraper->getMusicFromSource("https://tooxclusive.com/", 'main/download-mp3/');
            $fakaza = $musicScraper->getMusicFromSource("https://fakaza.com/", 'download-mp3/');
        //    $jaguda = $musicScraper->getMusicFromSource("https://jaguda.com/", 'music-mp3/');

            $scrapedMusic = array_merge($tooxclusive, $fakaza);
        }

        $this->extracted($scrapedMusic, $site, $data);
        return 0;
    }

    /**
     * @param array $scrapedMusic
     * @param array|string|null $site
     * @param array $data
     * @return void
     */
    public function extracted(array $scrapedMusic, array|string|null $site, array $data): void
    {
        $headers = [
            'source',
            'link'
        ];

        if (count($scrapedMusic) > 0) {
            foreach ($scrapedMusic as $music) {
                $data[] = [
                    'source' => $site,
                    'link' => $music,
                ];
            }
        }


        $this->output->table($headers, $data);

        $total = count($scrapedMusic);
        $this->output->info("extracted $total songs");
        info("=========================================DONE_SCRAPPING $site=================================");
    }
}
