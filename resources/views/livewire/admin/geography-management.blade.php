<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Geography Management (Provinces & Cities)') }}
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Provinces Section --}}
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Provinces</h3>
                    <x-button wire:click="createProvince()">Add Province</x-button>
                </div>
                <div class="overflow-x-auto shadow border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                         <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($provinces as $province)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $province->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button wire:click="editProvince({{ $province->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="deleteProvince({{ $province->id }})" wire:confirm="Are you sure? Deleting a province will also delete its cities." class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No provinces found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cities Section --}}
            <div>
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Cities</h3>
                    <x-button wire:click="createCity()">Add City</x-button>
                </div>
                 <div class="overflow-x-auto shadow border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                         <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($cities as $city)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $city->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $city->province->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button wire:click="editCity({{ $city->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="deleteCity({{ $city->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No cities found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 {{-- Pagination for Cities --}}
                <div class="mt-4">
                    {{ $cities->links() }}
                </div>
            </div>

        </div>
    </div>

     {{-- Province Modal --}}
    <x-dialog-modal wire:model.live="showProvinceModal">
        <x-slot name="title">
            {{ $provinceId ? 'Edit Province' : 'Add New Province' }}
        </x-slot>
        <x-slot name="content">
            <div class="mt-4">
                <x-label for="provinceName" value="{{ __('Province Name') }}" />
                <x-input id="provinceName" type="text" class="mt-1 block w-full" wire:model.defer="provinceName" />
                <x-input-error for="provinceName" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="closeProvinceModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-button class="ms-3" wire:click="saveProvince()" wire:loading.attr="disabled">
                {{ $provinceId ? __('Update') : __('Save') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

     {{-- City Modal --}}
     <x-dialog-modal wire:model.live="showCityModal">
        <x-slot name="title">
            {{ $cityId ? 'Edit City' : 'Add New City' }}
        </x-slot>
        <x-slot name="content">
            <div class="mt-4">
                <x-label for="selectedProvinceId" value="{{ __('Province') }}" />
                <select id="selectedProvinceId" wire:model.defer="selectedProvinceId" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">-- Select Province --</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="selectedProvinceId" class="mt-2" />
            </div>
             <div class="mt-4">
                <x-label for="cityName" value="{{ __('City Name') }}" />
                <x-input id="cityName" type="text" class="mt-1 block w-full" wire:model.defer="cityName" />
                <x-input-error for="cityName" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="closeCityModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-button class="ms-3" wire:click="saveCity()" wire:loading.attr="disabled">
                {{ $cityId ? __('Update') : __('Save') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
