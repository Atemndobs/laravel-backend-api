<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\MoodAnalysisService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ClassifySongJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected string $track;

    /**
     * @param string $track
     */
    public function __construct(string $track)
    {
        $this->track = $track;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $title = $this->track;
        (new MoodAnalysisService())->getAnalysis($title);
/*        $existingSong = Song::where('title', '=', $title)->first();

        if ($existingSong->bpm != null && $existingSong->happy != null) {
            return [
                'status' =>  'Already Analysed'
            ];
        }

        $url = "http://localhost:3000/song/$title";
        $response = Http::get($url)->body();
        return [
            'status' =>  'Job In Progress'
        ];*/

    }
}
