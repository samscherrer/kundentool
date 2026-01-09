<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRequest extends Model
{
    protected $fillable = [
        'document_version_id',
        'status',
        'requested_by_user_id',
        'requested_at',
        'due_at',
        'message_to_customer',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function version()
    {
        return $this->belongsTo(DocumentVersion::class, 'document_version_id');
    }

    public function reviewers()
    {
        return $this->hasMany(ReviewReviewer::class);
    }

    public function comments()
    {
        return $this->hasMany(ReviewComment::class);
    }
}
