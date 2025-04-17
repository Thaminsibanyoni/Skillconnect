<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Commission Settings') }}
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

        <form wire:submit.prevent="saveSettings">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Commission Type --}}
                <div>
                    <x-label for="commissionType" value="{{ __('Commission Type') }}" />
                    <select id="commissionType" wire:model="commissionType" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                    <x-input-error for="commissionType" class="mt-2" />
                </div>

                {{-- Commission Rate/Amount --}}
                <div>
                    <x-label for="commissionRate">
                        @if($commissionType === 'percentage')
                            {{ __('Commission Rate (%)') }}
                        @else
                            {{ __('Commission Amount') }}
                        @endif
                    </x-label>
                    <x-input id="commissionRate" type="number" step="0.01" class="mt-1 block w-full" wire:model="commissionRate" />
                    <x-input-error for="commissionRate" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit">
                    {{ __('Save Settings') }}
                </x-button>
            </div>
        </form>

         <div class="mt-6 border-t pt-4 text-sm text-gray-600">
            <p><strong>Note:</strong> Saving these settings here currently only simulates the update. For persistent changes, modify the `COMMISSION_TYPE` and `COMMISSION_RATE` variables in your `.env` file and potentially clear the configuration cache (`php artisan config:cache`). A database-driven settings approach is recommended for production environments.</p>
        </div>
    </div>
</div>
