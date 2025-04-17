<div class="flex items-center space-x-2">
    <button
        wire:click="toggleStatus"
        type="button"
        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        :class="{ 'bg-green-500': @js($isOnline), 'bg-gray-300': !@js($isOnline) }"
        role="switch"
        aria-checked="{{ $isOnline ? 'true' : 'false' }}">
        <span class="sr-only">Toggle Online Status</span>
        <span
            aria-hidden="true"
            class="inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
            :class="{ 'translate-x-5': @js($isOnline), 'translate-x-0': !@js($isOnline) }"></span>
    </button>
    <span class="text-sm font-medium {{ $isOnline ? 'text-green-600' : 'text-gray-500' }}">
        {{ $isOnline ? 'Online' : 'Offline' }}
    </span>
    @if(session()->has('status-error'))
         <span class="text-xs text-red-500">{{ session('status-error') }}</span>
    @endif
</div>
