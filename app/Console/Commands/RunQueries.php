<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Queries\GetByActivityTypeQuery;
use App\Queries\GetByIdQuery;
use App\Queries\GetByModelTypeQuery;
use App\Queries\GetByUserIdQuery;
use App\Queries\SearchPropertiesQueries;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class RunQueries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-queries';

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
        $mysqlRecordCount = ActivityLog::count();
        $this->info("Total record count: $mysqlRecordCount");
        $this->line('starting queries...');
        $progressBar = $this->output->createProgressBar(5 * 30);


        $queryClasses = [
            GetByIdQuery::class,
            //GetByModelTypeQuery::class, queries 3 times
            //GetByActivityTypeQuery::class, queries 10 times
            GetByUserIdQuery::class,
            SearchPropertiesQueries::class
        ];

        foreach ($queryClasses as $queryClass) {
            for ($i = 0; $i < 30; $i++) {
                $query = new $queryClass(mysqlRecordCount: $mysqlRecordCount, recordsToFetch: 7000);
                $query->run();
                $progressBar->advance(1);
            }
        }

        $getByModelTypeQuery = new GetByModelTypeQuery(mysqlRecordCount: $mysqlRecordCount, recordsToFetch: 7000);
        for ($i=0; $i<10; $i++){
            $getByModelTypeQuery->run();
            $progressBar->advance(3);
        }

        $getByActivityTypeQuery = new GetByActivityTypeQuery(mysqlRecordCount: $mysqlRecordCount, recordsToFetch: 7000);

        for ($i=0; $i<3; $i++){
            $getByActivityTypeQuery->run();
            $progressBar->advance(10);
        }

        $this->info('Complete');
    }
}
