<?php

namespace App\Console\Commands;

use App\Services\Birdy\MeiliSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use MeiliSearch\Client;

class SearchIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize / Index Meilsearch Entities  : Used for resetting Search Index';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find out and Fix 405 Error with Meileiseach
        $url = env('MEILISEARCH_HOST');
        $client = new Client($url);
        return 0;
    }
}
