<?php

namespace App\Services\Scraper;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BandcampService
{
    use Tools;
    public string $baseUrl = "https://bandcamp.com";

    public function getSongLinksByArtisName(string $artist)
    {
        $url = "$this->baseUrl/search?q=$artist&item_type";
        $songLinks = $this->getSongLinks($url);

        $searchQuery = Str::slug($artist);
        $artistSearchQuery = str_replace('-', '', $searchQuery);
        $artistSubDomain = $artistSearchQuery . '.bandcamp.com';
        dump($artistSubDomain);

        dump(['searchQuery' => $searchQuery]);
        $links = [];
        foreach ($songLinks as $songLink) {
            if (str_contains($songLink, $artistSubDomain)) {
                $links[] = $songLink;
            }
        }

        if (count($links) > 0) {
            foreach ($links as $link) {
                $collectedSongLinks = $this->getSongLinks($link);
            }
        }

        $trackLinks = [];
        foreach ($collectedSongLinks as $foundLink) {
            if(str_contains($foundLink, '/track')) {
                $trackLinks[] = "https://" . $artistSubDomain . $foundLink;
            }
        }
        return $trackLinks;
    }

    public function downloadSong(string $songLink)
    {
        $fileName = explode('/', $songLink);
        $fileName = Arr::last($fileName);
       // $dnload = shell_exec("bandcamp-dl $songLink --base-dir=public/music --file-name=$fileName");
        $dnload = shell_exec("bandcamp-dl $songLink --base-dir=. --file-name=$fileName -d");

        dump($dnload);
        return $fileName;
    }
}
