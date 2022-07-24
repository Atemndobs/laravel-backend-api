<?php

namespace App\Console\Commands\Song;

use App\Models\Catalog;
use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use League\CommonMark\Extension\CommonMark\Parser\Block\ThematicBreakParser;
use function Amp\call;
use function example\ask;

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
                $this->info('No song found for BPM Update. All song BPMs updated');
                return 0;
            }

            /** @var Song $song */
            if ($song->bpm !== null || $song->bpm !== 0) {
                $this->info('Song bpm already set');
                $res = $this->askWithCompletion('Do you want to update bpm?', ['y', 'n'], 'y');
                if ($res === 'n') {
                    $this->warn('Update skipped');
                    return 0;
                }

                $this->withProgressBar(1, function () use ($song, $bpm, $key, $updateService, &$updatedSongs) {
                $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
                $updatedSongs[] = $updatedSong;
                    $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], [
                        $updatedSong,
                    ]);
            });

                return 0;
            }
            $this->info("prepare updating |  $song->slug");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);

            $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], [
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
        $this->output->progressStart(count($songs));
        $this->newLine();
        // start time in seconds
        $start = time();
        /** @var Song $song */
        foreach ($songs as $position => $song) {
            // output with magenta color and gray bg
            $this->output->text("<fg=magenta;bg=gray>Current BPM : $song->bpm</>");
            if ((float)$song->bpm != 0) {
                $this->output->progressAdvance();
                continue;
            }

            $number = $position + 1;
            $left = count($songs) - $position;
            $this->info("updating | $song->slug | $number song out of ".count($songs)."| $left songs left");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
            $updatedSongs[] = $updatedSong;
            $this->output->progressAdvance(1);
            $this->newLine();
            // end time in seconds
            $end = time() - $start;
            // progress in seconds
            $this->warn("Time taken: ".($end)." seconds");
        }

        $this->output->progressFinish();
        $this->newLine();
        // table of updated songs
        $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], $updatedSongs);

        // delete all queues in default queue
        $this->call('rabbitmq:queue-delete', ['name' => 'default']);
        $this->info("Updated songs: ".count($updatedSongs));

        $this->call('index:reindex');
        $this->call('scout:import', ['model' => Song::class]);
        $this->info('Scout index updated');
        $this->call('scout:import', ['model' => Catalog::class]);
        $this->info('Scout index updated');

        return 0;
    }

    /**
     * @param bool $bpm
     * @param bool $key
     * @param SongUpdateService $updateService
     * @param $song
     * @return array
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
