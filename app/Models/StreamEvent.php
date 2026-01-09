<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StreamEvent extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'context_type',
        'context_id',
        'event_type',
        'visibility',
        'actor_user_id',
        'payload_json',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];
}
