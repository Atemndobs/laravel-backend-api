<?php

namespace App\Services\Scraper;

use Illuminate\Support\Str;

class BandcampService
{
    use Tools;
    public string $baseUrl = "https://bandcamp.com";

    public function getArtistByName(string $artist)
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

        foreach ($collectedSongLinks as $foundLink) {
            if(str_contains($foundLink, '/track')) {
                $trackLinks[] = "https://" . $artistSubDomain . $foundLink;
            }
        }

        dd($trackLinks);
        dd($collectedSongLinks);
       dd($songLinks);

    }
}
