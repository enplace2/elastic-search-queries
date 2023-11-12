<?php

namespace App\Queries;

use App\Interfaces\RunsQuery;
use App\Models\ActivityLog;
use App\Models\QueryType;
use App\Models\User;
use App\Traits\ElasticsearchQuery;
use App\Traits\LogsQueryTimes;
use App\Traits\QueriesMysql;

class GetByUserIdQuery implements RunsQuery
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private int $queryTypeId = 0;
    public function __construct($mysqlRecordCount, $recordsToFetch)
    {

        $this->initializeElasticsearchQueryTrait();
        $this->initializeQueriesMysqlTrait($mysqlRecordCount);
        $this->randomUserId = mt_rand(1, User::count());
        $this->recordsToFetch = $recordsToFetch;

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
            'identifier' => 'get_by_user_id',
            'description' => 'Retrieve a set of records based on their user id.'
        ]);
    }

    public function queryOnElasticSearch(): void
    {
        $response = $this->service->getDocumentByUserId($this->randomUserId,'activity_logs', $this->recordsToFetch);
        $this->logElasticSearchQueryTime($response, $this->queryTypeId);
    }

    public function queryOnMySQL(){;
        $query = ActivityLog::where('performed_by_user_id', $this->randomUserId)->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);
    }
}
