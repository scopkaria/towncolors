<?php

namespace App\Providers;

use App\Events\ProjectAssigned;
use App\Events\ProjectCreated;
use App\Events\ProjectFileUploaded;
use App\Events\ProjectStatusChanged;
use App\Listeners\NotifyAdminsOfNewProject;
use App\Listeners\NotifyClientOfFileUpload;
use App\Listeners\NotifyFreelancerOfAssignment;
use App\Listeners\NotifyPartiesOfStatusChange;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // ── Automation system ────────────────────────────────────────────────
        ProjectCreated::class => [
            NotifyAdminsOfNewProject::class,
        ],
        ProjectAssigned::class => [
            NotifyFreelancerOfAssignment::class,
        ],
        ProjectFileUploaded::class => [
            NotifyClientOfFileUpload::class,
        ],
        ProjectStatusChanged::class => [
            NotifyPartiesOfStatusChange::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
