<?php

namespace App\Services;

use App\Exceptions\SongException\NotClassifiedException;
use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\SongException\NotAnalyzedException;

class ClassifyService
{
    protected Song $song;

    protected array $songs;

    /**
     * @param  string  $track
     * @return array|string[]
     */
    public function analyzeTrack(string $track)
    {
        if ($this->checkSong($track)) {
            return [
                'status' => $this->song->status,
                'key' => $this->song->key,
                'bpm' => $this->song->bpm,
                'scale' => $this->song->scale,
                'danceability' => $this->song->danceability,
                'happy' => $this->song->happy,
                'sad' => $this->song->sad,
                'analyzed' => $this->song->analyzed,
            ];
        }
        ClassifySongJob::dispatch($track);
        return [
            'status' => 'qeued',
            'track' => $track,
        ];
    }

    /**
     * @param  string  $title
     * @return bool
     */
    public function checkSong(string $title): bool
    {
        $existingSong = $this->checExistingSong($title);
        if ($existingSong->key != null && $existingSong->bpm != null) {
            $this->song = $existingSong;

            return true;
        }

        return false;
    }

    /**
     * @param  string  $title
     * @return array|Song
     */
    public function checExistingSong(string $title): array | Song
    {
        $existingSong = Song::where('title', '=', $title)->first();
        if ($existingSong == null) {
            throw new ModelNotFoundException("$title does not exist , Please upload and try again");
        }

        return $existingSong;
    }

    /**
     * @param array|string|null $slug
     * @return array|Song
     * @throws NotAnalyzedException
     */
    public function reClassify(array|string|null $slug = ''): Song|array
    {
        if ($slug == '') {
            $songs = Song::where('analyzed', '=', true);
        } else {
            $songs = Song::where('slug', '=', $slug)->get('slug');
            if ($songs->count() == 0) {
                throw new ModelNotFoundException("$slug does not exist , Please upload and try again");
            }
            if ($songs->count() > 1) {
                throw new ModelNotFoundException("$slug is not unique , Please upload and try again");
            }
            $song = $songs->first();
            dump([
                'analyzed' => $song->analyzed,
                'int_vale' => $song->analyzed == 1,
                'slug' => $song->slug]);
            if ((int)$song->analyzed !== 1) {

                dump(['analyzed' => $song->analyzed, 'slug' => $song->slug]);
                $message = "Song $slug is not analyzed yet, please analyze it first";
                throw new NotAnalyzedException($message);
            }
            /** @var Song $song */
            if ($song->status == 're-classified' || $song->classification_properties != null) {
                $message = "Song $slug is already classified";
                throw new NotClassifiedException($message);
            }
            return $song->toArray();
        }

        return $songs->get('slug')->toArray();
    }
}
