<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneSnapshot extends Model
{
    protected $fillable = ['milestone_id', 'snapshot_json'];

    protected $casts = [
        'snapshot_json' => 'array',
    ];
}
