<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\ElasticsearchService;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait QueriesMysql
{
    use LogsQueryTimes;

    public function initializeQueriesMysqlTrait(): void
    {
       $this->totalRecordCount = ActivityLog::count();
    }

    public function runQueryAndRecordTime(Builder $query, string $method = 'get', $param = null): array
    {

        // Start timing
        $start = microtime(true);

        // Execute the query based on the method
        $result = match ($method) {
            'first' => $query->first(),
            'get' => $query->get(),
            'find' => $query->find($param),
            default => throw new InvalidArgumentException("Unknown method: $method"),
        };

        // End timing
        $end = microtime(true);

        $duration = ($end - $start) * 1000;

        return [
            'results' => $result,
            'took' => $duration
        ];
    }

    public function logMysqlQueryTime($queryTypeId, $queryTimeResults): void
    {
        $duration = $queryTimeResults['took'];
        $results = $queryTimeResults['results'];
        $results = json_encode($results);

        $this->logQueryTime($queryTypeId, $duration, 'mysql', $results, $this->totalRecordCount);
    }

    public function getRandomMysqlId(){
        return  mt_rand(1, $this->totalRecordCount);
    }


}
