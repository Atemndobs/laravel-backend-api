<?php

namespace App\Services\Birdy;

use App\Models\Song;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;


class SpotifyService
{
    public Song $song;
    public SpotifyWebAPI $spotify;

    /**
     * @param Song $song
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
        $client_id = env('SPOTIFY_CLIENT_ID');
        $client_secret = env('SPOTIFY_CLIENT_SECRET');
        $url = 'http://dejavu.atmkng.de/';
        $session = new Session(
            'c60869065e4c4a298aaf489700602182',
            '5548d231e4f74c07964d5d675a587c44',
            $url
        );
        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();
        $this->spotify = new SpotifyWebAPI();
        $this->spotify->setAccessToken($accessToken);
    }

    public function searchSong()
    {
      //  $existingSong = Song::where('title', '=', $title)->first();
        $spotifyTracks = null;
        $songTitle = $this->song->title;

        $spotifyTrack = $this->spotify->search('BurnaBoy Gbona', 'track')->tracks->items[0];

        $id = $spotifyTrack->id;

        $track = $this->spotify->getTrack($id);
        $artistId = $track->artists[0]->id;
        $artist = $this->spotify->getArtist($artistId);
        $genres = $artist->genres;

        return $genres;
    }

}
