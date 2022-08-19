<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use function Psy\debug;

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

        /**
         * @var Song $existingSong
         */
        if ($existingSong->analyzed) {
            dump([
                'analyzed' => $existingSong->analyzed,
                'Existing' => $existingSong->status,
            ]);
            // index the existing song
            $existingSong->searchable();

            return [
                'status' => $existingSong->status,
            ];
        }

        // $url = "http://localhost:3000/song/$slug";
        $url = "http://host.docker.internal:3000/song/$slug";
        $notAnalyzedSongs = Song::where('analyzed', '=', null)->count();
        ray("$notAnalyzedSongs Songs Pending Analysis ")->screenBlue();

        $req = Http::get($url);

        if ($req->json('status') == 'error') {
            dump([
                'status' => 'error',
                'message' => $req->json(),
                'url' => $url,
            ]);
            return [
                'status' => 'error',
                'message' => $req->json(),
            ];
        }
        info("Job in progress for $slug");
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
            if ($song->analyzed == 0 && $song->duration >= 600) {
                $song->status = 'skipped';
                $song->analyzed = false;
                $song->save();
                $skipped[] = [
                    'song' => $song->slug,
                    'duration' => $song->duration,
                    'status' => $song->status,
                    'analyzed' => $song->analyzed,
                ];
            }elseif($song->analyzed == null) {
                $song->status = 'queued';
                $song->save();
                $slug = $song->slug;
                $unClassified[] = $slug;
 //               ClassifySongJob::dispatch($slug);
//                info("$slug : has been queued");
            }
        }

        return $unClassified;
    }
}
