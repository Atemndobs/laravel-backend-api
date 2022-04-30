<?php

namespace App\Services\Scraper;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;


class MusicBlogScraper
{
    public Client $client;

    /**
     */
    public function __construct()
    {
        $this->client = new Client();

    }


    public function searchSongsByArtist(string $artis)
    {

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

        $res = $this->client->request('GET', $downloadLink);

        $songLinks = $res->filter('a')->each(function ($node){
            return $node->attr('href') . '';
        });

        $songLinks = array_unique($songLinks);

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

    public function getSongsFromHiphopkit()
    {
        $downloadLink = 'https://hiphopkit.com/music/artiste/drake/';

        $res = $this->client->request('GET', $downloadLink);

        $songLinks = $res->filter('a')->each(function ($node){
            dump($node);
            return $node->attr('href') . '';
        });

        $songLinks = array_unique($songLinks);

        dd($songLinks);
    }

}
