<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use League\CommonMark\Extension\CommonMark\Parser\Block\ThematicBreakParser;

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
            if ($song === null) {
                $this->info('No song found');

                return 0;
            }
            if ($song->bpm !== null) {
                $this->info('Song bpm already set');

                return 0;
            }
            $this->info("prepare updating |  $song->slug");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);

            $this->table(['slug', 'title', 'bpm', 'key'. 'energy', 'scale'], [
                $updatedSong,
            ]);

            return 0;
        }
        $songs = Song::where('bpm', '=', 0)->get();
        $this->info(count($songs).' songs found');
        if (count($songs) === 0) {
            $this->info('No song found');
            return 0;
        }
        $updatedSongs = [];

        $bar = $this->output->createProgressBar(count($songs));
        $this->newLine();
        $bar->start();

        $this->output->progressStart(count($songs));
        $this->newLine();


        $bar->setBarWidth(100);
        /** @var Song $song */
        foreach ($songs as $position => $song) {
            $number = $position + 1;
            $left = count($songs) - $position;
            $this->info("prepare updating | $song->slug | $number song out of ".count($songs)."| $left songs left");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
            $updatedSongs[] = $updatedSong;
//
//            $this->withProgressBar($left, function () use ($song, $bpm, $key, $updateService, &$updatedSongs) {
//                $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
//                $updatedSongs[] = $updatedSong;
//            });

            $bar->setMessage("$song->slug | $number song out of ".count($songs)."| $left songs left");
            $this->output->progressAdvance(1);
            $this->newLine();
            $bar->advance();
            $this->newLine();
        }

        $this->output->progressFinish();
        $bar->finish();
        $this->newLine();
        // table of updated songs
        $this->table(['slug', 'title', 'bpm', 'key'. 'energy', 'scale'], $updatedSongs);
        return 0;
    }

    /**
     * @param  bool  $bpm
     * @param  bool  $key
     * @param  SongUpdateService  $updateService
     * @param $song
     * @return Song
     */
    public function getUpdatedSong(bool $bpm, bool $key, SongUpdateService $updateService, $song): array
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
