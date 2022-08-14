<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use App\Services\Song\GenreUpdateService;
use Illuminate\Console\Command;

class SongUpdateGenreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:genre {author?} {--s|slug=null} ';

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
        $slug = $this->option('slug');
        if ($slug !== null) {
            $this->info('Update Genre for song with slug: '.$slug);
            $song = Song::query()->where('slug','like', "%$slug%")->first();
            if ($song === null) {
                $this->error('Song not found');
                return 1;
            }
            $genreService = new GenreUpdateService();
            /** @var Song $song */
            $genres = $genreService->getGenreFromId3($song);
            return 0;
        }

        $song = Song::query()->where('author', 'like', "%$author%")->first(
        //  ['author', 'title', 'genre']
        );
        $genres = [];


        if ($author !== null) {
            /** @var Song $song */
            $genres = $spotifyService->getGenreByArtist($author, $song);
            if (count($genres) === 0) {
                $this->info('No genres found');

                return 0;
            }


            if ($song === null) {
                $song = Song::query()->where('author', 'like', "%$author%")->first();

            }
            if ($song->genre !== null) {
                $gen = json_encode($song->genre);
                $title = $song->title;
                $this->info("$title by  $author : $gen");
                return 0;
            }
            $song->genre = $genres;

            $song->save();
            $displayGenres = json_encode($genres);
            $this->output->info("$author : $displayGenres");

            return 0;
        }

        // get genres from getId
        $this->getSongGenreFromId3();

        dd('WATIS');
        $songs =
            Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->where('author', '!=', null)
                ->get();

        if (count($songs) === 0) {
            $this->output->info('song:genre | No songs to update');
            ray('song:genre | No songs to update')->green();
            return 0;
        }

        $this->info('Found '.count($songs).' songs to update from Spotify');
        /** @var Song $song */
        foreach ($songs as $song) {
            $author = $song->author;
            if ($author === 'unknown') {
                $genres = ['remix'];
            } else {
                $genres = $spotifyService->getGenreByArtist($author, $song);
                sleep(5);
            }

            if ($song->genre !== null) {
                $gen = json_encode($song->genre);
                $this->info("$author : $gen");
                continue;
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
        }

        return 0;
    }

    private function getSongGenreFromId3(): void
    {
        $songs = Song::query()->whereNull('genre')
            ->orWhere('genre', '=', 0)
            ->orWhere('genre', '=', '[]')
            ->orWhere('genre', '=', null)
            ->get();

        if (count($songs) === 0) {
            $this->output->info('song:genre | No songs to update from ID3');
            ray('song:genre | No songs to update')->green();
            return;
        }
        $this->info('Found '.count($songs).' songs to update from ID3');

        $genreService = new GenreUpdateService();
        /** @var Song $song */
        foreach ($songs as $song) {
            $genres = $genreService->getGenreFromId3($song);
            $genre = json_encode($genres);
            $this->output->info("$song->title : $genre");
            $left = count(
                Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->get()
            );
            $this->line("<fg=red;bg=cyan>$left songs left</>");
        }
    }
}
