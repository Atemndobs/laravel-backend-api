<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\FindSongService;
use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use function PHPUnit\Framework\containsIdentical;

class FindSongCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:find {--a|slug=null} {--b|bpm=null} {--s|scale=null} {--k|key=null}
    {--g|genre=null} {--t|title=null}';

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
        ray()->clearAll();
        $findService = new FindSongService();
        $allArgs = $this->options();
        $header = ['ID','Title', 'slug', 'BPM', 'key', 'scale', 'path'];

        $allArgs = array_filter($allArgs, function ($arg) {
            return $arg !== null && $arg !== '' && $arg !== 'null' && $arg !== false;
        });

        if (count($allArgs) === 0) {
            $this->info('No arguments provided');
            $this->info(count(Song::all()->toArray()) . ' songs found');
            return 0;
        }


        if (count($allArgs) > 1) {
            $this->info( count($allArgs) . " arguments : " . implode(', ', $allArgs) . ' provided');
            // find by multiple attributes
            $songs = $findService->findByMultipleAttributes($allArgs);
            $this->table($header, $songs);

            return 0;
        }
        ray($allArgs);
        foreach ($allArgs as $key => $value) {
            $this->info("$key: $value");
            $songs = $findService->{'findBy' . ucfirst($key)}($value);

            if (count($songs) === 0) {
                $this->info('No songs found');
                return 0;
            }

            if (count($songs) === 1) {
                $this->info('1 song found');
                $this->table($header, $songs[0]);
                return 0;
            }

            // display songs in chunks of 10
            if (count($songs) > 1) {
                $collapsed = Arr::collapse($songs);
                $this->info('Found ' . count($collapsed) . ' songs');
                $results = [];
                foreach ($songs as $chunkId => $chunk) {
                    dump($chunkId);
                    foreach ($chunk as $song) {
                        $results[] =  $song['title'] . ' by ' . $song['slug'];
                    }
                    $results[] = 'Next 10 songs';
                    $choice = $this->choice('Please chose a song', $results, 0);

                    if ($choice === 'Next 10 songs') {
                        $results = [];
                        continue;
                    }else{
                        $this->info($choice);
                    }
                }
                $this->info('Listen to : '. $choice);
            }
        }

        return 0;
    }
}
