<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

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
    protected $description = 'Get Artist Genre from Spotify';

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
            dump($genres);
            $song = Song::query()->where('author', '=', $author)->first(
             //  ['author', 'title', 'genre']
            );

            if ($song === null) {
                $song = Song::query()->where('author', 'like', "%$author%")->first();
            }
            $song->genre = $genres;
            /// $song->get(['genre']);

            $song->setAttribute();
            $song->save();
            $displayGenres = json_encode($genres);
            $this->output->info("$author : $displayGenres");
            return 0;
        }

        $songs = Song::where('genre', '=', null)
            ->where('author', '!=', null)
            ->get();

        if (count($songs) === 0) {
            $this->output->info("song:genre | No songs to update");
            ray("song:genre | No songs to update")->green();
            return 0;
        }

        $this->info("Found ".count($songs)." songs to update");
        /** @var Song $song */
        foreach ($songs as $song) {
            $author = $song->author;
            if ($author === 'unknown') {
                $genres = ["remix"];
            }else{
                $genres = $spotifyService->getGenreByArtist($author);
                sleep(5);
            }
            $song->genre = $genres;
            $song->save();
            $genre = json_encode($genres);
            $this->output->info("$author : $genre");
            $left = count(Song::where('genre', '=', null)
                ->where('author', '!=', null)
                ->get());
            $this->line("<fg=red;bg=cyan>$left songs left</>");
            ray("Artist => $author  |  genres => $genre | $left songs pending genres")->blue();
           //  ray("$left songs pending genres")->blue();
          //  $this->output->warning("$left songs left");

        }
        return 0;
    }
}
