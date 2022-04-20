<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        $song->uploadFileToDisk($track, 'path', 'public', 'audio');
        $existingSong = $this->getExistingSong($song);

        if ($existingSong){
            return $existingSong;
        }

        $filledSong = $this->loadSong($track, $song);
        $filledSong->status = 'uploaded';
        $filledSong->save();

       // (new MoodAnalysisService())->getAnalysis($filledSong->title);

        ClassifySongJob::dispatch($filledSong->title);
        return $filledSong->getDirty();
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
    public function importSongs(array $tracks)
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
            $api_url = env('APP_URL') . '/api/songs/match/';
            $song->related_songs = $api_url . $file_name;
            $this->fillSong($source, $song, $type, $file_name, $ext);
            $song->save();
            $response[] = $song;
            $this->deletables[] = $this->deletItem;
            ClassifySongJob::dispatch($file_name);
        }
        return $response;
    }

    /**
     * @param UploadedFile $track
     * @param Song $song
     * @return Song
     */
    protected function loadSong(UploadedFile $track, Song $song): Song
    {
        $name = $track->getClientOriginalName();
        $name = str_replace('&', '-', $name);
        $ext = $track->getClientOriginalExtension();
        $type = $track->getMimeType();
        $source = 'uploaded';

        $api_url = env('APP_URL') . '/api/songs/match/';
        $song->related_songs = $api_url . $name;
        $this->fillSong($source, $song, $type, $name, $ext);
        return $song;
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
     * @return array
     */
    protected function processAndSaveSong(mixed $file, Song $song): Song
    {
        $file_name = $file->getClientOriginalName();
        $new_file_name = str_replace('&', '-', $file_name);
        $file_path = $file->storeAs('audio', $new_file_name, 'public');
        $full_path = asset(Storage::url($file_path));
        $song->path = $full_path;

        info($song);
        $existingSong = $this->getExistingSong($song);

        if ($existingSong) {
            return $existingSong;
        }

        $ext = $file->getClientOriginalExtension();
        $type = $file->getMimeType();
        $source = 'uploaded';
        $song->status = 'uploaded';
        $api_url = env('APP_URL') . '/api/songs/match/';


        $song->related_songs = $api_url . $file_name;
        $this->fillSong($source, $song, $type, $new_file_name, $ext);
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
        $file_name = str_replace('&', '-', $file);
       // Storage::putFileAs($path_to_store, $storeFile, $file_name);
        $full_path = asset(Storage::url('audio/' . $file_name));

        $deletables = [];
        if ($file_name  !== $file) {
            $oldFile = storage_path('app/public/audio/' .  $file);
            $this->deletItem = $file;
            rename($oldFile, $file_name);
            $this->addDeletables($storeFile);
        }
        $song->path = $full_path;
        return $file_name;
    }
}

