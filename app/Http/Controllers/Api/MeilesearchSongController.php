<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use MeiliSearch\Client;
use MeiliSearch\Contracts\DocumentsQuery;
use function example\int;

class MeilesearchSongController extends Controller
{
    public \MeiliSearch\Client $client;

    public function __construct()
    {
        $this->client = new Client(env('MEILISEARCH_HOST'), env('MEILI_MASTER_KEY'));
    }

    public function getSongs()
    {
        $offset = request()->offset ?? 0;
        $limit = request()->limit ?? 10;

        $query =
            [
                'filter' => [
                ['status != deleted'],
                'analyzed = 1'
            ],
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            ];
/*        $songs = $this->client->getIndex('songs')
            ->getDocuments(
                (new DocumentsQuery())->setOffset($offset)->setLimit($limit)
            )->toArray();
        $total = $songs['total'];
        $last = $total / $limit;
        // replace 'results' with 'data'
        $songs['data'] = $songs['results'];
        $songs['last'] = $last;
        unset($songs['results']);*/

        $search = $this->client->getIndex('songs')
            ->search('', $query)
            ->toArray();

        $search['data'] = $search['hits'];
        unset($search['hits']);
        $search['total'] = $search['estimatedTotalHits'];
        unset($search['estimatedTotalHits']);
        unset($search['processingTimeMs']);
        unset($search['query']);
        unset($search['facetDistribution']);
        unset($search['hitsCount']);
        $search['offset'] = (int)$offset;
        $search['limit'] = (int)$limit;
        $searchTotal = $search['total'];
        $searchLast = $searchTotal / $limit;
        $search['last'] = (int)$searchLast;

        return response()->json($search);
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