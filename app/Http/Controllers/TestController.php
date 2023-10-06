<?php

namespace App\Http\Controllers;

use App\Models\ElasticSearchModels\ElasticSearchActivityLog;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(): string
    {
        $log = new ElasticSearchActivityLog();
        dd($log->createIndex());
    }
}
