<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewComment extends Model
{
    protected $fillable = [
        'review_request_id',
        'author_user_id',
        'visibility',
        'body',
        'context_type',
        'context_json',
        'created_at',
        'resolved_at',
        'resolved_by_user_id',
    ];

    protected $casts = [
        'context_json' => 'array',
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public $timestamps = false;
}
