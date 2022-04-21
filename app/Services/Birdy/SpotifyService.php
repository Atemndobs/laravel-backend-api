<?php

namespace App\Services\Birdy;

use App\Models\Song;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;


class SpotifyService
{
    public Song $song;
    public SpotifyWebAPI $spotify;

    public function __construct()
    {
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

    public function getArtistGenre(string $artist)
    {

        //$existingSong = Song::where('title', '=', $title)->first();

        $spotifyTrack = $this->spotify->search($artist, 'track')->tracks->items[0];
        $id = $spotifyTrack->id;

        $track = $this->spotify->getTrack($id);
        $artists = $track->artists;

        $genres = [];

        if (count($artists) > 1) {
            $genres = $this->getBestMatch($artist, $genres);
        }

        return $genres;
    }

    public function getGenreByArtist(string $author)
    {
        $genres = [];
        $artists = $this->spotify->search($author, 'artist')->artists->items;
        foreach ($artists as $artist) {
            if (count($artist->genres) === 0) {
                continue;
            }
            $genres[] = [
                'name' => $artist->name,
                'genres' => $artist->genres
            ];
        }

        if (count($genres) === 1 && strtoupper($genres[0]['name'] )=== strtoupper($author)) {
            return  $genres[0]['genres'];
        }


        if (count($genres) >= 1) {
            foreach ($genres as $genre) {
                if (strtoupper($genre['name']) === strtoupper($author)) {
                    return $genre['genres'];
                }
                similar_text(strtoupper($author), strtoupper($genre['name']), $perc);

                if ($perc === 100.0) {
                    return $genre['genres'];
                }

                if ($perc >= 80.0 ){
                    return $genre['genres'];
                }
            }
        }






        if (count($genres) === 0){
            return $genres;
        }

//          dump($author);
//          dump($genres);
//          dump($artists);
//        dd('ENDE');
      //  $genres = $this->getBestMatch($author, $genres);
        return [];
    }

    /**
     * @param $author
     * @param array $genres
     * @return array
     */
    public function getBestMatch($author, array $genres): array
    {
        $matchingGenres = [];
        foreach ($genres as $genre) {
            if ($author === $genre) {

            }
        }

        return $matchingGenres;
    }

}
