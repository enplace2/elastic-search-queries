<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\ElasticSearchModels\ElasticSearchActivityLog;
use App\Services\ElasticsearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class MigrateActivityLogsToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-activity-logs-to-elasticsearch';

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
        // ... [omitted code]
        $this->info('Starting migration...');
        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $totalCount = 50000000 - 20980000;
        $progressBar = $this->output->createProgressBar($totalCount);

        $lastProcessedId = 20980000;  // Start from the beginning.

        $chunkSize = 20000;
        while (true) {
            $activityLogs = ActivityLog::where('id', '>', $lastProcessedId)
                ->orderBy('id')
                ->limit($chunkSize)
                ->get();

            if ($activityLogs->isEmpty()) {
                break;
            }

            $bulkParams = [];
            foreach ($activityLogs as $activityLog) {
                $data = $activityLog->toArray();
                $data['properties'] = json_decode($data['properties'], true);

                // Add action and metadata:
                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => 'activity_logs',
                        '_id'    => $data['id']
                    ]
                ];
                // Add source:
                $bulkParams['body'][] = $data;

                $lastProcessedId = $data['id'];  // Update the last processed id.
            }

            // Bulk index all documents in this chunk:
            $client->bulk($bulkParams);

            $progressBar->advance(count($activityLogs));
        }

        $progressBar->finish();
        $this->info(PHP_EOL . 'Migration completed!');
    }

}
