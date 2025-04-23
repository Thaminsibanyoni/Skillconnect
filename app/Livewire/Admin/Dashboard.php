<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB; // Import DB facade
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
            $data['activeCategories'] = ServiceCategory::count();
            $data['latestUsers'] = User::latest()->take(5)->get();

            // Data for Users by Role Pie Chart
            $data['userRoleCounts'] = [
                 'labels' => ['Seekers', 'Providers'],
                 'data' => [$totalSeekers, $totalProviders],
             ];

            // Data for Orders by Status Chart
            $orderStatusCounts = Order::query()
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            // Prepare data in a format Chart.js can easily use
            $statuses = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected']; // Ensure consistent order
            $data['orderStatusChart'] = [
                'labels' => collect($statuses)->map(fn($status) => ucfirst(str_replace('_', ' ', $status)))->toArray(),
                'data' => collect($statuses)->map(fn($status) => $orderStatusCounts[$status] ?? 0)->toArray(),
            ];

            // Add more 'all' stats/chart data later
        } elseif ($this->viewMode === 'seeker') {
            $data['totalServiceRequests'] = Order::count();
            // Calculate average rating given by seekers
            $data['avgSeekerRatingGiven'] = Rating::avg('rating'); // Simplified: Average of ALL ratings for now
            // Add more 'seeker' stats later
        } elseif ($this->viewMode === 'provider') {
            $data['pendingProviders'] = User::where('role', 'provider')->where('status', 'pending')->count();
            $data['onlineProviders'] = User::where('role', 'provider')->where('is_online', true)->count();
            // Calculate average rating received by providers
            $data['avgProviderRatingReceived'] = Rating::avg('rating'); // Simplified: Average of ALL ratings for now

            // Placeholder data for Provider Earnings Chart (e.g., last 7 days)
            $data['providerEarningsChart'] = [
                'labels' => ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], // Replace with actual dates
                'data' => [150, 200, 180, 220, 170, 210, 250], // Replace with actual earnings data query
            ];
            // Add more 'provider' stats later
        }

        return view('livewire.admin.dashboard', $data)
               ->layout('layouts.admin');
    }
}
