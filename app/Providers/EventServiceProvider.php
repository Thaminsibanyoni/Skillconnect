<?php

namespace App\Providers;

use App\Listeners\RedirectBasedOnRole;
use Illuminate\Auth\Events\Login;
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
        // Add the Login event and its listener
        Login::class => [
            RedirectBasedOnRole::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register observers here if needed, e.g.:
        // \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        // Note: We already registered OrderObserver in AppServiceProvider, keep it there or move it here.
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Set to false if manually defining listeners here
    }
}
