<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    public function log(?User $actor, string $action, string $entityType, ?int $entityId, array $details = []): AuditLog
    {
        return AuditLog::create([
            'tenant_id' => $actor?->tenant_id,
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details_json' => $details,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
