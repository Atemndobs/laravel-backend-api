<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\ClassifyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClassificationController extends Controller
{
    public Request $request;
    protected ClassifyService $classifyService;
    protected Song $song;

    /**
     * @param ClassifyService $classifyService
     * @param Song $song
     */
    public function __construct(
        ClassifyService $classifyService,
        Song            $song,
        Request         $request
    )
    {
        $this->classifyService = $classifyService;
        $this->song = $song;
        $this->request = $request;
    }


    public function classify()
    {
        $track = $this->request->track;
        return $this->classifyService->analyzeTrack($track);
    }

    public function analyze()
    {
        return 'HERE';
    }

    public function findByTitle(Request $request) : Response
    {
        $title = $request->title;
        $song = Song::where('title', '=', $title)->first();

        if ($song) {
            $msg = [
                'id' => $song->id,
                'path'=> $song->path
            ];
            return \response($msg, 200);
        }

        $msg = [
            'SONG_NOT_FOUND' => "$title does not exist , Please upload and try again"
        ];
        return \response($msg, 404);
    }
}
