<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        // Initialize the Elasticsearch client
        $this->client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->build();
    }

    public function index(string $index, array $data)
    {
        $params = [
            'index' => $index,
            'body'  => $data
        ];

        return $this->client->index($params);
    }

    public function search(string $index, array $query)
    {
        $params = [
            'index' => $index,
            'body'  => [
                'query' => $query
            ]
        ];

        return $this->client->search($params);
    }

    public function createIndex(string $indexName, array $settings = [], array $mappings = []): Elasticsearch|Promise
    {
        $body = [];

        if (!empty($settings)) {
            $body['settings'] = $settings;
        }

        if (!empty($mappings)) {
            $body['mappings'] = $mappings;
        }

        $params = [
            'index' => $indexName,
            'body'  => $body
        ];

        return $this->client->indices()->create($params);
    }


}
