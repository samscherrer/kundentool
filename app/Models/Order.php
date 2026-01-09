<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'title',
        'status',
        'created_by_user_id',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
