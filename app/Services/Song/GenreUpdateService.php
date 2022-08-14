<?php

namespace App\Services\Song;

use App\Models\Song;
use App\Services\SongUpdateService;

class GenreUpdateService
{
    public function getGenreFromId3(Song $song) : Song
    {
        $id3Service = new SongUpdateService();
        $fileInfo = $id3Service->getAnalyze($song->path);
        dd($fileInfo);
    }

    public function checkSongGenre(Song $song) : bool
    {
        $genre = $song->genre;
        if ($genre !== null) {
            return false;
        }
        return true;
    }
}
