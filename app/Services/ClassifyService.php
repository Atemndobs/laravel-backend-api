<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Goutte\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class ClassifyService
{
    public Client $client;
    protected Song $song;
    protected array $songs;

    /**
     * @param Client $client
     * @param Goutte $goutte
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return void
     */
    public function getClaissification()
    {
    }

    /**
     * @param string $track
     * @return array|string[]
     */
    public function analyzeTrack(string $track)
    {
        if ($this->checkSong($track)){
            return [
                'status' =>  $this->song->status,
                'key' => $this->song->key,
                'bpm' => $this->song->bpm,
                'scale' => $this->song->scale,
                'danceability' => $this->song->danceability,
                'happy' => $this->song->happy,
                'sad' => $this->song->sad,
                'analyzed' => $this->song->analyzed,
            ];
        }
        ClassifySongJob::dispatch($track);
       // (new MoodAnalysisService())->getAnalysis($track);
        return [
            'status' => 'qeued',
            'track' => $track
        ];

    }

    /**
     * @param string $title
     * @return bool
     */
    public function checkSong(string $title) : bool
    {
        $existingSong = $this->checExistingSong($title);
        if ($existingSong->key != null && $existingSong->bpm != null) {
            $this->song = $existingSong;
            return true ;
        }
        return false;
    }

    /**
     * @param string $title
     * @return array|Song
     */
    public function checExistingSong(string $title) : array | Song
    {
        $existingSong = Song::where('title', '=', $title)->first();
        if ($existingSong == null){
            throw new ModelNotFoundException("$title does not exist , Please upload and try again");
        }
        return $existingSong;
    }

}
