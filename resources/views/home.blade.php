<x-app-layout> {{-- Use the main app layout --}}
    {{-- Optional Header --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot> --}}

    {{-- Hero Section --}}
    <div class="bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-indigo-600 dark:text-indigo-400 tracking-wide uppercase">SkillConnect</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
                    On-Demand Services Platform
                </p>
                <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500 dark:text-gray-400">
                    Connect instantly with skilled professionals near you for any service you need, right when you need it.
                </p>
                <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Get started
                        </a>
                    </div>
                    {{-- Add link to browse services later --}}
                    {{-- <div class="ml-3 inline-flex">
                        <a href="#" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Browse Services
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- How It Works Section --}}
    <div class="py-12 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">How SkillConnect Works</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Simple Steps to Get Started
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                    Whether you need a service or you're providing one, getting started is easy.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    {{-- For Seekers --}}
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center size-12 rounded-md bg-indigo-500 text-white">
                                {{-- Heroicon: outline/magnifying-glass --}}
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <p class="ms-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">1. Find a Service</p>
                        </dt>
                        <dd class="mt-2 ms-16 text-base text-gray-500 dark:text-gray-400">
                            Search for the service you need by category or keyword. Browse profiles, ratings, and reviews to find the perfect provider near you.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center size-12 rounded-md bg-indigo-500 text-white">
                                {{-- Heroicon: outline/calendar-days --}}
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                            </div>
                            <p class="ms-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">2. Book or Schedule</p>
                        </dt>
                        <dd class="mt-2 ms-16 text-base text-gray-500 dark:text-gray-400">
                            Request the service immediately or schedule it for a convenient time. Provide your location and any specific details needed for the job.
                        </dd>
                    </div>

                    {{-- For Providers --}}
                     <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center size-12 rounded-md bg-emerald-500 text-white">
                                {{-- Heroicon: outline/briefcase --}}
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.07a2.25 2.25 0 0 1-2.25 2.25h-12A2.25 2.25 0 0 1 3.75 18.22V14.15M16.5 14.15v-2.475a3.375 3.375 0 0 0-3.375-3.375h-1.5a3.375 3.375 0 0 0-3.375 3.375V14.15m16.5 0h-2.25m-12 0h-2.25m12 0a2.25 2.25 0 0 0 2.25-2.25v-1.5a2.25 2.25 0 0 0-2.25-2.25H7.5A2.25 2.25 0 0 0 5.25 10.5v1.5a2.25 2.25 0 0 0 2.25 2.25m12 0h-12" />
                                </svg>
                            </div>
                            <p class="ms-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">3. Offer Your Skills</p>
                        </dt>
                        <dd class="mt-2 ms-16 text-base text-gray-500 dark:text-gray-400">
                            Register as a provider, list the services you offer, set your availability, and get verified to start receiving job requests in your area.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center size-12 rounded-md bg-emerald-500 text-white">
                                {{-- Heroicon: outline/currency-dollar --}}
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 11.219 12.768 11 12 11c-.768 0-1.536.219-2.121.659-.986.741-.986 2.275 0 3.016.879.659 2.303.659 3.182 0l.879-.659m-4.688-1.871a4.5 4.5 0 0 1 6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="ms-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">4. Get Paid</p>
                        </dt>
                        <dd class="mt-2 ms-16 text-base text-gray-500 dark:text-gray-400">
                            Accept jobs, complete them professionally, and receive payments directly to your secure wallet after the platform takes its commission.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Other Sections (e.g., Featured Categories) can be added here --}}

</x-app-layout>
