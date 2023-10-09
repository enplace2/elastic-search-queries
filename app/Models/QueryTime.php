<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryTime extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'query_results' => 'object',
    ];


    public function queryType()
    {
        return $this->belongsTo(QueryType::class);
    }
}
