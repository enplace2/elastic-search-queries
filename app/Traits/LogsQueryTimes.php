<?php

namespace App\Traits;

use App\Models\QueryTime;
trait LogsQueryTimes
{
    /**
     * Log the query time.
     *
     * @param int $queryTypeId
     * @param int $duration Duration of the query in milliseconds.
     * @param string $source Source of the query ('mysql' or 'elasticsearch').
     * @param array|null $queryResults Results of the query.
     * @return void
     */
    public function logQueryTime(int $queryTypeId, int $duration, string $source, string $queryResults, int $totalRecordsAtRuntime, ?int $shards = null): void
    {
        // Create the new QueryTime record.
        QueryTime::create([
            'query_type_id' => $queryTypeId,
            'time_in_ms' => $duration,
            'source' => $source,
            'total_records_at_run_time' => $totalRecordsAtRuntime,
            'query_results' => json_encode($queryResults, JSON_PRETTY_PRINT),
            'shards' => $shards,
        ]);
    }
}
