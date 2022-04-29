<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use Illuminate\Console\Command;

class SongUpdateGenreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:genre {author?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Artis Genre from Spotify';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $spotifyService = new SpotifyService();
        $author = $this->argument('author');

        $genres = [] ;
        if ($author !== null ) {
            $genres = $spotifyService->getGenreByArtist($author);
            $song = Song::where('author', '=', $author)->first();
            $song->genre = $genres;
            $song->save();
            $displayGenres = json_encode($genres);
            $this->output->info("$author : $displayGenres");
            return 0;
        }

        $songs = Song::all();

        /** @var Song $song */
        foreach ($songs as $song) {

            if ($song->genre !== null || $song->author === null){
                continue;
            }
            $author = $song->author;
            $genres = $spotifyService->getGenreByArtist($author);

            $song->genre = $genres;

            // dd($genres);
            $song->save();

            $genre = json_encode($genres);
            $this->output->info("$author : $genre");
            sleep(5);
            $this->output->info("$author : DONE");

        }
        return 0;
    }
}
