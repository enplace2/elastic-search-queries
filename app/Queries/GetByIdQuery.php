<?php

namespace App\Queries;

use App\Models\ActivityLog;
use App\Models\QueryType;
use App\Traits\ElasticsearchQuery;
use App\Traits\QueriesMysql;
use App\Traits\LogsQueryTimes;

class GetByIdQuery
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private int $queryTypeId = 0;
    public function __construct()
    {
        $this->initializeElasticsearchQueryTrait();
        $this->initializeQueriesMysqlTrait();

        // first or create the type
        $queryType = $this->firstOrCreateType();
        $this->queryTypeId = $queryType->id;
    }

    public function run() {
        $this->queryOnElasticSearch();
        $this->queryOnMySQL();

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
        $this->logElasticSearchQueryTime($response, $this->queryTypeId);
    }

    public function queryOnMySQL(){
            $randomId = $this->getRandomMysqlId();
            $query = ActivityLog::whereId($randomId);
            $results = $this->runQueryAndRecordTime($query, 'first');
            $this->logMysqlQueryTime($this->queryTypeId, $results);
    }
}
