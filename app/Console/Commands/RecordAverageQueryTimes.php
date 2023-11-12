<?php

namespace App\Console\Commands;

use App\Models\AverageQueryTime;
use App\Models\QueryType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecordAverageQueryTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:record-average-query-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //truncate query time averages table
        Schema::disableForeignKeyConstraints();
        AverageQueryTime::truncate();
        Schema::enableForeignKeyConstraints();

        //get all query types
        $queryTypes = QueryType::with('queryTimes')->get();

        //loop through query types
        foreach ($queryTypes as $queryType) {

            $totalQueryTimes =[
                'elasticsearch' => 0,
                'mysql' => 0,
            ];
            $totalRecordsReturned =[
                'elasticsearch' => 0,
                'mysql' => 0,
            ];

            $queryCount = [
                'elasticsearch' => 0,
                'mysql' => 0,
            ];

            $collectedQueryTimes = [
                'elasticsearch' => [],
                'mysql' => [],
            ];

            //loop through the query times
            $queryTimes = $queryType->queryTimes;
            foreach ($queryTimes as $queryTime) {
                $dbSource = $queryTime->source; // 'elasticsearch' or 'mysql'

                // add to total query times
                $totalQueryTimes[$dbSource] += $queryTime->time_in_ms;

                //add to total records returned
                $totalRecordsReturned[$dbSource] += $queryTime->number_of_records_returned;

                //add to total query count
                $queryCount[$dbSource] ++;

                // add to collected query times for median calculation
                $collectedQueryTimes[$dbSource][] = $queryTime->time_in_ms;
            }

            dump($totalQueryTimes, $totalRecordsReturned, $queryCount, $collectedQueryTimes);

            $dbSources = ['mysql', 'elasticsearch'];

            $meanQueryTimes =[
                'elasticsearch' => 0,
                'mysql' => 0,
            ];


              $medianQueryTimes =[
                'elasticsearch' => 0,
                'mysql' => 0,
            ];

              $meanRecordsReturned =[
                'elasticsearch' => 0,
                'mysql' => 0,
            ];

            foreach ($dbSources as $dbSource) {
                $meanQueryTimes[$dbSource] = ($queryCount[$dbSource] > 0) ? $totalQueryTimes[$dbSource] / $queryCount[$dbSource] : 0;
                $meanRecordsReturned[$dbSource] = ($queryCount[$dbSource] > 0) ? $totalRecordsReturned[$dbSource] / $queryCount[$dbSource] : 0;


                // Median calculation for query times
                sort($collectedQueryTimes[$dbSource]);
                $count = count($collectedQueryTimes[$dbSource]);
                $middleIndex = $count / 2;
                if ($count % 2 == 0) {
                    $medianQueryTimes[$dbSource] = ($collectedQueryTimes[$dbSource][$middleIndex - 1] + $collectedQueryTimes[$dbSource][$middleIndex]) / 2;
                } else {
                    $medianQueryTimes[$dbSource] = $collectedQueryTimes[$dbSource][floor($middleIndex)];
                }


            }

            foreach ( $dbSources as $dbSource) {
                AverageQueryTime::create([
                    'mean_time'     => $meanQueryTimes[$dbSource],
                    'median_time'   => $medianQueryTimes[$dbSource],
                    'query_type_id' => $queryType->id,
                    'db_source'     => $dbSource,
                    'mean_results_returned'  => $meanRecordsReturned[$dbSource],
                    'query_type_description' =>$queryType->description,
                    'query_type_string_identifier' =>$queryType->identifier
            ]);
            }

        }

    }
}
