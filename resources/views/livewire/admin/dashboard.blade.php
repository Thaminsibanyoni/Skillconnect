<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- View Mode Switcher --}}
            <div class="mb-6">
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    <button wire:click="setViewMode('all')" type="button"
                            class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium {{ $viewMode === 'all' ? 'text-indigo-700 bg-indigo-100' : 'text-gray-700 hover:bg-gray-50' }} focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        All Users / System
                    </button>
                    <button wire:click="setViewMode('seeker')" type="button"
                            class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium {{ $viewMode === 'seeker' ? 'text-indigo-700 bg-indigo-100' : 'text-gray-700 hover:bg-gray-50' }} focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        Seekers
                    </button>
                    <button wire:click="setViewMode('provider')" type="button"
                            class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium {{ $viewMode === 'provider' ? 'text-indigo-700 bg-indigo-100' : 'text-gray-700 hover:bg-gray-50' }} focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        Providers
                    </button>
                </span>
            </div>

            {{-- Stats Overview (Basic) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</dd>
                </div>
                 <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Seekers</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSeekers }}</dd>
                </div>
                 <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Providers</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalProviders }}</dd>
                </div>
                {{-- Mode-specific stats --}}
                @if($viewMode === 'all')
                    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $pendingOrders ?? 0 }}</dd>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Completed Orders</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $completedOrders ?? 0 }}</dd>
                    </div>
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Providers</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $pendingProviders ?? 0 }}</dd>
                    </div>
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Platform Revenue (Gross)</dt>
                        {{-- Add currency formatting later --}}
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($platformRevenue ?? 0, 2) }}</dd>
                    </div>
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Categories</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $activeCategories ?? 0 }}</dd>
                    </div>
                @elseif($viewMode === 'seeker')
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Service Requests</dt>
                         <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalServiceRequests ?? 0 }}</dd>
                     </div>
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg. Rating Given</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($avgSeekerRatingGiven ?? 0, 1) }} / 5</dd>
                    </div>
                     {{-- Add more seeker stats cards --}}
                 @elseif($viewMode === 'provider')
                      <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Providers</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $pendingProviders ?? 0 }}</dd>
                    </div>
                     <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Online Providers</dt>
                         <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $onlineProviders ?? 0 }}</dd>
                     </div>
                      <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg. Rating Received</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($avgProviderRatingReceived ?? 0, 1) }} / 5</dd>
                    </div>
                      {{-- Add more provider stats cards --}}
                 @endif
            </div>

            {{-- Mode-Specific Content Area --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"> {{-- Added dark:bg --}}
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"> {{-- Added dark:bg/border --}}
                    @if($viewMode === 'all')
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">System Overview</h3> {{-- Added dark:text --}}
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Displaying combined data for all users.</p> {{-- Added dark:text --}}

                        {{-- Chart Area --}}
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Users by Role Chart --}}
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"> {{-- Added dark:bg --}}
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Users by Role</h4> {{-- Added dark:text --}}
                                <div x-data="userRoleChartData({{ json_encode($userRoleCounts ?? ['labels' => [], 'data' => []]) }})" x-init="renderChart()">
                                    <canvas id="userRoleChart"></canvas>
                                </div>
                            </div>
                            {{-- Orders by Status Chart --}}
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"> {{-- Added dark:bg --}}
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Orders by Status</h4> {{-- Added dark:text --}}
                                <div x-data="orderStatusChartData({{ json_encode($orderStatusChart ?? ['labels' => [], 'data' => []]) }})" x-init="renderChart()">
                                    <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

@push('scripts')
<script>
    // Alpine component for Users by Role Pie Chart
    document.addEventListener('alpine:init', () => {
        Alpine.data('userRoleChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('userRoleChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: 'Users by Role', // Corrected label access
                            data: this.data.data, // Corrected data access
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)', // Green (Seeker)
                                'rgba(54, 162, 235, 0.7)', // Blue (Provider)
                                'rgba(201, 203, 207, 0.7)' // Grey (Admin - if included)
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(201, 203, 207, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    },
                });
            }
        }));

        // Alpine component for Orders by Status Bar Chart
        Alpine.data('orderStatusChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('orderStatusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: 'Order Count',
                            data: this.data.data,
                            backgroundColor: [ // Add more colors if more statuses
                                'rgba(255, 205, 86, 0.7)', // Pending (Yellow)
                                'rgba(54, 162, 235, 0.7)', // Accepted (Blue)
                                'rgba(153, 102, 255, 0.7)',// In Progress (Purple)
                                'rgba(75, 192, 192, 0.7)', // Completed (Green)
                                'rgba(255, 99, 132, 0.7)', // Cancelled (Red)
                                'rgba(255, 159, 64, 0.7)' // Rejected (Orange)
                            ],
                             borderColor: [
                                'rgba(255, 205, 86, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1 // Ensure y-axis shows whole numbers for counts
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // Hide legend for bar chart if label is clear
                            }
                        }
                    }
                });
            }
        }));

        // Alpine component for Provider Earnings Line Chart
        Alpine.data('providerEarningsChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('providerEarningsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: 'Total Earnings',
                            data: this.data.data,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            fill: false
                        }]
                    },
                     options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }));

        // Alpine component for Provider Earnings Line Chart
        Alpine.data('providerEarningsChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('providerEarningsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: 'Total Earnings',
                            data: this.data.data,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            fill: false
                        }]
                    },
                     options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
@endpush
</div>


                        {{-- Latest Users --}}
                        <div class="mt-8">
                             <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Latest Registered Users</h3> {{-- Added dark:text --}}
                             <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700"> {{-- Added dark:divide --}}
                                @forelse ($latestUsers ?? [] as $user)
                                    <li class="py-3 sm:py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <img class="size-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"> {{-- Added dark:text --}}
                                                    {{ $user->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate"> {{-- Added dark:text --}}
                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                            <div class="inline-flex items-center text-sm text-gray-900 dark:text-gray-300"> {{-- Added dark:text --}}
                                                {{-- Adjusted dark mode badge colors --}}
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $user->role === 'seeker' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100' : '' }}
                                                    {{ $user->role === 'provider' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100' : '' }}
                                                    {{ $user->role === 'admin' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' : '' }}
                                                ">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                            <div class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400"> {{-- Added dark:text --}}
                                                {{ $user->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-3 sm:py-4 text-center text-gray-500 dark:text-gray-400">No users registered yet.</li> {{-- Added dark:text --}}
                                @endforelse
                             </ul>
                        </div>

                        {{-- Include links to management sections --}}
                        @include('admin.dashboard-links')

                    @elseif($viewMode === 'seeker')
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Seeker Overview</h3> {{-- Added dark:text --}}
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Displaying data relevant to service seekers.</p> {{-- Added dark:text --}}
                        {{-- Placeholder for 'Seeker' specific charts/data --}}
                         <div class="mt-4 p-4 border dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Seeker-specific charts and stats here...</div> {{-- Added dark styles --}}

                    @elseif($viewMode === 'provider')
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Provider Overview</h3> {{-- Added dark:text --}}
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Displaying data relevant to service providers.</p> {{-- Added dark:text --}}

                        {{-- Provider Earnings Chart --}}
                        <div class="mt-6 grid grid-cols-1 gap-6">
                             <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow"> {{-- Added dark:bg --}}
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Recent Provider Earnings (Placeholder)</h4> {{-- Added dark:text --}}
                                <div x-data="providerEarningsChartData({{ json_encode($providerEarningsChart ?? ['labels' => [], 'data' => []]) }})" x-init="renderChart()">
                                    <canvas id="providerEarningsChart"></canvas>
                                </div>
                            </div>
                        </div>
                         {{-- Add other provider specific content here --}}

                    @endif
                </div>
            </div>

        </div>
    </div>

@push('scripts')
<script>
    // Alpine component for Users by Role Pie Chart
    document.addEventListener('alpine:init', () => {
        Alpine.data('userRoleChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('userRoleChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: this.data.datasets[0].label,
                            data: this.data.datasets[0].data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)', // Blue (Provider)
                                'rgba(75, 192, 192, 0.7)', // Green (Seeker)
                                'rgba(201, 203, 207, 0.7)' // Grey (Admin - if included)
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(201, 203, 207, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    },
                });
            }
        }));

        // Alpine component for Orders by Status Bar Chart
        Alpine.data('orderStatusChartData', (chartData) => ({
            data: chartData,
            renderChart() {
                let ctx = document.getElementById('orderStatusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            label: 'Order Count',
                            data: this.data.data,
                            backgroundColor: [ // Add more colors if more statuses
                                'rgba(255, 205, 86, 0.7)', // Pending (Yellow)
                                'rgba(54, 162, 235, 0.7)', // Accepted (Blue)
                                'rgba(153, 102, 255, 0.7)',// In Progress (Purple)
                                'rgba(75, 192, 192, 0.7)', // Completed (Green)
                                'rgba(255, 99, 132, 0.7)', // Cancelled (Red)
                                'rgba(255, 159, 64, 0.7)' // Rejected (Orange)
                            ],
                             borderColor: [
                                'rgba(255, 205, 86, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1 // Ensure y-axis shows whole numbers for counts
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // Hide legend for bar chart if label is clear
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
@endpush
</div>
