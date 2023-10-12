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

class SearchPropertiesQueries
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private string $loremSubstring = "ipsa";
    private string $loremSubstring2 = "labor";
    private array $keys = ["key1", "key2", "key3", "key4"];
    private array $subKeys = ["sub_key1", "sub_key2"];
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
            'identifier' => 'search_activity_properties',
            'description' => "Searches for records by querying on the 'properties' json object"
        ]);
    }

    /**
     * @return void
     * "key1" always contains a single lorem ipsum word
     */
    public function queryOnKey1(){
        // elasticsearch
        $response =  $this->service->searchProperties('key1', $this->loremSubstring, $this->recordsToFetch, 'match');
        $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);

        //mysql
        $query = ActivityLog::where('properties->key4->sub_key1', 'LIKE', '%ipsa%')->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);

    }

    /**
     * @return void
     * "key2" always contains a random number
     */
    public function queryOnKey2(){
        // elasticsearch
        $response =  $this->service->searchPropertiesByRange(100000, 200000, 'key2', $this->recordsToFetch);
        $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);

        //mysql
        $query = ActivityLog::whereBetween('properties->key2', [100000, 200000])->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);

    }

    /**
     * @return void
     * "key3" always contains a boolean
     */
    public function queryOnKey3(){
        // elasticsearch
        $response =  $this->service->searchProperties('key3', 'true', $this->recordsToFetch, 'term');
        $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);

        //mysql
        $query = ActivityLog::where('properties->key3', 'true')->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);

    }

    public function queryOnKey4(){

    }

    public function queryOnSubkey1(){
        $response =  $this->service->searchProperties(
            'key4.sub_key1',
            $this->loremSubstring2,
            $this->recordsToFetch,
            'match'
        );
        $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);

        $query = ActivityLog::where('properties->key4->sub_key1', 'LIKE', "%$this->loremSubstring2%")
            ->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);

    }

    public function queryOnSubkey2(){

        $start = '2023-01-01 00:00:00';
        $end = '2023-02-28 23:59:59';

        $response =  $this->service->searchPropertiesByRange(
            $start,
            $end,
            'key4.sub_key2',
            $this->recordsToFetch
        );
        $this->logElasticSearchQueryTime($response, $this->queryTypeId, false);

        $query = ActivityLog::whereBetween('properties->key4->sub_key2', [$start, $end])->get();
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($this->queryTypeId, $results);
    }
}
