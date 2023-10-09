<?php

namespace App\Queries;

use App\Models\ActivityLog;
use App\Models\QueryType;
use App\Services\ElasticsearchService;
use App\Traits\ElasticsearchQuery;
use App\Traits\LogsQueryTimes;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class GetByIdQuery
{
    use LogsQueryTimes, ElasticsearchQuery;

    private int $queryTpeId = 0;
    public function __construct()
    {
        $this->initializeElasticsearchQueryTrait();
    }

    public function run() {

        // first or create the type
        $queryType = $this->firstOrCreateType();
        $this->queryTpeId = $queryType->id;

        // do elasticsearch query
        $this->queryOnElasticSearch();

    }

    public function firstOrCreateType(){
        return QueryType::firstOrCreate( [
            'identifier' => 'get_by_id',
            'description' => 'Retrieve a single record based on its ID.'
        ]);
    }

    public function queryOnElasticSearch(): void
    {
        $response = $this->service->getDocumentById($this->randomElasticSearchId,'activity_logs');
        $this->logElasticSearchQueryTime($response, $this->queryTpeId);
    }

    public function queryOnMySQL(){

    }
}
