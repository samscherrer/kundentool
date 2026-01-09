<?php

namespace App\Policies;

use App\Models\ReviewRequest;
use App\Models\User;

class ReviewRequestPolicy extends BasePolicy
{
    public function view(User $user, ReviewRequest $review): bool
    {
        if ($this->isInternal($user)) {
            return $user->tenant_id === $review->version->document->tenant_id;
        }

        return $user->tenant_id === $review->version->document->tenant_id
            && $this->sameOrganization($user, $review->version->document);
    }
}
