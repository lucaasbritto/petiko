<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Events\UserLoggedIn;
use App\Listeners\NotifyUserOnTaskCreated;
use App\Listeners\NotifyUserOnTaskUpdated;
use App\Listeners\LogTaskDeletion;
use App\Listeners\LogUserLoginListener;

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
        TaskCreated::class => [
            NotifyUserOnTaskCreated::class,
        ],
        TaskUpdated::class => [
            NotifyUserOnTaskUpdated::class,
        ],
        TaskDeleted::class => [
            LogTaskDeletion::class,
        ],
        UserLoggedIn::class => [
            LogUserLoginListener::class,
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
