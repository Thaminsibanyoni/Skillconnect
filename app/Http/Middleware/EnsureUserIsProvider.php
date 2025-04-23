<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsProvider
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'provider') {
            // Redirect non-providers or unauthenticated users
            // Could redirect to home or show a 403 error
            abort(403, 'Access denied. Provider role required.');
        }
        // Optional: Check if provider status is 'approved' as well
        // if (Auth::user()->status !== 'approved') {
        //     abort(403, 'Access denied. Provider account not approved.');
        // }

        return $next($request);
    }
}
