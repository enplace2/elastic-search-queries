<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticsearchService
{
    public $client;

    public function __construct()
    {
        // Initialize the Elasticsearch client
        $this->client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->build();
    }

    public function handleElasticSearchException(ElasticsearchException $e){
        $errorMessage = "Elasticsearch Server Response Exception:\n";
        $errorMessage .= "Message: " . $e->getMessage() . "\n";
        $errorMessage .= "Trace: " . $e->getTraceAsString();

        \Log::error($errorMessage);
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

    public function createIndex(string $indexName, array $settings = [], array $mappings = [])
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

        $response = $this->client->indices()->create($params);
        $body = $response->getBody();
        return json_decode($body, true);
    }

    public function getMapping($indexName)
    {
        $params = ['index' => $indexName];
        $response = $this->client->indices()->getMapping($params)->asArray();
        return $response;
    }


    public function createDocument(string $indexName, array $data, $documentId = null)
    {
        $params = [
            'index' => $indexName,
            'body'  => [
                'properties'=>$data
            ],
            'op_type' => 'create' // This ensures the operation fails if the document ID already exists
        ];

        if ($documentId) {
            $params['id'] = $documentId;
        }

        $response = $this->client->index($params);
        $body = $response->getBody();
        return json_decode($body, true);
    }

    public function updateMapping(string $indexName, array $newFields)
    {
        $params = [
            'index' => $indexName,
            'body' => $newFields
        ];

        return $this->client->indices()->putMapping($params);
    }


    public function getAllDocuments(string $indexName)
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ],
                //'size' => 100  // Gets the first 100 docs. Don't use this function if your dataset is large.
            ]
        ];

        return $this->client->search($params);
    }

    public function getDocumentCount(string $indexName)
    {
        $params = ['index' => $indexName];
        $response = $this->client->count($params)->asArray();
        return $response;
    }

    public function deleteIndex($indexName){
        $params = ['index' => $indexName];

        $response = $this->client->indices()->delete($params);
        return $response;
    }

    public function getRandomDocumentFromActivityLogs($indexName)
    {
        $params = [
            'index' => $indexName,
            'body'  => [
                'query' => [
                    'function_score' => [
                        'query' => [
                            'match_all' => new \stdClass()  // Match all documents
                        ],
                        'functions' => [
                            [
                                'random_score' => new \stdClass()  // Randomly score each document
                            ]
                        ]
                    ]
                ],
                'size' => 1  // Return only one document
            ]
        ];

        $response = $this->client->search($params);
        return $response->asArray();
    }


    public function getDocumentById($id, $index)
    {
        $params = [
            'index' => $index,
            'body'  => [
                'query' => [
                    'term' => [
                        'id' => $id
                    ]
                ]
            ]
        ];

        $response =  $this->client->search($params);
        return $response->asArray();
    }
    public function getDocumentByUserId($id, $index, $size)
    {
        $params = [
            'index' => $index,
            'size'  =>$size,
            'body'  => [
                'query' => [
                    'term' => [
                        'performed_by_user_id' => $id
                    ]
                ]
            ]
        ];

        $response =  $this->client->search($params);
        return $response->asArray();
    }

    public function getByModelType($modelType, $size = 500)
    {
        $params = [
            'index' => 'activity_logs',
            'size' => $size,
            'body'  => [
                'query' => [
                    'term' => [
                        'model_type' => $modelType
                    ]
                ]
            ]
        ];

        return $this->client->search($params)->asArray();
    }

    public function getByActivityTypeId($id, $size)
    {
        $params = [
            'index' => 'activity_logs',
            'size'  => $size,
            'body'  => [
                'query' => [
                    'term' => [
                        'activity_type_id' => $id
                    ]
                ]
            ]
        ];

        $response =  $this->client->search($params);
        return $response->asArray();
    }

    public function searchProperties($key, $value, $size, $matchType)
    {
        $params = [
            'index' => 'activity_logs',
            'size'  => $size,
            'body'  => [
                'query' => [
                    $matchType => [
                        "properties.$key" => $value
                    ]
                ]
            ]
        ];


        return $this->client->search($params)->asArray();
    }

    public function searchPropertiesByRange($min, $max, $key, $size, $format = null)
    {

        $range = [
            'lte' => $max,   // Less than or equal to
            'gte' => $min,  // Greater than or equal to
        ];
      if($format){
           $range =  array_merge($range, ['format' => $format]);
        }


        $params = [
            'index' => 'activity_logs',
            'size'  => $size,
            'body'  => [
                'query' => [
                    'range' => [
                        "properties.$key" => $range,
                    ]
                ]
            ]
        ];
        //dd($params);

        return $this->client->search($params)->asArray();
    }

    public function testWildcard(){
        $params = [
            'index' => 'activity_logs',
            'size' => 7000,
            'body' => [
                'query' => [
                    'match' => [
                        'properties.key4.sub_key2' => '2023-04'
                    ]
                ]
            ]
        ];
        $results = $this->client->search($params)->asArray();
        dd("here",$results);
    }

    public function aliasIndex(){
        $this->client->indices()->delete(['index' => 'activity_logs']);

        // 4. Rename the new index to the original index name
        return $this->client->indices()->putAlias([
            'index' => 'activity_logs_v2',
            'name' => 'activity_logs'
        ])->asArray();
    }



}
