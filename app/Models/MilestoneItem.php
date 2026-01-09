<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneItem extends Model
{
    protected $fillable = [
        'milestone_id',
        'item_type',
        'item_id',
    ];
}
