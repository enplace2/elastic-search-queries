<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';

    public function activityType(){
        return $this->belongsTo(ActivityType::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function performedByUser(){
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}

