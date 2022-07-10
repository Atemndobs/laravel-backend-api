<?php

namespace App\Services\Birdy;

use App\Models\Song;
use MeiliSearch\Client;
use MeiliSearch\Endpoints\Indexes;

class MeiliSearchService
{
    public Client $meiliSearch;

    public function __construct()
    {
        $url = env('MEILISEARCH_HOST');
        $this->meiliSearch = new Client($url);
    }

    public function seatchSong(string $searchTerm)
    {
        $songIndex = $this->meiliSearch->index('songs')->resetSettings();
        $songs = Song::search($searchTerm)->paginate(10);
        $matches = Song::search($searchTerm)->get();

        return $matches;
    }

    public function setSearchSettings(Indexes $songIndex): Indexes
    {
        $songIndex->updateFilterableAttributes([
            'bpm',
            'key',
            'scale',
            'energy',
            'happy',
            'sad',
            'aggressiveness',
            'danceability',
            'relaxed',
            'analyzed',
            'slug',
        ]);
        $songIndex->updateSortableAttributes([
            'bpm',
            'key',
            'scale',
            'energy',
            'happy',
            'sad',
            'aggressiveness',
            'danceability',
            'relaxed',
            'slug',
        ]);
        $songIndex->updateDisplayedAttributes([
            'title',
            'bpm',
            'key',
            'scale',
            'energy',
            'happy',
            'sad',
            'aggressiveness',
            'danceability',
            'relaxed',
            'played',
            'path',
            'slug',
            'image',
            'related_songs',
        ]);

        $songIndex->updateRankingRules([
            'bpm',
            'key',
            'scale',
            'danceability',
            'energy',
            'happy:asc',
            'sad:dsc',
            'relaxed',
        ]);

        return $songIndex;
    }

    public function searchByAttribute(string $attribute)
    {
        $songIndex = $this->meiliSearch->index('songs');
        $this->setSearchSettings($songIndex);

        $res = $songIndex->search('', [
            'filter' => "$attribute > 70",
            'sort' => ['bpm:asc'],
        ]);

        return $res->getHits();
    }

    /**
     * @return Indexes
     */
    public function getSongIndex(): Indexes
    {
        $songIndex = $this->meiliSearch->index('songs');
        $this->setSearchSettings($songIndex);

        return $songIndex;
    }
}
