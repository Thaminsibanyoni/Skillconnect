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
                            {{-- Add location later --}}
                            {{-- <p class="text-sm font-medium text-gray-500">Location</p> --}}

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

                    {{-- About Section / Services Offered / Reviews etc. can go here --}}
                    <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">About</h3>
                         <p class="mt-2 text-gray-600">
                            {{-- Add provider bio/description field later --}}
                            Profile description coming soon.
                         </p>
                    </div>

                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">Services Offered</h3>
                         <p class="mt-2 text-gray-600">
                            {{-- Load and display services associated with this provider --}}
                            Service list coming soon.
                         </p>
                    </div>

                     <div class="mt-8 border-t border-gray-200 pt-8">
                         <h3 class="text-lg font-medium text-gray-900">Reviews</h3>
                         <p class="mt-2 text-gray-600">
                            {{-- Load and display reviews for this provider --}}
                            Reviews list coming soon.
                         </p>
                    </div>

                    {{-- Booking Button/Form --}}
                     <div class="mt-8 border-t border-gray-200 pt-8">
                         {{-- Add booking component/button later --}}
                         <x-button>Book {{ $provider->name }}</x-button>
                     </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
