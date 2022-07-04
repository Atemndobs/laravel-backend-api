<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;
use function Psy\sh;

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

        $bpm = str_replace('bpm: ', '', $res);
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
        return str_replace('http://mage.tech:8899/storage/', '', $path);
    }

    /**
     * @param Song $song
     * @return array
     */
    public function extracted(Song $song): array
    {
        $file = $this->getFilePath($song);

        dump($file);
        $slug = $song->slug;
        $shell = shell_exec(" ./storage/app/public/streaming_extractor_music storage/app/public/$file storage/app/public/$slug.json 2>&1");
        $shellRes = explode(" ", $shell);

        dd($shellRes);

        $error = str_contains($shell, "error = Operation not permitted");
        $error2 = str_contains($shell, "File does not exist ");

        if ($error || $error2) {
            dump($error);
            $path = str_replace('mp3', '.mp3', $slug);
            shell_exec("rm storage/app/public/audio/$path");
            $song->delete();
            return array(0, 0, 0, 0, 0);
        }
        if ($shellRes[1] === "1:") {
            dump($shell);
        }

        $analysed = Storage::get("public/$slug.json");
        $res = json_decode($analysed);

        $key_key = $res->tonal->key_key;
        // $key_scale = $res->tonal->key_scale;
        $key = $key_key;

        $chords_key = $res->tonal->chords_key;
        $chords_scale = $res->tonal->chords_scale;
        $chord = $chords_key . $chords_scale;

        $energy = $res->lowlevel->spectral_energy->max;
        $bpm = round($res->rhythm->bpm * 2) / 2;

        $album = $res->metadata->tags->album ?? '';
        $author = $res->metadata->tags->artist ?? '';
        if ($author !== "") {
            $author = $res->metadata->tags->artist[0];
        } else {
            $author = $song->author;
        }

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

    public function updateDuration(array|string $slug = null)
    {
        if ($slug === null) {
            $songs = Song::all();
        } else {
            $songs[] = Song::query()->where('slug', $slug)->first();
        }
        dump("found " . count($songs) . " songs");
        $completed = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            dump(count($songs));
            // get song duration from path using getID3
            $songPath = $song->path;
            $fileInfo = $this->getAnalyze($songPath);
            try {
                $duration = $fileInfo['playtime_seconds'];
                $song->duration = $duration;
                $song->save();
            } catch (\Exception $e) {
                dump($e->getMessage());
              continue;
            }
            // get info from id3v2 tags
            try {
                $this->getInfoFromId3v2Tags($fileInfo, $song);
                $song->save();
            }catch (\Exception $e) {
                dump($e->getMessage());
                continue;
            }

            try {
                $this->setImageFromSong($fileInfo['comments']['picture'][0]['data'], $song);
                $song->save();
            } catch (\Exception $e) {
                dump($e->getMessage());
                continue;
            }

            $completed[] =  $song->slug;
        }
        return $completed;
    }

    /**
     * @param $data
     * @param Song|null $song
     * @return void
     */
    public function setImageFromSong($data, Song|null $song): void
    {
// get image from picture data in comments tag
        $image = $data;
        // save image to storage/app/public/images/
        $imageName = $song->slug . '.jpg';
        $imagePath = "storage/app/public/images/$imageName";
        file_put_contents($imagePath, $image);
        // save image path as asset  to database
        $imagePath = asset("storage/images/$imageName");

        if ($song->image === null) {
            $song->image = $imagePath;
        }
    }

    /**
     * @param mixed $songPath
     * @return array
     */
    public function getAnalyze(mixed $songPath): array
    {
        $path = str_replace('http://mage.tech:8899/storage/', '', $songPath);
        $getID3 = new \getID3;
        $fileInfo = $getID3->analyze("storage/app/public/$path");
        return $fileInfo;
    }

    /**
     * @param $fileInfo
     * @param Song|null $song
     * @return void
     */
    public function getInfoFromId3v2Tags($fileInfo, Song|null $song): void
    {
         $idv = $fileInfo['tags']['id3v2'] ?? null;
        if (count($idv) < 5) {
            $title = $idv['title'][0] ?? null;
            // remove everything after the | in the title
            if (str_contains($title, '|')) {
                $title = substr($title, 0, strpos($title, '|'));
            }
            $title = trim($title);
            $song->title = $title;
           return ;
        }
        $genres = $idv['genre'];
        $artist = $idv['artist'];
        $title = $idv['title'][0];
        // remove everything after the | in the title
        if (str_contains($title, '|')) {
            $title = substr($title, 0, strpos($title, '|'));
        }
        $title = trim($title);
        // replace spaces with underscores in the title
        // $title = str_replace(' ', '_', $title);
        $comment = $idv['comment'][0];
        if ($song->genre === null) {
            $song->genre = $genres;
        }
        if ($song->author === null) {
            $song->author = $artist;
        }
        $song->title = $title;
        $song->comment = $comment;
    }
}
