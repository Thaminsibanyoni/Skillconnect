<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Provider Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">My Approved Service Areas</h3>
                    @if($approvedCities->count() > 0)
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($approvedCities->groupBy('province.name') as $provinceName => $cities)
                                <li>
                                    <span class="font-semibold">{{ $provinceName ?? 'Uncategorized' }}:</span>
                                    {{ $cities->pluck('name')->implode(', ') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">You have not been assigned any service areas yet. Please contact the administrator.</p>
                    @endif

                    {{-- TODO: Add other dashboard elements: Stats, Map showing online status, Quick links --}}
                    <div class="mt-8 border-t pt-6">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Links</h3>
                         <div class="space-x-2">
                             <a href="{{ route('provider.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                My Orders
                            </a>
                             <a href="{{ route('provider.services.manage') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                My Services
                            </a>
                            {{-- Add link to profile settings --}}
                             <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                My Profile
                            </a>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
