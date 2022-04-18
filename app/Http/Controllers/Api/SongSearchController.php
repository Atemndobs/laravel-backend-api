<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Birdy\MeiliSearchService;

use Illuminate\Http\Request;

class SongSearchController extends Controller
{
    public Request $request;

    public Song $song;

    public MeiliSearchService $meiliSearchService;

    /**
     * @param Request $request
     * @param Song $song
     * @param MeiliSearchService $meiliSearchService
     */
    public function __construct(Request $request, Song $song, MeiliSearchService $meiliSearchService)
    {
        $this->request = $request;
        $this->song = $song;
        $this->meiliSearchService = $meiliSearchService;
    }


    public function searchSong()
    {
        $response = $this->meiliSearchService->seatchSong($this->request->term);
        return response($response);
    }
}
