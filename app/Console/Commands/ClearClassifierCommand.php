<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        $deleteItems = [];
        $deleteItems[] = $this->cleanSongDb();
        $url = "http://localhost:3000/music/delete";
        $response = Http::get($url)->body();

        $deleteItems[] = $response;
        $this->output->info("Audio directory Cleaned");

        dump($deleteItems);
        return $response;
    }

    public function cleanSongDb()
    {
        $allSongs = Song::all();
        $dir = Storage::allFiles('public/audio');

        // dd($dir);
        //  93 => "public/audio/DJ SNAKE - Loco Contigo (with J Balvin & Tyga).mp3"
        //  dd(Storage::get('public/audio/DJ SNAKE - Loco Contigo (with J Balvin & Tyga).mp3'));
        // public/audio/DJ SNAKE - Loco Contigo (with J Balvin & Tyga).mp3

        $fails = [];
        $pass = [];
        /** @var Song $song */
        foreach ($allSongs as $song){

            $base = $song->path;
            $local = "http://localhost:8899/";
            $url = str_replace('mage.tech', 'localhost', $base);

            try {
                $status = Http::get($url)->status();
                if ($status !== 200) {
                    $fails[$song->id] = $song->title;

                    $path = trim($song->path, 'http://mage.tech:8899/');
                    $path = substr($path, 7);
                    $checkPath = Storage::get('public'.$path);

                    if ($checkPath === null) {
                        $song->delete();
                    }
                }

            }catch (\Exception $exception){
                info($exception->getMessage());
            }
        }

        return $fails;
    }
}
