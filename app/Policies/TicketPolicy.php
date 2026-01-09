<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy extends BasePolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        if ($this->isInternal($user)) {
            return $user->tenant_id === $ticket->tenant_id;
        }

        return $user->tenant_id === $ticket->tenant_id
            && $this->sameOrganization($user, $ticket);
    }
}
