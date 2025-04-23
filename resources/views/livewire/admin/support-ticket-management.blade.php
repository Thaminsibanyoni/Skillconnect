<div>
     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Support Tickets') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        {{-- Filters --}}
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label for="statusFilter" value="{{ __('Filter by Status') }}" class="dark:text-gray-300"/>
                <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
             <div>
                <x-label for="priorityFilter" value="{{ __('Filter by Priority') }}" class="dark:text-gray-300"/>
                <select wire:model.live="priorityFilter" id="priorityFilter" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>

         {{-- Tickets Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                 <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assigned To</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ticket->id }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $ticket->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->subject, 40) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($ticket->status === 'resolved' || $ticket->status === 'closed') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 @endif
                                    @if($ticket->status === 'open') bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 @endif
                                    @if($ticket->status === 'in_progress') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                             <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($ticket->priority === 'high') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 @endif
                                    @if($ticket->priority === 'medium') bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 @endif
                                    @if($ticket->priority === 'low') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 @endif
                                ">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                             </td>
                             <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ticket->assignedAdmin->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View/Manage</a> {{-- TODO: Link to details/management --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No support tickets found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         {{-- Pagination --}}
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
