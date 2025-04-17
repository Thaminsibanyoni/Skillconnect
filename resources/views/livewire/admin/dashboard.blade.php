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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    @if($viewMode === 'all')
                        <h3 class="text-lg font-medium text-gray-900">System Overview</h3>
                        <p class="mt-1 text-sm text-gray-600">Displaying combined data for all users.</p>
                        {{-- Placeholder for 'All Users' specific charts/data --}}
                        <div class="mt-4 p-4 border rounded bg-gray-50 mb-6">System-wide charts and stats here...</div>

                        {{-- Latest Users --}}
                        <div class="mt-8">
                             <h3 class="text-lg font-medium text-gray-900 mb-2">Latest Registered Users</h3>
                             <ul role="list" class="divide-y divide-gray-200">
                                @forelse ($latestUsers ?? [] as $user)
                                    <li class="py-3 sm:py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <img class="size-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $user->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                            <div class="inline-flex items-center text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'seeker' ? 'bg-green-100 text-green-800' : ($user->role === 'provider' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                            <div class="inline-flex items-center text-sm text-gray-500">
                                                {{ $user->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-3 sm:py-4 text-center text-gray-500">No users registered yet.</li>
                                @endforelse
                             </ul>
                        </div>

                        {{-- Include links to management sections --}}
                        @include('admin.dashboard-links')

                    @elseif($viewMode === 'seeker')
                        <h3 class="text-lg font-medium text-gray-900">Seeker Overview</h3>
                        <p class="mt-1 text-sm text-gray-600">Displaying data relevant to service seekers.</p>
                        {{-- Placeholder for 'Seeker' specific charts/data --}}
                         <div class="mt-4 p-4 border rounded bg-gray-50">Seeker-specific charts and stats here...</div>

                    @elseif($viewMode === 'provider')
                        <h3 class="text-lg font-medium text-gray-900">Provider Overview</h3>
                        <p class="mt-1 text-sm text-gray-600">Displaying data relevant to service providers.</p>
                        {{-- Placeholder for 'Provider' specific charts/data --}}
                         <div class="mt-4 p-4 border rounded bg-gray-50">Provider-specific charts and stats here...</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
