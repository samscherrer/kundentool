<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Milestone;
use App\Models\Offer;
use App\Models\ReviewRequest;
use App\Models\Ticket;
use App\Policies\DocumentPolicy;
use App\Policies\MilestonePolicy;
use App\Policies\OfferPolicy;
use App\Policies\ReviewRequestPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Ticket::class => TicketPolicy::class,
        Offer::class => OfferPolicy::class,
        Document::class => DocumentPolicy::class,
        ReviewRequest::class => ReviewRequestPolicy::class,
        Milestone::class => MilestonePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
