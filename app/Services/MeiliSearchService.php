<?php

namespace App\Services;

use Meilisearch\Client;

class MeiliSearchService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(config('services.meili.host'), config('services.meili.key'));
    }
    
    // public static function create()
    // {
    //     $client = new Client(config('services.meili.host'), config('services.meili.key'));
    //     return new MeiliSearchService($client);
    // }

    public function getIndex(string $indexName)
    {
        return $this->client->index($indexName);
    }
}