<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy extends BasePolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($this->isInternal($user)) {
            return $user->tenant_id === $document->tenant_id;
        }

        return $user->tenant_id === $document->tenant_id
            && $this->sameOrganization($user, $document);
    }
}
