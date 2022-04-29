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
           //     || strlen($songLink) < (strlen($downloadLink) + 4)
              || str_contains($songLink, 'news')
                || str_contains($songLink, 'contact')
                || str_contains($songLink, 'editorial')
             //   || str_contains($songLink, 'feed')
              //  || str_contains($songLink, 'video')
              //  || str_contains($songLink, 'main/')
             //   || str_contains($songLink, 'tag/')
             //   || str_contains($songLink, 'sitemap')
            //    || str_contains($songLink, 'lyrics')
              //  || str_contains($songLink, 'about')
                || str_contains($songLink, 'disclaimer')
              //  || str_contains($songLink, 'contact-us')
                || str_contains($songLink, 'artists-z-music-list')
             //   || str_contains($songLink, 'videos')
            //    || str_contains($songLink, 'artists-a-z')
               // || str_contains($songLink, '#comments')
            ){
                unset($songLinks[$key]);
            }

        }


        foreach ($songLinks as $songLink) {
            if (count($this->getAudio($songLink)) > 0) {
                $collectedSongs[] = $this->getAudio($songLink)[0];
            }

        }

        $collectedSongs = array_unique($collectedSongs);

        return $collectedSongs;
    }

}
