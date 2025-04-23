<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Activity Log') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

        {{-- Filters --}}
         <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label for="searchAdmin" value="{{ __('Filter by Admin') }}" class="dark:text-gray-300"/>
                <x-input wire:model.live.debounce.300ms="searchAdmin" id="searchAdmin" class="block mt-1 w-full" type="text" placeholder="Admin name..." />
            </div>
             <div>
                <x-label for="searchAction" value="{{ __('Filter by Action') }}" class="dark:text-gray-300"/>
                <x-input wire:model.live.debounce.300ms="searchAction" id="searchAction" class="block mt-1 w-full" type="text" placeholder="Action name..." />
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Timestamp</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Admin</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Target</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $log->adminUser->name ?? 'N/A' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $log->action }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($log->target)
                                    {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                    {{-- Optionally add link to target resource --}}
                                @else
                                    -
                                @endif
                            </td>
                             <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if($log->details)
                                    <pre class="text-xs whitespace-pre-wrap">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
