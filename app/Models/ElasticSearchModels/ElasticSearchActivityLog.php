<?php

namespace App\Models\ElasticSearchModels;


use App\Services\ElasticsearchService;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchActivityLog
{

    public function __construct()
    {
        $hosts = [
            env('ELASTICSEARCH_HOST', 'localhost:9200')  // Change this to your Elasticsearch host details
        ];
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    public string $indexName = 'activity_logs';

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
                'type' => 'keyword'
            ],
            'model_id' => [
                'type' => 'integer'
            ],
            'properties' => [
                'type' => 'object',
                'properties' => [
                    'key1' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'key2' => [
                        'type' => 'long'
                    ],
                    'key3' => [
                        'type' => 'boolean'
                    ],
                    'key4' => [
                        'properties' => [
                            'sub_key1' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword',
                                        'ignore_above' => 256
                                    ]
                                ]
                            ],
                            'sub_key2' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss'
                            ]
                        ]
                    ]
                ]
            ],
            'created_at' => [
                'type' => 'date',
                'format' => 'strict_date_optional_time||epoch_millis'
            ],
            'updated_at' => [
                'type' => 'date',
                'format' => 'strict_date_optional_time||epoch_millis'
            ]
        ]
    ];




    public function createIndex()
    {
        $client = new ElasticsearchService();
        return $client->createIndex(indexName: $this->indexName, settings: $this->settings, mappings: $this->mappings);
    }

    public function deleteIndex(){
        $client = new ElasticsearchService();
        $client->deleteIndex($this->indexName);
    }

   public function deleteAndRecreateIndex(){
        $this->deleteIndex();
        $this->createIndex();
       $client = new ElasticsearchService();
       return $client->getMapping($this->indexName);

   }

    public function reindexWithNewMapping()
    {
        $newIndexName = 'activity_logs_v2';

        // Create a new index with the new mapping
        $params = [
            'index' => $newIndexName,
            'body' => [
                'mappings' => $this->mappings
            ]
        ];
        $this->client->indices()->create($params);

        // Reindex data from the old index to the new one
        $params = [
            'body' => [
                'source' => [
                    'index' => 'activity_logs'
                ],
                'dest' => [
                    'index' => $newIndexName
                ]
            ]
        ];
        $response = $this->client->reindex($params);
        dd($response->asArray());

    }
}

