<!DOCTYPE html>
{{-- Add Alpine.js data for dark mode state --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Expose Auth User ID to JavaScript --}}
        @auth
            <script>
                window.userId = {{ auth()->id() }};
                // Pass initial provider online status if needed for Echo join logic
                window.isProviderInitiallyOnline = {{ (auth()->user()->role === 'provider' && auth()->user()->is_online) ? 'true' : 'false' }};
            </script>
        @endauth

        <!-- Styles -->
        @livewireStyles

        {{-- Leaflet CSS --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin=""/>
    </head>
    {{-- Apply dark class based on Alpine state --}}
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <x-banner />

        {{-- Add Dark Mode Toggle Button (Example placement in body, adjust as needed) --}}
        <div class="fixed top-4 right-4 z-50">
            <button @click="darkMode = !darkMode" class="p-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
        </div>

        <div class="min-h-screen"> {{-- Removed bg-gray-100 from here --}}
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        {{-- Chart.js Initialization --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('userRoleChartData', (chartData) => ({
                    chartData: chartData,
                    chartInstance: null,
                    renderChart() {
                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                        }
                        const ctx = document.getElementById('userRoleChart');
                        if (!ctx) return; // Don't render if canvas not found

                        this.chartInstance = new Chart(ctx, {
                            type: 'pie', // Or 'doughnut'
                            data: {
                                labels: this.chartData.labels,
                                datasets: [{
                                    label: 'User Roles',
                                    data: this.chartData.data,
                                    backgroundColor: [
                                        'rgb(59, 130, 246)', // blue-500 for Seekers
                                        'rgb(16, 185, 129)', // emerald-500 for Providers
                                        // Add more colors if needed
                                    ],
                                    hoverOffset: 4
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
                            }
                        });
                    }
                }));

                Alpine.data('orderStatusChartData', (chartData) => ({
                    chartData: chartData,
                    chartInstance: null,
                    renderChart() {
                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                        }
                        const ctx = document.getElementById('orderStatusChart');
                        if (!ctx) return;

                        this.chartInstance = new Chart(ctx, {
                            type: 'bar', // Bar chart for status counts
                            data: {
                                labels: this.chartData.labels,
                                datasets: [{
                                    label: 'Order Count',
                                    data: this.chartData.data,
                                    backgroundColor: [ // Add more colors as needed
                                        'rgba(255, 205, 86, 0.7)', // pending (yellow)
                                        'rgba(54, 162, 235, 0.7)', // accepted (blue)
                                        'rgba(153, 102, 255, 0.7)',// in_progress (purple)
                                        'rgba(75, 192, 192, 0.7)', // completed (green)
                                        'rgba(201, 203, 207, 0.7)',// cancelled (grey)
                                        'rgba(255, 99, 132, 0.7)'  // rejected (red)
                                    ],
                                    borderColor: [
                                        'rgb(255, 205, 86)',
                                        'rgb(54, 162, 235)',
                                        'rgb(153, 102, 255)',
                                        'rgb(75, 192, 192)',
                                        'rgb(201, 203, 207)',
                                        'rgb(255, 99, 132)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Horizontal bar chart might be better if many statuses
                                scales: {
                                    x: { beginAtZero: true }
                                },
                                responsive: true,
                                plugins: {
                                    legend: { display: false }, // Hide legend for bar chart
                                }
                            }
                        });
                    }
                }));
            });
        </script>

        {{-- Leaflet JS --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>

        {{-- Custom Map Script Placeholder --}}
        @stack('map-scripts')

    </body>
</html>
