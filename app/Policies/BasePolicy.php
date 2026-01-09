<?php

namespace App\Policies;

use App\Models\User;

class BasePolicy
{
    protected function isInternal(User $user): bool
    {
        return $user->is_internal;
    }

    protected function sameOrganization(User $user, $model): bool
    {
        return $user->organization_id !== null
            && $model->organization_id === $user->organization_id;
    }
}
