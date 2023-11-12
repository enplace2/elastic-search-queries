<?php

namespace App\Interfaces;

interface RunsQuery
{
    public function run();
    public function __construct($mysqlRecordCount, $recordsToFetch);
}
