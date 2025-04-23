<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        // Added loading for admin routes with web middleware
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'auth.provider' => \App\Http\Middleware\EnsureUserIsProvider::class, // Added provider auth alias
        ]);

        // Add CSRF exceptions here
        $middleware->validateCsrfTokens(except: [
            'payment/payfast/notify', // Exclude PayFast ITN route
            'payment/flutterwave/webhook', // Exclude Flutterwave webhook route
            // Add other webhook routes here later (e.g., Stripe)
            // 'stripe/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
