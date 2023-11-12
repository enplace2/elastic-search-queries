<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AverageQueryTime extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function queryType(){
        return $this->belongsTo(QueryType::class);
    }
}
