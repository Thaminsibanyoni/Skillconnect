<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added Auth facade
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the 'admin' role via Spatie
        if (!Auth::check() || !Auth::user()->hasRole('admin')) { // Use Spatie's hasRole()
            // Redirect non-admins or unauthenticated users
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
