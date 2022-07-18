<?php

namespace App\Services\Birdy;

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

    /**
     * @param Indexes $songIndex
     * @return Indexes
     */
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
