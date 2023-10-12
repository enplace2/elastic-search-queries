<?php

namespace App\Queries;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Address;
use App\Models\File;
use App\Models\QueryType;
use App\Models\User;
use App\Traits\ElasticsearchQuery;
use App\Traits\LogsQueryTimes;
use App\Traits\QueriesMysql;

class GetByActivityTypeQuery
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private int $queryTypeId = 0;
    public function __construct($totalRecordCount, $recordsToFetch)
    {
        $this->initializeElasticsearchQueryTrait();
        $this->initializeQueriesMysqlTrait($totalRecordCount);
        $this->recordsToFetch = $recordsToFetch;
        $this->activityTypes = ActivityType::all();

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
            'identifier' => 'get_by_activity_type',
            'description' => 'Retrieves a set of records based on the activity type of the record.'
        ]);
    }

    public function queryOnElasticSearch(): void
    {


        foreach ($this->activityTypes as $activityType) {
            $response = $this->service->getByActivityTypeId($activityType->id, $this->recordsToFetch);
            $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);
        }


    }

    public function queryOnMySQL(){


        foreach ($this->activityTypes as $activityType) {
            $query = ActivityLog::where('activity_type_id', $activityType->id)->limit($this->recordsToFetch);
            $results = $this->runQueryAndRecordTime($query, 'get');
            $this->logMysqlQueryTime($this->queryTypeId, $results);
        }
    }
}
