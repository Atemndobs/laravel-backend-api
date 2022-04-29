<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    protected Song $song;

    public array $deletables = [];

    /**
     * @return array
     */
    public function getDeletables(): array
    {
        return $this->deletables;
    }

    /**
     * @param array $deletables
     * @return UploadService
     */
    public function setDeletables(array $deletables): UploadService
    {
        $this->deletables = $deletables;
        return $this;
    }


    public function addDeletables($deleteItem) : array
    {
        $this->deletables[] = $deleteItem;
        return $this->deletables;
    }

    public string $deletItem = '';

    public function uploadSong(UploadedFile $track)
    {
        $song = new Song();
        $this->processAndSaveSong($track, $song);
        $existingSong = $this->getExistingSong($song);

        if ($existingSong){
            return $existingSong;
        }
        $song->status = 'uploaded';
        $song->save();
       // (new MoodAnalysisService())->getAnalysis($filledSong->title);
        ClassifySongJob::dispatch($song->title);
        return $song->getDirty();
    }

    /**
     * @param array $tracks
     * @return array
     */
    public function batchUpload(array $tracks)
    {
        $response = [];
        foreach ($tracks as $file) {
            $song = new Song();
            if ($file->isValid()) {
                $filledSong = $this->processAndSaveSong($file, $song);
                $response[] = $filledSong;

                ClassifySongJob::dispatch($filledSong->title);
            }
        }
        return $response;
    }

    /**
     * @param array $tracks
     * @return array
     */
    public function importSongs(array $tracks): array
    {
        $response = [];
        $this->deletables = [];
        foreach ($tracks as $file) {

            $song = new Song();
            $file_name = $this->getFullSongPath($file, $song);
            $song->status = 'imported';
            $existingSong = $this->getExistingSong($song);

            if ($existingSong) {
                $response[] = $existingSong;
                continue;
            }

            $ext = substr($file_name, -3);
            $type = $ext;
            $source = 'imported';
            $this->fillSong($source, $song, $type, $file, $ext);
            $song->title = $file;
            $song->save();
            $response[] = $song;
            $this->deletables[] = $this->deletItem;
            ClassifySongJob::dispatch($file_name);
        }
        return $response;
    }

    /**
     * @param Song $song
     * @return mixed
     */
    protected function getExistingSong(Song $song)
    {
        return Song::where('path', '=', $song->path)->first();
    }

    /**
     * @param mixed $file
     * @param Song $song
     * @return Song
     */
    protected function processAndSaveSong(mixed $file, Song $song): Song
    {
        $file_name = $file->getClientOriginalName();
        $type = $file->getMimeType();
        $source = 'uploaded';
        $api_url = env('APP_URL') . '/api/songs/match/';
        $ext = substr($file_name, -4);
        $new_file_name = str_replace($ext, '', $file_name);
        $new_file_name = Str::slug($new_file_name, '_');
        $new_file_name .= $ext;

        $file_path = $file->storeAs('audio', $new_file_name, 'public');
        $full_path = asset(Storage::url($file_path));
        $song->status = 'uploaded';
        $song->path = $full_path;

        $existingSong = $this->getExistingSong($song);

        if ($existingSong) {
            return $existingSong;
        }

        $slug = Str::slug($new_file_name, '_');
        $song->slug = $slug;
        $song->related_songs = $api_url . $slug;
        $this->fillSong($source, $song, $type, $file_name, $ext);
        $song->save();

        return $song;
    }

    /**
     * @param string $source
     * @param Song $song
     * @param string|null $type
     * @param string $name
     * @param string $ext
     * @return void
     */
    public function fillSong(string $source, Song $song, ?string $type, string $name, string $ext): void
    {
        $fields = [
            'aggressiveness' => '',
            'author' => '',
            'bpm' => '',
            'comment' => '',
            'created_at' => '',
            'created_by_id' => '',
            'danceability' => '',
            'energy' => '',
            'happy' => '',
            'id' => '',
            'key' => '',
            'link' => $source,
            'path' => $song->path,
            'relaxed' => '',
            'sad' => '',
            'slug' => $song->slug,
        //    'image' => '',
            'source' => $type,
            'title' => $name,
            'extension' => $ext,
        ];
        $song->fill($fields);
    }

    /**
     * @param mixed $file
     * @param Song $song
     * @return array|mixed|string|string[]
     */
    protected function getFullSongPath(mixed $file, Song $song): mixed
    {
        Storage::disk('public');

        $path_to_store = Storage::path('public/audio/');
        $storeFile = $path_to_store . $file;
        $file_name = $file;
        $ext = substr($file_name, -4);
        $file_name = str_replace($ext, '', $file_name);
        $file_name = Str::slug($file_name, '_');
        $file_name .= $ext;

        $full_path = asset(Storage::url('audio/' . $file_name));
        if ($file_name  !== $file) {
            $oldFile = storage_path('app/public/audio/' .  $file);
            $newFile = storage_path('app/public/audio/' .  $file_name);
            rename($oldFile, $newFile);
            $this->deletItem = $file;
            $this->addDeletables($storeFile);
        }

        $api_url = env('APP_URL') . '/api/songs/match/';
        $slug = Str::slug($file_name, '_');

        $song->path = $full_path;
        $song->slug = $slug;
        $song->related_songs = $api_url . $slug;
        return $file_name;
    }
}

