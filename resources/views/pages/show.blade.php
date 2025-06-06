<x-app-layout> {{-- Use the main app layout from Jetstream --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $page->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200 prose max-w-none">
                    {{-- Render the HTML content from the database --}}
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
