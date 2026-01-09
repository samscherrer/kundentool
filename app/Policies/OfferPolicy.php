<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy extends BasePolicy
{
    public function view(User $user, Offer $offer): bool
    {
        if ($this->isInternal($user)) {
            return $user->tenant_id === $offer->tenant_id;
        }

        return $user->tenant_id === $offer->tenant_id
            && $this->sameOrganization($user, $offer);
    }
}
