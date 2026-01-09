<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'order_id',
        'subject',
        'status',
        'priority',
        'created_by_user_id',
        'assigned_to_user_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function estimate()
    {
        return $this->hasOne(Estimate::class);
    }

    public function streamEvents()
    {
        return $this->morphMany(StreamEvent::class, 'context');
    }
}
