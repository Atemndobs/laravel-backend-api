<?php

namespace App\Console\Commands\Scraper;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use Illuminate\Console\Command;

class SpotifySongDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Spotify:song {db?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Sotify Song By Title';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new SpotifyService();
        $db = $this->argument('db');

        if (!$db) {
            $title = $this->ask('Enter Song Title');
            dd($title);
        }
        $songs = Song::query()
            ->where('status', 'like', '%' . 'deleted'. '%')
            ->get();

        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();
        $downloaded = [];
        foreach ($songs as $song) {

            $bar->advance();
            try {
                $url = $service->getSongUlrByTitle($song->title);
                $downloaded[] = $song->title;
                $downloaded[] = $url;
            }catch (\Exception $e) {
                $this->error($e->getMessage());
                $song->status = 'spotify-not-found';
                continue;
            }
            $this->info($url);
            // call spotity dowload command
            $this->call('spotify', [
                'url' => $url
            ]);
            $song->status = 'downloaded';
            $song->save();
            $this->line('Song ' . $song->title . ' |' . 'new status' . $song->status);
        }
        $bar->finish();

        // put downloaded songs to table
        $this->table(['title', 'url'], $downloaded);
        $this->info("Doenloaded songs: " . count($downloaded));
        return 0;
    }
}
