<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Facades\Http;

class MoodAnalysisService
{
    public function getAnalysis(string $slug): array
    {
        $existingSong = Song::where('slug', '=', $slug)->first();

        if (!$existingSong) {
            $res =  [
                'status' =>  "$slug does not exist"
            ];

            dump($res);
            return $res;
        }
        if ($existingSong->analyzed) {
            return [
                'status' =>  $existingSong->status
            ];
        }

        $url = "http://localhost:3000/song/$slug";

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
            if ($song->analyzed === null ) {
                $song->status = 'queued';
                $unClassified[] = $song->slug;
            }
        }

        foreach ($unClassified as $slug) {
            ClassifySongJob::dispatch($slug);
            info("$slug : has been queued");
        }
        return $unClassified;
    }
}
