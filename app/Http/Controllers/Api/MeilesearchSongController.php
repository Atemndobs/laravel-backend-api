<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use MeiliSearch\Client;

class MeilesearchSongController extends Controller
{
/**
     * @var Client
     */
    public function getSongs()
    {

        // http://mage.tech:7700/indexes/songs/search?offset=0&limit=600
        $songs = (new Client(env('MEILISEARCH_HOST')))->getIndex('songs');

        $searchQuery = [
            'query' => [
                'match' => [
                    'title' => 'the beat',
                ],
            ],
        ];
        $songs->updateSettings([
            'filterableAttributes' => [
                'status',
            ],
        ]);
        $songs->search('', [
            'filter' => "status != 'deleted'",
            'sort' => ['bpm:asc'],
        ]);
        $query = [
            'offset' => 0,
            'limit' => 100,
        ];
        return $songs->getDocuments($query);
    }

    public function ping()
    {
        $request = request()->all();
        info(json_encode($request));

        try {
        $status = $request['status'];
        if ($status == 'deleted') {
            return response()->json([
                'status' => 'delete notified',
            ]);
        }
        } catch (\Exception $e) {
            throw new \Exception('Process Deleted');
        }
        return response()->json([
            'status' => 'success',
        ]);
    }
}
