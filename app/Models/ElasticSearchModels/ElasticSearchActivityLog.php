<?php

namespace App\Models\ElasticSearchModels;


use App\Services\ElasticsearchService;

class ElasticSearchActivityLog
{
    public string $indexName = 'activity_logszzz';

    public array $settings = [
        'number_of_shards' => 3,
        'number_of_replicas' => 1
    ];

    public array $mappings = [
        'properties' => [
            'performed_by_user_id' => [
                'type' => 'integer'
            ],
            'activity_type_id' => [
                'type' => 'integer'
            ],
            'model_type' => [
                'type' => 'keyword' //https://www.elastic.co/guide/en/elasticsearch/reference/current/keyword.html
            ],
            'model_id' => [
                'type' => 'integer'
            ],
            'properties' => [
                'type' => 'object' // https://www.elastic.co/guide/en/elasticsearch/reference/current/object.html
            ]
        ]
    ];


    public function createIndex()
    {
        $client = new ElasticsearchService();
        return $client->createIndex(indexName: $this->indexName, settings: $this->settings, mappings: $this->mappings);
    }
}

