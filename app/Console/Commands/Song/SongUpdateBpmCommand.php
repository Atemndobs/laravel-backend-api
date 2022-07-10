<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;

class SongUpdateBpmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:bpm {slug?} {--f|field=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update song bpm and key , argument slug, options key and bpm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updateService = new SongUpdateService();
        $slug = $this->argument('slug');
        $bpm = $this->option('field') === 'bpm';
        $key = $this->option('field') === 'key';

        if ($slug !== null) {
            $song = Song::where('slug', '=', $slug)->first();
            $this->info("prepare updating |  $song->slug");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
            $this->info($updatedSong->bpm);

            return 0;
        }
        $songs = Song::where('bpm', '=', 0)->get();
        $this->info(count($songs).' songs found');
        $updatedSongs = [];

        /** @var Song $song */
        foreach ($songs as $position => $song) {
            $number = $position + 1;
            $left = count($songs) - $position;
            $this->info("prepare updating | $song->slug | $number song out of ".count($songs)."| $left songs left");

            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
            $updatedSongs[] = $updatedSong->bpm;
        }

        dump($updatedSongs);

        return 0;
    }

    /**
     * @param  bool  $bpm
     * @param  bool  $key
     * @param  SongUpdateService  $updateService
     * @param $song
     * @return Song
     */
    public function getUpdatedSong(bool $bpm, bool $key, SongUpdateService $updateService, $song): Song
    {
        $updatedSong = new Song();
        if (! $bpm && ! $key) {
            $updatedSong = $updateService->updateBpmAndKey($song);
        }
        if ($bpm && ! $key) {
            $updatedSong = $updateService->updateBpm($song);
        }
        if ($key && ! $bpm) {
            $updatedSong = $updateService->updateKey($song);
        }

        return $updatedSong;
    }
}
