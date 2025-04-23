<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage My Offered Services') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif
         @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="saveServices">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Select the services you offer:</h3>

            <div class="space-y-4">
                @forelse ($allServices as $service)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            {{-- Important: Use service ID as the key for wire:model --}}
                            <input id="service_{{ $service->id }}"
                                   wire:model="providerServices.{{ $service->id }}"
                                   value="{{ $service->id }}"
                                   type="checkbox"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="service_{{ $service->id }}" class="font-medium text-gray-700">{{ $service->name }}</label>
                            <p class="text-gray-500">{{ $service->serviceCategory->name ?? 'Uncategorized' }} - {{ $service->description }}</p>
                        </div>
                    </div>
                @empty
                     <p class="text-gray-500">No services have been added by the administrator yet.</p>
                @endforelse
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit" wire:loading.attr="disabled" wire:target="saveServices">
                    {{ __('Save Offered Services') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
