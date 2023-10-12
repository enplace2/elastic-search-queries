<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\ElasticSearchModels\ElasticSearchActivityLog;
use App\Models\QueryTime;
use App\Models\QueryType;
use App\Queries\GetByActivityTypeQuery;
use App\Queries\GetByIdQuery;
use App\Queries\GetByModelTypeQuery;
use App\Queries\GetByUserIdQuery;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test()
    {

        $service = new ElasticsearchService();
        $log = $service->searchProperties('key1', 'ipsa', 7000);
        dd($log);
        $count = 50000000;//ActivityLog::count();
        $query = new GetByUserIdQuery($count, 7000);
        $query->run();
        dd("success");

        $className = Address::class;
        $service = new ElasticsearchService();
        $test = $service->getByModelType($className);
        dd($test);

        //$qt = QueryTime::latest()->first();
        //return $qt;
        //$qt->query_results = json_decode($qt->query_results);

        //dd(json_decode($qt->query_results));
        //dd($qt->toArray());



        $service = new ElasticsearchService();
        $log = $service->getRandomDocumentFromActivityLogs(18290726, 'activity_logs');
        dd($log);

        dd($log);


        $query = new GetByIdQuery();
        $query->run();
        dd("success");

    }
}
