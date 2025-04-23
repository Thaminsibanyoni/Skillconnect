<?php

namespace App\Http\Controllers;

use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\View\View; // Import View

class ProviderController extends Controller
{
    /**
     * Display a listing of approved providers.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View // Add Request
    {
        $query = User::where('role', 'provider')
                     ->where('status', 'approved');

        // TODO: Add filtering by service/category later
        // if ($request->filled('category')) {
        //     $query->whereHas('services.serviceCategory', function ($q) use ($request) {
        //         $q->where('service_categories.id', $request->category);
        //         // Or filter by category name/slug if preferred
        //     });
        // }

        // TODO: Add filtering by location later (requires location data on user/provider profile)
        // if ($request->filled('location')) { ... }

        // Basic name search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $providers = $query->orderBy('name')->paginate(12)->withQueryString(); // Append query string to pagination

        // Fetch categories for filter dropdown
        $categories = \App\Models\ServiceCategory::orderBy('name')->get();

        return view('providers.index', compact('providers', 'categories'));
    }

    /**
     * Display the specified provider's profile.
     *
     * @param  \App\Models\User $provider
     * @return \Illuminate\View\View
     */
    public function show(User $provider): View
    {
        // Ensure the user is an approved provider
        if ($provider->role !== 'provider' || $provider->status !== 'approved') {
            abort(404); // Or redirect with an error
        }

        // Eager load relationships needed for the profile (e.g., ratings received)
        $provider->loadAvg('ratingsReceived', 'rating');
        $provider->loadCount('ratingsReceived');
        $provider->load([
            'services.serviceCategory',
            'ratingsReceived' => function ($query) { // Load ratings with the user who gave them
                $query->with('user')->latest()->limit(10); // Load latest 10 reviews with seeker info
            }
        ]);

        return view('providers.show', compact('provider'));
    }
}
