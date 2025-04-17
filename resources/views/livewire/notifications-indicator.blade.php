<div class="relative">
    {{-- Button to trigger dropdown (and mark as read for now) --}}
    <button wire:click="markAsRead" type="button" class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <span class="absolute -inset-1.5"></span>
        <span class="sr-only">View notifications</span>
        {{-- Bell Icon --}}
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.017 5.454 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        {{-- Unread Count Badge --}}
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white text-xs text-white flex items-center justify-center">
                 {{-- Optionally show count if > 9: {{ $unreadCount > 9 ? '9+' : $unreadCount }} --}}
            </span>
        @endif
    </button>

    {{-- Dropdown content (to be implemented later) --}}
    {{-- <div class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
        <p class="p-4 text-sm text-gray-500">Notifications list here...</p>
    </div> --}}
</div>
