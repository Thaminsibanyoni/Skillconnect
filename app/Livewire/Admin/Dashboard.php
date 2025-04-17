<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating; // Import Rating model
use App\Models\ServiceCategory;
use Livewire\Component;

class Dashboard extends Component
{
    public $viewMode = 'all'; // Default view: 'all', 'seeker', 'provider'

    public function setViewMode($mode)
    {
        if (in_array($mode, ['all', 'seeker', 'provider'])) {
            $this->viewMode = $mode;
            // Optionally reset pagination or other filters when mode changes
            // $this->resetPage();
        }
    }

    public function render()
    {
        // Basic Stats - these can be expanded significantly
        $totalUsers = User::count();
        $totalSeekers = User::where('role', 'seeker')->count();
        $totalProviders = User::where('role', 'provider')->count();
        $totalOrders = Order::count(); // Example: Total orders

        // Initialize data array
        $data = [
            'totalUsers' => $totalUsers,
            'totalSeekers' => $totalSeekers,
            'totalProviders' => $totalProviders,
        ];

        // Fetch stats based on view mode
        if ($this->viewMode === 'all') {
            $data['pendingOrders'] = Order::whereIn('status', ['pending', 'accepted', 'in_progress'])->count();
            $data['completedOrders'] = Order::where('status', 'completed')->count();
            $data['pendingProviders'] = User::where('role', 'provider')->where('status', 'pending')->count();
            // Placeholder for platform revenue (sum of completed order amounts)
            $data['platformRevenue'] = Order::where('status', 'completed')->sum('total_amount');
            $data['activeCategories'] = ServiceCategory::count(); // Assuming all categories in DB are active for now
            $data['latestUsers'] = User::latest()->take(5)->get(); // Fetch latest 5 users
            // Add more 'all' stats later
        } elseif ($this->viewMode === 'seeker') {
            $data['totalServiceRequests'] = Order::count();
            // Calculate average rating given by seekers
            $data['avgSeekerRatingGiven'] = Rating::avg('rating'); // Simplified: Average of ALL ratings for now
            // Add more 'seeker' stats later
        } elseif ($this->viewMode === 'provider') {
            $data['pendingProviders'] = User::where('role', 'provider')->where('status', 'pending')->count();
            $data['onlineProviders'] = User::where('role', 'provider')->where('is_online', true)->count(); // Use is_online column
            // Calculate average rating received by providers
            $data['avgProviderRatingReceived'] = Rating::avg('rating'); // Simplified: Average of ALL ratings for now
            // Add more 'provider' stats later
        }

        return view('livewire.admin.dashboard', $data)
               ->layout('layouts.admin');
    }
}
