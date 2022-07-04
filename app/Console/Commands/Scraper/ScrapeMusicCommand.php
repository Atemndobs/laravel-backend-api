<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;
use App\Services\Scraper\MusicBlogScraper;

class ScrapeMusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:song {site?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

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
        // sources = fakaza, tooxclusive, hiphopkit

        $scrapedMusic = [];
        $data = [];
        $site = $this->argument('site');
        $artist = $this->option('artist') ;
        $playlist = $this->option('playlist') ;
        $title = $this->option('title') ;

        $allOptions = $this->options();
        // filter out null values
        $allOptions = array_filter($allOptions, function ($arg) {
            return $arg !== null && $arg !== '' && $arg !== 'null' && $arg !== false;
        });
        if (count($allOptions) === 0){
            $this->info('No arguments provided');
            return 0;
        }
        $musicScraper = new MusicBlogScraper();

        if ($site === 'hiphopkit'){
            $scrapedMusic = $musicScraper->getSongsFromHiphopkit($artist);
        }
        if ($site === 'zaplaylist'){
            $scrapedMusic = $musicScraper->getSongsFromZaplaylist($allOptions);
        }

        dd($scrapedMusic);

        if ($site === 'sc'){
            $soundCloudService  = new  SoundcloudService();
            $choice_1 = "Get $artist PlayList";
            $choice_2 = "Get $artist Liked Songs";

            if ($artist !== null){
                $choice = $this->choice('Download Option', [
                    1 => $choice_1,
                    2 => $choice_2
                ],
                1
                );

                if ($choice === $choice_2) {
                    $soundCloudService->getLikedSongsByArtis($artist);
                }
                else {
                    ray($choice_1)->blue();
                    $playlistOptions = $soundCloudService->getArtistPlaylists($artist);
                    $playlistChoice = $this->choice('Download Option',
                        $playlistOptions
                    );
                    ray()->clearAll();
                    ray($playlistChoice);
                    return $soundCloudService->downloadPlaylist($playlistChoice);
                }

            }

            if ($playlist !== null){
                $soundCloudService->getCuratedPlaylist($playlist);
            }
        }elseif ($site !== null){
            $scrapedMusic = $musicScraper->getMusicFromSource("https://{$site}.com/", 'main/download-mp3/');
        }else{
            $tooxclusive = $musicScraper->getMusicFromSource("https://tooxclusive.com/", 'main/download-mp3/');
            $fakaza = $musicScraper->getMusicFromSource("https://fakaza.com/", 'download-mp3/');
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
        info("========================================= DONE_SCRAPPING $site =================================");
    }
}
