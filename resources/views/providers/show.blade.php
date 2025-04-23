<x-app-layout> {{-- Use the main app layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $provider->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">

                    <div class="md:flex md:items-center md:space-x-5">
                        <div class="flex-shrink-0">
                            <img class="h-24 w-24 rounded-full object-cover" src="{{ $provider->profile_photo_url }}" alt="{{ $provider->name }}">
                        </div>
                        <div class="mt-4 md:mt-0">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $provider->name }}</h1>
                            {{-- Display Location if available --}}
                            @if($provider->latitude && $provider->longitude)
                                <p class="text-sm font-medium text-gray-500 mt-1">
                                    {{-- Heroicon: outline/map-pin --}}
                                    <svg class="inline-block h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    {{-- Displaying coords for now, replace with reverse geocoding later --}}
                                    Lat: {{ number_format($provider->latitude, 4) }}, Lng: {{ number_format($provider->longitude, 4) }}
                                </p>
                            @endif

                            {{-- Rating --}}
                            <div class="mt-2 flex items-center">
                                @if($provider->ratings_received_count > 0)
                                    {{-- Simple Star Rating Display --}}
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="h-5 w-5 flex-shrink-0 {{ $provider->ratings_received_avg_rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="ml-2 text-sm text-gray-500">{{ number_format($provider->ratings_received_avg_rating, 1) }} ({{ $provider->ratings_received_count }} reviews)</p>
                                @else
                                    <p class="text-sm text-gray-500">No reviews yet</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- About Section --}}
                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">About</h3>
                         <p class="mt-2 text-gray-600 whitespace-pre-wrap"> {{-- Use whitespace-pre-wrap to preserve line breaks --}}
                            {!! nl2br(e($provider->bio ?? 'No description provided.')) !!} {{-- Display bio, converting newlines --}}
                         </p>
                    </div>

                    {{-- Services Offered --}}
                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">Services Offered</h3>
                         @if($provider->services->count() > 0)
                            <ul role="list" class="mt-4 divide-y divide-gray-200 border-t border-b border-gray-200">
                                @foreach($provider->services as $service)
                                    <li class="flex items-center justify-between py-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $service->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $service->serviceCategory->name ?? 'Uncategorized' }}</p>
                                        </div>
                                        {{-- Add button to select this service for booking? --}}
                                    </li>
                                @endforeach
                            </ul>
                         @else
                            <p class="mt-2 text-gray-600">
                                This provider has not listed any specific services yet.
                            </p>
                         @endif
                    </div>

                    {{-- Reviews --}}
                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">Reviews ({{ $provider->ratings_received_count }})</h3>
                         @if($provider->ratingsReceived->count() > 0)
                            <div class="mt-4 space-y-6">
                                @foreach($provider->ratingsReceived as $rating)
                                    <div class="flex space-x-4 text-sm text-gray-500">
                                        <div class="flex-none py-0.5">
                                            <img src="{{ $rating->user?->profile_photo_url }}" alt="{{ $rating->user?->name }}" class="size-8 rounded-full bg-gray-100">
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $rating->user?->name ?? 'Anonymous' }}</h4>
                                            <p>
                                                <div class="flex items-center">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <svg class="h-4 w-4 flex-shrink-0 {{ $rating->rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </p>
                                            @if($rating->comment)
                                                <div class="mt-2 prose prose-sm max-w-none text-gray-500">
                                                    <p>{{ $rating->comment }}</p>
                                                </div>
                                            @endif
                                             <p class="mt-1 text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- Add link to view all reviews if paginating --}}
                            </div>
                         @else
                            <p class="mt-2 text-gray-600">
                                This provider has not received any reviews yet.
                            </p>
                         @endif
                    </div>

                    {{-- Booking Form --}}
                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Request Service</h3>
                         @livewire('booking-form', ['provider' => $provider])
                     </div>

                     {{-- Removed Temporary Payment Buttons --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
