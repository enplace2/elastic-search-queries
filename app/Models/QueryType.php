<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function queryTimes()
    {
        return $this->hasMany(QueryTime::class);
    }

    public function averageQueryTimes(){
        return $this->hasMany(AverageQueryTime::class);
    }
}
