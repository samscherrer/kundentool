<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
