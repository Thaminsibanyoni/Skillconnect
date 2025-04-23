<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $redirectPath = '/'; // Default fallback

        if ($user) {
            switch ($user->role) {
                case 'admin':
                    $redirectPath = route('admin.dashboard', [], false); // Use route helper, relative path
                    break;
                case 'provider':
                    $redirectPath = route('provider.dashboard', [], false); // Use route helper, relative path
                    break;
                case 'seeker':
                    // Redirect seekers to their dashboard or order history
                    $redirectPath = route('seeker.orders.index', [], false); // Example: redirect to orders
                    break;
                default:
                    // Default dashboard if role is unexpected
                    $redirectPath = config('fortify.home', '/dashboard');
                    break;
            }
        }

        // Fortify uses a session variable to determine the redirect
        // We modify the intended location *before* Fortify redirects.
        // Note: This relies on internal Fortify behavior which might change,
        // but is a common approach. Check Fortify documentation for latest best practices.
        session(['url.intended' => $redirectPath]);

        // Alternatively, directly manipulate the redirect response if possible,
        // but modifying the intended URL is often simpler with Fortify's flow.
    }
}
