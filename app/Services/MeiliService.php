<?php

namespace App\Services;

use Meilisearch\Client;

class MeiliSearchService
{
    private Client $client;

    private function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    public static function create()
    {
        $client = new Client(config('services.meili.host'), config('services.meili.key'));
        return new MeiliSearchService($client);
    }

    public function getIndex(string $indexName)
    {
        return $this->client->index($indexName);
    }
}