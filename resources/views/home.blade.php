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

    {{-- Other Sections (e.g., How it works, Featured Categories) can be added here --}}

</x-app-layout>
