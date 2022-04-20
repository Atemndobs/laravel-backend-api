<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Facades\Http;

class MoodAnalysisService
{
    public function getAnalysis(string $title): array
    {
        $existingSong = Song::where('title', '=', $title)->first();

        if ($existingSong->analyzed) {
            return [
                'status' =>  $existingSong->status
            ];
        }

        $url = "http://localhost:3000/song/$title";

        Http::get($url)->body();
        info("Job In Progress: $url");
        return [
            'status' =>  'Job In Progress'
        ];
    }

    public function classifySongs(): array
    {
        $songs = Song::all();
        $unClassified = [];
        /** @var Song $song */
        foreach ($songs as $song) {

            $song->save();
            if ($song->analyzed == null ) {
                $song->status = 'queued';

                $api_url = env('APP_URL') . '/api/songs/match/';
                $song->related_songs = $api_url . $song->title;
                $unClassified[] = $song->title;
            }
        }

        foreach ($unClassified as $title) {
            ClassifySongJob::dispatch($title);
            info("$title : has been queued");
        }
        return $unClassified;
    }
}
