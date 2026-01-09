<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Worklog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'user_id',
        'ticket_id',
        'task_id',
        'budget_position_id',
        'work_date',
        'hours',
        'billable',
        'description_internal',
        'description_invoice',
    ];

    protected $casts = [
        'work_date' => 'date',
        'billable' => 'boolean',
    ];
}
