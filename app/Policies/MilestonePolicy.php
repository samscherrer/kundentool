<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;

class MilestonePolicy extends BasePolicy
{
    public function view(User $user, Milestone $milestone): bool
    {
        if ($this->isInternal($user)) {
            return $user->tenant_id === $milestone->tenant_id;
        }

        return $user->tenant_id === $milestone->tenant_id
            && $this->sameOrganization($user, $milestone);
    }
}
