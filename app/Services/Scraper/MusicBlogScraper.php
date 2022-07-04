<?php

namespace App\Services\Scraper;

use Goutte\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use function example\int;

// factor out source websites to separate class

class MusicBlogScraper
{
    public Client $client;

    /**
     */
    public function __construct()
    {
        $this->client = new Client();

    }

    /**
     * @param Crawler $crawler
     * @return string|null
     */
    public function getNotJustOKAudio(Crawler $crawler): ?string
    {
        return $crawler->filterXPath('//*[@id="main-content-article"]/div/main/div[1]/article/figure/audio')
            ->attr('src');
    }

    /**
     * @param string $link
     * @return array
     */
    protected function getAudio(string $link): array
    {
        $crawler = $this->client->request('GET', $link);
        return $crawler->filter('audio')->each(function ($node){
            return $node->text();
        });
    }

    /**
     * Source = Website e.g Fakaza, Tooxclusive
     * @param string $baseUrl
     * @param string $downloadPage
     * @return array
     */
    public function getMusicFromSource(string $baseUrl = 'https://fakaza.com/', string $downloadPage ='download-mp3' ): array
    {
        $downloadLink = $baseUrl . $downloadPage;

        $songLinks = $this->getSongLinks($downloadLink);

        $collectedSongs = [];
        foreach ($songLinks as $key => $songLink) {
            if ($songLink === $baseUrl
                || $songLink === $downloadLink
                || !str_contains($songLink, $baseUrl)
                || str_contains($songLink, 'news')
                || str_contains($songLink, 'contact')
                || str_contains($songLink, 'editorial')
                || str_contains($songLink, 'disclaimer')
                || str_contains($songLink, 'artists-z-music-list')
            ){
                unset($songLinks[$key]);
            }

        }

        foreach ($songLinks as $songLink) {
            if (count($this->getAudio($songLink)) > 0) {
                $assetLink = $this->getAudio($songLink)[0];
                $ext = substr($assetLink, -3);
                if ($ext !== 'mp3') {
                    continue;
                }
                $split = explode('/', $assetLink);
                $size = count($split);
                $title = '';
                foreach ($split as $i => $part){
                    if ($i === $size - 1) {
                        $title = $part;
                    }
                }

                $path = "storage/app/public/audio/$title";
                $this->download($path, $assetLink);
                $collectedSongs[] = $assetLink;
            }

        }
        $collectedSongs = array_unique($collectedSongs);

        return $collectedSongs;
    }

    /**
     * @param $outFileName
     * @param $url
     * @return mixed|void
     */
    public function download($outFileName, $url)
    {
        if(is_file($url)) {
            copy($url, $outFileName);
        } else {
            $options = array(
                CURLOPT_FILE    => fopen($outFileName, 'w'),
                CURLOPT_TIMEOUT =>  28800, // set this to 8 hours so we dont timeout on big files
                CURLOPT_URL     => $url
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $httpcode;
        }
    }

    public function getSongsFromHiphopkit(string $artist)
    {
        $artist = Str::slug($artist, '-');
        $baseUrl = "https://hiphopkit.com/";
        $downloadLink = $baseUrl . "music/artiste/" .  $artist;
        $res = $this->client->request('GET', $downloadLink);

        $songLinks = $res->filter('a')->each(function ($node){
            return $node->attr('href') . '';
        });

        ray()->clearAll();


        $songLinks = array_unique($songLinks);
        $collectedSongs = [];
        foreach ($songLinks as $key => $songLink) {
            if (

                $songLink == $baseUrl
             //   || $songLink == $downloadLink
              //  || !str_contains($songLink, "$baseUrl$artist")
            ){
                unset($songLinks[$key]);
            }
        }

        ray([
            'base_url' => $baseUrl,
            'download_link' => $downloadLink,
            'Artist' => $artist,
            'links' => $songLinks
        ]);
        foreach ($songLinks as $songLink){
            $link = $this->client->request('GET', $songLink);
            $artistLinks = $link->filter('a')->each(function ($node){
                $link = $node->attr('href') . '' ;
                return str_contains($link, 'music/download') ? $link : null;
            });
            $dnloadMusic = array_unique($artistLinks);

            foreach ($dnloadMusic as $music){
                if ($music !== null) {
                    ray($music)->screenGreen();
                    $songId = substr($songLink, -4);
                    $title = str_replace(array('https://hiphopkit.com/', $songId), '', $songLink);
                    $title = Str::slug($title, '_');
                    $path = "storage/app/public/audio/$title.mp3";
                    $exist = Storage::exists("public/audio/$title.mp3");

                    ray($path)->red();
                    if (!$exist){
                          $this->download($path, $music);
                         file_put_contents($path, fopen($music, 'r'));
                        $collectedSongs[] = asset("storage/audio/$title.mp3");
                    }

                    ray($collectedSongs)->screenGreen();

                    if (count($collectedSongs) === 5){
                        break;
                    }
                }
            }
        }
        return $collectedSongs;
    }

    /**
     * @param array $searchTerms
     * @return array
     */
    public function getSongsFromZaplaylist(array $searchTerms) : array
    {
       //https://zaplaylist.com/?s=Big+Flexa

        // if search terms are empty, return empty array
        if (empty($searchTerms)) {
            return [];
        }

        // if search terms are not empty, build search url
        // search = artis, title, mixtape
        // initialize artist , title and mixtape variables
        $artist = $title = null;
        $searchOptions = [];
        $searchUrl = 'https://zaplaylist.com/?';
        foreach ($searchTerms as $search => $term) {
            $term = Str::slug($term, '+');
            $artist = $search === 'artist' ? $searchUrl . "s=$term" : null;
            if ($artist !== null) {
                $searchOptions['artist'] = $artist;
            }
            $title = $search === 'title' ? $searchUrl . "s=$term" : null;
            if ($title !== null) {
                $searchOptions['title'] = $title;
            }
            $mixtape = $search === 'mixtape' ? $searchUrl . "s=$term" : null;
            if ($mixtape !== null) {
                $searchOptions['mixtape'] = $mixtape;
            }
        }

        if (count($searchOptions) === 1) {
            // if only title search option is set, get song links
            if (array_key_exists('title', $searchOptions)) {
                $songLinks = $this->getSongLinks($searchOptions['title']);
            } else {
                // if only artist search option is set, get artist links
                $artistLinks = $this->getArtistLinksFromZaplaylist($searchOptions['artist']);
            }
            $songLinks = $this->getSongLinks($searchOptions['artist']);
            $songLinks = $this->filterDownloadUrls($songLinks);
            // if empty, return empty array
            if (empty($songLinks)) {
                return [];
            }
            dump("found " . count($songLinks) . " songs");
            dd();
            $res  = [];
            foreach ($songLinks as $songLink) {
                dump("downloading $songLink");
                $downloadLink = $this->downloadZaplaylist($songLink);
                $res[] = $downloadLink;
            }
            dump($res);
            return $res;
        }
        else {
            ray()->clearAll();
            dump('CONTAINS MORE THAN ONE SEARCH OPTION');
            // first get song links from title search option
            $songLinks = $this->getSongLinks($searchOptions['title']);
            $songLinks = $this->filterDownloadUrls($songLinks);
            // if $songLinks is empty, get song links from artist search option
            if (empty($songLinks)) {
                $songLinks = $this->getArtistLinksFromZaplaylist($searchOptions['artist']);
            }
            // if $songLinks has more than 5 songs, filter results by title
            if (count($songLinks) > 1) {
                $songLinks = $this->filterSongLinksByTitle($songLinks, $searchOptions['title']);
            }

            // download song from $songLinks
            $songFoundUrl = Arr::first($songLinks);
            $this->downloadZaplaylist($songFoundUrl);
        }
        return $songLinks;
    }

    /**
     * @param string $uri
     * @return array
     */
    public function getSongLinks(string $uri): array
    {
        $res = $this->client->request('GET', $uri);
        $songLinks = $res->filter('a')->each(function ($node) {
            return $node->attr('href') . '';
        });

        return array_unique($songLinks);
    }

    private function getArtistLinksFromZaplaylist(string $artist)
    {
        $songLinks = $this->getSongLinks($artist);
        // filter download links
        $songLinks = array_filter($songLinks, function ($link) {
            return str_contains($link, 'music/download');
        });

        return $songLinks;
    }

    private function filterSongLinksByTitle(array $songLinks, string $title)
    {
        // replace + with - in title
        $title = str_replace('+', '-', $title);
        $title = Str::slug($title, '-');
        return array_filter($songLinks, function ($link) use ($title) {
            return str_contains($link, $title);
        });
    }

    /**
     * @param array $songLinks
     * @param string $filter
     * @return array
     */
    public function filterDownloadUrls(array $songLinks, string $filter = 'mp3/'): array
    {
        // filter links containing $title and ends with mp3
        return array_filter($songLinks, function ($link) use ($filter) {
            return str_ends_with($link, $filter);
        });
    }

    /**
     * @param mixed $songFoundUrl
     * @return void
     */
    public function downloadZaplaylist(mixed $songFoundUrl): void
    {
        $res = $this->client->request('GET', $songFoundUrl);
        $downloadUrls = $res->filter('a')->each(function ($node) {
            return $node->attr('href') . '';
        });
        $downloadUrls = array_unique($downloadUrls);
        $downloadUrls = $this->filterDownloadUrls($downloadUrls, '.mp3');
        $downloadUrl = Arr::first($downloadUrls);
        $fileName = explode('/', $downloadUrl);
        $fileName = Arr::last($fileName);
        $this->download($fileName, $downloadUrl);
    }
}
