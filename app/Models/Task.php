<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'order_id',
        'title',
        'status',
        'assigned_to_user_id',
        'assigned_to_customer_user_id',
        'due_at',
        'created_by_user_id',
    ];
}
