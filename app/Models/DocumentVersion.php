<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'version_number',
        'file_storage_key',
        'original_filename',
        'mime_type',
        'file_size',
        'sha256_hash',
        'changelog',
        'customer_visible',
        'created_by_user_id',
    ];

    protected $casts = [
        'customer_visible' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
