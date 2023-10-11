<?php
namespace App\Traits;
use App\Services\ElasticsearchService;

trait ElasticsearchQuery
{
    use LogsQueryTimes;
    protected int $totalElasticSearchCount;
    protected int $randomElasticSearchId;
    protected int $millionsOfRecords;

    public function initializeElasticsearchQueryTrait(): void
    {
        $this->service = new ElasticsearchService();
        $this->totalElasticSearchCount = $this->getTotalElasticSearchCount();
        $this->randomElasticSearchId =  mt_rand(1, $this->totalElasticSearchCount);
        $this->millionsOfRecords = $this->totalElasticSearchCount / 1000000;
    }


    public function logElasticSearchQueryTime($responseArray, int $queryTypeId, $logQueryResults = true): void
    {
        $elasticSearchCountInfo = $this->getElasticsearchCountInfo();
        $duration = $responseArray['took'];
        $jsonResponseArray = json_encode($responseArray, JSON_UNESCAPED_SLASHES);

        $this->logQueryTime(
            queryTypeId: $queryTypeId,
            duration: $duration,
            source: 'elasticsearch',
            queryResults: $logQueryResults ? $jsonResponseArray : '',
            totalRecordsAtRuntime: $elasticSearchCountInfo["count"],
            shards: $elasticSearchCountInfo["_shards"]["successful"],
            recordsReturned: count($responseArray["hits"]["hits"])
        );
    }

    public function getElasticsearchCountInfo()
    {
        return $this->service->getDocumentCount('activity_logs');
    }


    public function getTotalElasticSearchCount()
    {
        $countInfo =  $this->getElasticsearchCountInfo();
        return $countInfo["count"];
    }

}
