<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'title',
        'description',
        'created_by_user_id',
        'current_version_id',
        'linked_context_type',
        'linked_context_id',
    ];

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion()
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }
}
