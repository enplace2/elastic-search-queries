<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;
    protected $table = 'activity_types';

    public function activityLogs(){
        return $this->hasMany(ActivityLog::class);
    }
}
