<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Support\Facades\Storage;

class SongUpdateService
{
    /**
     * @param Song $song
     * @return Song
     */
    public function updateBpmAndKey(Song $song): Song
    {
        [$chords_scale, $energy, $bpm, $author, $key] = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->energy = (float)$energy;
        $song->bpm = $bpm;
        $song->author = $author;
        $song->save();

        $slug = $song->slug;
        shell_exec("rm storage/app/public/$slug.json");
        return $song;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function updateBpm(Song $song): Song
    {
        $file = $this->getFilePath($song);
        $exec = shell_exec(" ./storage/app/public/streaming_rhythmextractor_multifeature storage/app/public/$file 2>&1");

        $res = explode("\n", $exec)[5];

        $bpm = str_replace('bpm: ', '' , $res);
        $bpm = round($bpm, 1);
        $song->bpm = $bpm;
        $song->save();
        return $song;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function updateKey(Song $song): Song
    {
        [$chords_scale, $key] = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->save();

        $slug = $song->slug;
        shell_exec("rm storage/app/public/$slug.json");
        return $song;
    }

    /**
     * @param Song $song
     * @return string
     */
    public function getFilePath(Song $song): string
    {
        $path = $song->path;
        $dir = Storage::disk('public');
        return str_replace('http://mage.tech:8899/storage/', '', $path);
    }

    /**
     * @param Song $song
     * @return array
     */
    public function extracted(Song $song): array
    {
        $file = $this->getFilePath($song);
        $slug = $song->slug;
        shell_exec(" ./storage/app/public/streaming_extractor_music storage/app/public/$file storage/app/public/$slug.json 2>&1");

        $analysed = Storage::get("public/$slug.json");
        $res = json_decode($analysed);

        $key_key = $res->tonal->key_key;
        // $key_scale = $res->tonal->key_scale;

        $key = $key_key;

        $chords_key = $res->tonal->chords_key;
        $chords_scale = $res->tonal->chords_scale;
        $chord = $chords_key . $chords_scale;

        $energy = $res->lowlevel->spectral_energy->max;

        //         dump(round(10.4 * 2 ) / 2);
        $bpm = round($res->rhythm->bpm * 2) / 2;

        $album = $res->metadata->tags->album ?? '';
        $author = $res->metadata->tags->artist ?? '';
        if ($author !== ""){
            $author = $res->metadata->tags->artist[0] ;
        }
        else {
            $author = $song->author;
        }

        // $title = $res->metadata->tags->title ? $res->metadata->tags->title[0] : $song->title;
        // $initialKey = $res->metadata->tags->initialkey[0];

        dump([
            'song' => $song->slug,
            'bpm' => $bpm,
            'scale' => $chords_scale,
            'key' => $key,
            'chord' => $chord,
            'author' => $author,
            'album' => $album,
            'energy' => $energy,
        ]);
        return array($chords_scale, $energy, $bpm, $author, $key);
    }
}