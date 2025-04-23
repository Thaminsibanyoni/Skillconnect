<div>
    {{-- Display validation errors --}}
    <x-validation-errors class="mb-4" />

    {{-- Display success/error messages --}}
     @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="createOrder">
        {{-- Service Selection --}}
        <div class="mb-4">
            <x-label for="selectedServiceId" value="{{ __('Select Service') }}" />
            <select wire:model="selectedServiceId" id="selectedServiceId" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">-- Please select a service --</option>
                @forelse ($availableServices as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @empty
                    <option value="" disabled>This provider offers no services yet.</option>
                @endforelse
            </select>
            <x-input-error for="selectedServiceId" class="mt-2" />
        </div>

        {{-- Schedule Type --}}
        <div class="mb-4">
            <x-label value="{{ __('When?') }}" />
            <div class="mt-2 space-y-2">
                <label class="inline-flex items-center">
                    <input type="radio" wire:model.live="scheduleType" name="scheduleType" value="now" class="form-radio border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Book Now</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" wire:model.live="scheduleType" name="scheduleType" value="later" class="form-radio border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Schedule for Later</span>
                </label>
            </div>
             <x-input-error for="scheduleType" class="mt-2" />
        </div>

        {{-- Scheduled Date/Time (Conditional) --}}
        @if ($scheduleType === 'later')
            <div class="mb-4">
                <x-label for="scheduledDateTime" value="{{ __('Select Date and Time') }}" />
                {{-- Consider using a dedicated date/time picker component --}}
                <x-input type="datetime-local" wire:model="scheduledDateTime" id="scheduledDateTime" class="block mt-1 w-full" />
                <x-input-error for="scheduledDateTime" class="mt-2" />
            </div>
        @endif

        {{-- Address --}}
        <div class="mb-4">
            <x-label for="address" value="{{ __('Service Address') }}" />
            <textarea wire:model="address" id="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Enter the full address where the service is needed"></textarea>
            <x-input-error for="address" class="mt-2" />
        </div>

        {{-- TODO: Add Latitude/Longitude inputs if needed, potentially hidden and filled via JS Geocoding --}}

        {{-- Submit Button --}}
        <div class="mt-6">
            <x-button type="submit" wire:loading.attr="disabled" wire:target="createOrder">
                <span wire:loading wire:target="createOrder" class="mr-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                {{ __('Request Booking') }}
            </x-button>
        </div>
    </form>
</div>
