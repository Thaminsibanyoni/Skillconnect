<x-app-layout> {{-- Use the main app layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Service Providers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters and Search Form --}}
            <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('providers.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Search Input --}}
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search by Name</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Enter provider name..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        {{-- Category Filter (Example - requires controller logic) --}}
                        {{-- <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Filter by Category</label>
                            <select name="category" id="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        {{-- Submit Button --}}
                        <div class="md:col-span-3 flex justify-end items-end">
                            <x-button type="submit">
                                Filter / Search
                            </x-button>
                             @if(request()->has('search') || request()->has('category'))
                                <a href="{{ route('providers.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    @if($providers->count() > 0)
                        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($providers as $provider)
                                <li class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200">
                                    <div class="w-full flex items-center justify-between p-6 space-x-6">
                                        <div class="flex-1 truncate">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-gray-900 text-sm font-medium truncate">{{ $provider->name }}</h3>
                                                {{-- Add service type/category later --}}
                                                {{-- <span class="flex-shrink-0 inline-block px-2 py-0.5 text-green-800 text-xs font-medium bg-green-100 rounded-full">Category</span> --}}
                                            </div>
                                            {{-- Add location/rating later --}}
                                            {{-- <p class="mt-1 text-gray-500 text-sm truncate">Location / Rating</p> --}}
                                        </div>
                                        <img class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" src="{{ $provider->profile_photo_url }}" alt="{{ $provider->name }}">
                                    </div>
                                    <div>
                                        <div class="-mt-px flex divide-x divide-gray-200">
                                            <div class="w-0 flex-1 flex">
                                                {{-- Link to provider profile page --}}
                                                <a href="{{ route('providers.show', $provider) }}" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500">
                                                    <!-- Heroicon name: solid/mail -->
                                                    {{-- <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                    </svg> --}}
                                                    <span class="ml-3">View Profile</span>
                                                </a>
                                            </div>
                                            {{-- Add contact/book button later --}}
                                            {{-- <div class="-ml-px w-0 flex-1 flex">
                                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500">
                                                    <span class="ml-3">Book Now</span>
                                                </a>
                                            </div> --}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $providers->links() }}
                        </div>
                    @else
                         <p class="text-center text-gray-500">No providers found matching your criteria.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
