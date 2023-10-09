<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ElasticSearchModels\ElasticSearchActivityLog;
use App\Models\QueryTime;
use App\Models\QueryType;
use App\Queries\GetByIdQuery;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test()
    {

        //$qt = QueryTime::latest()->first();
        //return $qt;
        //$qt->query_results = json_decode($qt->query_results);

        //dd(json_decode($qt->query_results));
        //dd($qt->toArray());

        $query = new GetByIdQuery();
        $query->run();


        dd("success");
        $service = new ElasticsearchService();
        $log = $service->getDocumentById(18290726, 'activity_logs');
        dd($log);

        dd($log);


        $query = new GetByIdQuery();
        $query->run();
        dd("success");

    }
}
