<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateInviteCommand extends Command
{
    protected $signature = 'app:create-invite {--organization=} {--email=} {--role=customer_admin}';

    protected $description = 'Create an invite link for a customer user.';

    public function handle(): int
    {
        $organizationId = $this->option('organization');
        $email = $this->option('email');
        $role = $this->option('role');

        if (! $organizationId || ! $email) {
            $this->error('Organization and email are required.');
            return self::FAILURE;
        }

        $organization = Organization::findOrFail($organizationId);
        $token = Str::random(64);
        $hash = hash('sha256', $token);

        Invite::create([
            'tenant_id' => $organization->tenant_id,
            'organization_id' => $organization->id,
            'email' => $email,
            'role' => $role,
            'token_hash' => $hash,
            'expires_at' => now()->addDays(7),
        ]);

        AuditLog::create([
            'tenant_id' => $organization->tenant_id,
            'actor_user_id' => null,
            'action' => 'invite_created',
            'entity_type' => 'invite',
            'entity_id' => null,
            'details_json' => ['email' => $email, 'role' => $role],
        ]);

        $baseUrl = config('app.url');
        $link = rtrim($baseUrl, '/') . '/invite/' . $token;

        $this->info('Invite link: ' . $link);

        return self::SUCCESS;
    }
}
