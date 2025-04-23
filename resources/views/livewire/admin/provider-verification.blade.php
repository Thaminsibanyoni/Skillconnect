<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Provider Document Verification') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-100 rounded">
                {{ session('message') }}
            </div>
        @endif
         @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded">
                {{ session('error') }}
            </div>
        @endif
         @if (session()->has('provider_message'))
            <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-100 rounded">
                {{ session('provider_message') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="mb-4">
            <x-label for="statusFilter" value="{{ __('Filter Provider Status') }}" class="dark:text-gray-300"/>
            <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full md:w-1/4 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">All</option>
                <option value="pending">Pending Approval</option>
                <option value="approved">Approved</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>

        {{-- Provider List --}}
        <div class="overflow-x-auto mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Providers</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Provider</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docs Pending</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docs Approved</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docs Rejected</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($providers as $provider)
                        <tr class="{{ $selectedProvider && $selectedProvider->id === $provider->id ? 'bg-indigo-50 dark:bg-indigo-900' : '' }}">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $provider->name }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($provider->status === 'approved') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 @endif
                                    @if($provider->status === 'pending') bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 @endif
                                    @if($provider->status === 'suspended') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 @endif
                                ">
                                    {{ ucfirst($provider->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $provider->pending_documents_count }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $provider->approved_documents_count }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $provider->rejected_documents_count }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="viewDocuments({{ $provider->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $selectedProvider && $selectedProvider->id === $provider->id ? 'Viewing' : 'View Docs' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No providers found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $providers->links() }}
        </div>

        {{-- Document Verification Section --}}
        @if($selectedProvider)
            <hr class="my-8 dark:border-gray-700">
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Verify Documents for: {{ $selectedProvider->name }}</h3>
                    <button wire:click="clearSelectedProvider" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&times; Close</button>
                </div>

                 @if (session()->has('doc_message'))
                    <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-100 rounded text-sm">
                        {{ session('doc_message') }}
                    </div>
                @endif
                 @if (session()->has('doc_error'))
                    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded text-sm">
                        {{ session('doc_error') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                         <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Document</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Uploaded</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                         <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($selectedProvider->providerDocuments as $doc)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ Str::title(str_replace('_', ' ', $doc->document_type)) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($doc->url)
                                            <a href="{{ $doc->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View/Download</a>
                                        @else
                                            File not found
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($doc->status === 'approved') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 @endif
                                            @if($doc->status === 'pending') bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 @endif
                                            @if($doc->status === 'rejected') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 @endif
                                        ">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $doc->admin_notes ?? '-' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                         @if($doc->status !== 'approved')
                                            <button wire:click="approveDocument({{ $doc->id }})" class="text-green-600 hover:text-green-900">Approve</button>
                                        @endif
                                        @if($doc->status !== 'rejected')
                                            <button wire:click="showRejectDocumentModal({{ $doc->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No documents uploaded by this provider yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>
            </div>
        @endif

    </div>

     {{-- Reject Document Modal --}}
    <x-dialog-modal wire:model.live="showRejectModal">
        <x-slot name="title">
            Reject Document
        </x-slot>

        <x-slot name="content">
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Please provide a reason for rejecting this document.</p>
            <div>
                <x-label for="rejectReason" value="{{ __('Rejection Reason') }}" class="dark:text-gray-300"/>
                <textarea id="rejectReason" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model.defer="rejectReason" rows="3"></textarea>
                <x-input-error for="rejectReason" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeRejectModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="rejectDocument()" wire:loading.attr="disabled">
                {{ __('Reject Document') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>

</div>
