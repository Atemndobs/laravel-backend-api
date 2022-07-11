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

        if (! $existingSong) {
            return [
                'status' => "$slug does not exist",
            ];
        }
        if ($existingSong->analyzed) {
            dump([
                'analyzed' => $existingSong->analyzed,
                'Existing' => $existingSong->status,
            ]);

            return [
                'status' => $existingSong->status,
            ];
        }

        // $url = "http://localhost:3000/song/$slug";
        $url = "http://host.docker.internal:3000/song/$slug";

        Http::get($url)->body();
        info("Job In Progress: $url");
        // total of all songs not yet analyzed
        $notAnalyzedSongs = Song::where('analyzed', '=', null)->count();
        ray("$notAnalyzedSongs Songs Pending Analysis ")->screenBlue();

        return [
            'status' => 'Job In Progress',
        ];
    }

    public function classifySongs(): array
    {
        $songs = Song::all();
        $unClassified = [];
        $skipped = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            // $song->save();
            if ($song->analyzed === null && $song->duration < 600 && $song->duration !== null) {
                $song->status = 'queued';
                $unClassified[] = $song->slug;
            }

            if ($song->analyzed === null || $song->duration >= 600) {
                $song->status = 'skipped';
                $skipped[] = [
                    'song' => $song->slug,
                    'duration' => $song->duration,
                    'status' => $song->status,
                ];
            }
        }

        foreach ($unClassified as $slug) {
            ClassifySongJob::dispatch($slug);
            info("$slug : has been queued");
        }
        dump($skipped);

        return $unClassified;
    }
}
