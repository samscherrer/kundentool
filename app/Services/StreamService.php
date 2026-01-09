<?php

namespace App\Services;

use App\Models\StreamEvent;
use App\Models\User;

class StreamService
{
    public function log(
        User $actor,
        string $contextType,
        int $contextId,
        string $eventType,
        string $visibility,
        array $payload,
        int $organizationId
    ): StreamEvent {
        return StreamEvent::create([
            'tenant_id' => $actor->tenant_id,
            'organization_id' => $organizationId,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'event_type' => $eventType,
            'visibility' => $visibility,
            'actor_user_id' => $actor->id,
            'payload_json' => $payload,
        ]);
    }
}
