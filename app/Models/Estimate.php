<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'created_by_user_id',
        'hours_estimate',
        'currency',
        'amount_estimate_cents',
        'scope_note',
        'status',
        'sent_at',
        'decided_at',
        'decided_by_user_id',
        'decision_note',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
