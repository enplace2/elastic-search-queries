<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ElasticSearchModels\ElasticSearchActivityLog;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(): string
    {
        $client = new ElasticsearchService();
        //dd($activityLog->toArray());

        $response = $client->getDocumentCount('activity_logs');

        //dd($response);
        $body = $response->getBody();
        dd(json_decode($body, true));
        dd($activityLog->toArray());

    }
}
