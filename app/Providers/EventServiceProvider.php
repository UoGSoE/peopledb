<?php

namespace App\Providers;

use App\Events\PersonCreated;
use App\Events\PersonIsLeaving;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\AllocateAppropriateTasksToNewPerson;
use App\Listeners\AllocateAppropriateTasksToLeavingPerson;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PersonCreated::class => [
            AllocateAppropriateTasksToNewPerson::class,
        ],
        PersonIsLeaving::class => [
            AllocateAppropriateTasksToLeavingPerson::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
