<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use App\Services\Scraper\SoundcloudService;
use App\Services\Song\GenreUpdateService;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;

class SongUpdateGenreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:genre {author?} {--s|slug=} ';

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
        $genres = [];
        $spotifyService = new SpotifyService();
        $author = $this->argument('author');
        $slug = $this->option('slug');
        if ($slug !== null) {
            $this->info('Update Genre for song with slug: '.$slug);
            $song = Song::query()->where('slug','like', "%$slug%")->first();
            $genreService = new GenreUpdateService();
            /** @var Song $song */
            $genres = $genreService->getGenreFromId3($song)->genre;
            $genres = json_encode($genres);
            $this->line("<fg=magenta> Genres | $genres </>");
            return 0;
        }

        $song = Song::query()->where('author', 'like', "%$author%")->first(
        //  ['author', 'title', 'genre']
        );
        if (strlen($author) > 0 || $author !== null) {
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
        $songs =
            Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->where('author', '!=', null)
                ->get();

        if (count($songs) === 0) {
            $this->output->info('song:genre | No more songs to update');
            ray('song:genre | No songs to update')->green();
            return 0;
        }
      //  $this->info('Found '.count($songs).' songs to update from Spotify');
        /** @var Song $song */
/*        foreach ($songs as $song) {
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
        }*/

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
        }
        $this->info('Found '.count($songs).' songs to update For ID3');
        $genreService = new GenreUpdateService();
        /** @var Song $song */
        foreach ($songs as $song) {
                try {
                     $songGenre = $genreService->getGenreFromId3($song)->genre;
                     $genre = json_encode($songGenre);
                     $this->output->info("$song->title : $genre");
                 }catch (\Exception $e) {
                     $this->warn($e->getMessage());
                 }
                 $left = count(
                     Song::query()->whereNull('genre')
                     ->orWhere('genre', '=', 0)
                     ->orWhere('genre', '=', '[]')
                     ->orWhere('genre', '=', null)
                     ->get()
                 );
                 $this->line("<fg=red;bg=cyan>$left songs left</>");
        }

        $left = count(
            Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->get()
        );

        if ($left >  0) {
            // redownload song from sound cloud and update genre
            $this->info('Found '.$left.' songs to update from SoundCloud');
            $this->info('Downloading songs from SoundCloud');
            $updateService = new SongUpdateService();
            foreach ($songs as $song) {
                $updateService->getSongDetailsFromSoundCloud($song);
            }
        }

    }
}
