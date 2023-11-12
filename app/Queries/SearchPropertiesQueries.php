<?php

namespace App\Queries;

use App\Interfaces\RunsQuery;
use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\File;
use App\Models\QueryType;
use App\Models\User;
use App\Traits\ElasticsearchQuery;
use App\Traits\LogsQueryTimes;
use App\Traits\QueriesMysql;

class SearchPropertiesQueries implements RunsQuery
{
    use LogsQueryTimes, QueriesMysql, ElasticsearchQuery;

    private string $loremSubstring = "ipsa";
    private string $loremSubstring2 = "labor";
    private array $keys = ["key1", "key2", "key3", "key4"];
    private array $subKeys = ["sub_key1", "sub_key2"];
    public function __construct($mysqlRecordCount, $recordsToFetch)
    {
        $this->initializeElasticsearchQueryTrait();
        $this->initializeQueriesMysqlTrait($mysqlRecordCount);
        $this->recordsToFetch = $recordsToFetch;

        // first or create the type
        $queryType = $this->firstOrCreateType();
        $this->queryTypeId = $queryType->id;
    }

    public function run() {
        $this->queryOnKey1();
        $this->queryOnKey2();
        $this->queryOnKey3();
        $this->queryOnSubkey1();
        $this->queryOnSubkey2();


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

        $queryType = QueryType::firstOrCreate( [
            'identifier' => 'search_activity_properties_key1',
            'description' => "Searches for records by querying on 'key1' of the 'properties' json object. Key 1 always contains a single lorem ipsum generated word"
        ]);

        // elasticsearch
        $response =  $this->service->searchProperties('key1', $this->loremSubstring, $this->recordsToFetch, 'match');
        $this->logElasticSearchQueryTime($response, $queryType->id, false);

        //mysql
        $query = ActivityLog::where('properties->key4->sub_key1', 'LIKE', '%ipsa%')->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($queryType->id, $results);

    }

    /**
     * @return void
     * "key2" always contains a random number
     */
    public function queryOnKey2(){
        $queryType = QueryType::firstOrCreate( [
            'identifier' => 'search_activity_properties_key2',
            'description' => "Searches for records whose properties->key2 value is between 100000 and 20000. Key 2 is always a random integer."
        ]);

        // elasticsearch
        $response =  $this->service->searchPropertiesByRange(100000, 200000, 'key2', $this->recordsToFetch);
        $this->logElasticSearchQueryTime($response, $queryType->id, false);

        //mysql
        $query = ActivityLog::whereBetween('properties->key2', [100000, 200000])->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($queryType->id, $results);

    }

    /**
     * @return void
     * "key3" always contains a boolean
     */
    public function queryOnKey3(){
        $queryType = QueryType::firstOrCreate( [
            'identifier' => 'search_activity_properties_key3',
            'description' => "Searches for records whose properties->key3 is true. Key 3 is always a boolean "
        ]);
        // elasticsearch
        $response =  $this->service->searchProperties('key3', 'true', $this->recordsToFetch, 'match');
        $this->logElasticSearchQueryTime($response, $queryType->id, false);

        //mysql
        $query = ActivityLog::where('properties->key3', 'true')->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($queryType->id, $results);

    }

    public function queryOnKey4(){

    }

    public function queryOnSubkey1(){
        $queryType = QueryType::firstOrCreate( [
            'identifier' => 'search_activity_properties_key4_sub_key1',
            'description' => "Searches for records whose properties->key4->sub_key1 value contains a given substring. The value is always a lorem ipsum sentence."
        ]);
        $response =  $this->service->searchProperties(
            'key4.sub_key1',
            $this->loremSubstring,
            $this->recordsToFetch,
            'match'
        );
        $this->logElasticSearchQueryTime($response, $queryType->id, false);

        $query = ActivityLog::where('properties->key4->sub_key1', 'LIKE', "%$this->loremSubstring%")
            ->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);
        $this->logMysqlQueryTime($queryType->id, $results);

    }

    public function queryOnSubkey2(){
        $queryType = QueryType::firstOrCreate( [
            'identifier' => 'search_activity_properties_key4_sub_key2',
            'description' => "Searches for records whose properties->key4->sub_key2 is within a given date range. The value is always a datetime string."
        ]);
        $start = "2023-04-07 22:36:29";
        $end = "2023-05-24 05:12:50";


        $response =  $this->service->searchPropertiesByRange(
            min: $start,
            max: $end,
            key: 'key4.sub_key2',
            size: $this->recordsToFetch,
            format: 'yyyy-MM-dd HH:mm:ss'
        );
        $this->logElasticSearchQueryTime($response, $queryType->id, false);

        $query = ActivityLog::whereBetween('properties->key4->sub_key2', [$start, $end])->limit($this->recordsToFetch);
        $results = $this->runQueryAndRecordTime($query);

        $this->logMysqlQueryTime($queryType->id, $results);
    }
}
