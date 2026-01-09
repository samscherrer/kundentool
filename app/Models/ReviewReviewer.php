<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReviewer extends Model
{
    protected $fillable = [
        'review_request_id',
        'reviewer_user_id',
        'role',
        'seen_at',
        'decision',
        'decision_note',
        'decided_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
        'decided_at' => 'datetime',
    ];
}
