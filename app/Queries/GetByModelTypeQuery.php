<?php

namespace App\Queries;

use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\File;
use App\Models\QueryType;
use App\Models\User;
use App\Traits\ElasticsearchQuery;
use App\Traits\LogsQueryTimes;
use App\Traits\QueriesMysql;

class GetByModelTypeQuery
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private int $queryTypeId = 0;
    public function __construct($totalRecordCount, $recordsToFetch)
    {
        $this->initializeElasticsearchQueryTrait();
        $this->initializeQueriesMysqlTrait($totalRecordCount);
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
            'identifier' => 'get_by_model_type',
            'description' => 'Retrieves a set of records based on the model type.'
        ]);
    }

    public function queryOnElasticSearch(): void
    {

        $modelTypes =[
            User::class,
            File::class,
            Address::class
        ];

        foreach ($modelTypes as $modelType) {

            $response = $this->service->getByModelType($modelType, $this->recordsToFetch);
            $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);
        }


    }

    public function queryOnMySQL(){
        $modelTypes = [
            User::class,
            File::class,
            Address::class
        ];

        foreach ($modelTypes as $modelType) {
            $query = ActivityLog::where('model_type', $modelType)->limit($this->recordsToFetch);
            $results = $this->runQueryAndRecordTime($query, 'get');
            $this->logMysqlQueryTime($this->queryTypeId, $results);
        }
    }
}
