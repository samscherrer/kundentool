<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'title',
        'type',
        'planned_at',
        'actual_at',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'planned_at' => 'datetime',
        'actual_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(MilestoneItem::class);
    }
}
