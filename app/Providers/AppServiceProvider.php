<?php

namespace App\Providers;

use App\Models\Order; // Import Order model
use App\Observers\OrderObserver; // Import OrderObserver
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class); // Register the observer
    }
}
