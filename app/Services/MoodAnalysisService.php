<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Facades\Http;

class MoodAnalysisService
{
    public function getAnalysis($title)
    {
        $existingSong = Song::where('title', '=', $title)->first();

        if ($existingSong->analyzed) {
            return [
                'status' =>  $existingSong->status
            ];
        }

        $url = "http://localhost:3000/song/$title";
        $response = Http::get($url)->body();
        return [
            'status' =>  'Job In Progress'
        ];
    }

    public function classifySongs()
    {
        $songs = Song::all();
        $unClassified = [];
        $data = [];
        /** @var Song $song */
        foreach ($songs as $song) {

            $song->save();
            if ($song->analyzed == null ) {
                $song->status = 'qeued';

                $api_url = env('APP_URL') . '/api/songs/match/';
                $song->related_songs = $api_url . $song->title;
                $unClassified[] = $song->title;
                $data[] = [
                    'title' => $song->title,
                    'status' => 'pending'
                ];
            }
        }

        foreach ($unClassified as $title) {
            ClassifySongJob::dispatch($title);
            info("$title : has been qeued");
        }
        return $unClassified;
    }
}
