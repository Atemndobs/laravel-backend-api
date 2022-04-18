<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Widmogrod\Functional\tryCatch;

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

    public function cleanSongDb()
    {
        $allSongs = Song::all();

        $fails = [];
        $pass = [];
        /** @var Song $song */
        foreach ($allSongs as $song){

            $url = $song->path;
            $status = Http::get($url);
            dd($status);

            try {
                $url = $song->path;
                $status = Http::get($url);
                $pass[] = [$status => $song->path ];
            }catch (\Exception $exception){
                $fails[] = $song->path;
            }
        }

        return $pass;
    }
}
