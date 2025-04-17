<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Page Management (CMS)') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        {{-- Button to open modal --}}
        <div class="mb-4">
            <x-button wire:click="create()">
                {{ __('Add New Page') }}
            </x-button>
        </div>

        {{-- Pages Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pages as $page)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $page->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">/{{ $page->slug }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($page->status) }}
                                </span>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $page->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete({{ $page->id }})" wire:confirm="Are you sure you want to delete this page?" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $pages->links() }}
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $pageId ? 'Edit Page' : 'Add New Page' }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label for="title" value="{{ __('Title') }}" />
                <x-input id="title" type="text" class="mt-1 block w-full" wire:model.live.debounce.500ms="title" /> {{-- Use live update for slug generation --}}
                <x-input-error for="title" class="mt-2" />
            </div>
             <div class="mt-4">
                <x-label for="slug" value="{{ __('Slug (URL)') }}" />
                <x-input id="slug" type="text" class="mt-1 block w-full bg-gray-100" wire:model.defer="slug" /> {{-- Consider making readonly or allowing manual override --}}
                <x-input-error for="slug" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-label for="content" value="{{ __('Content (HTML allowed)') }}" />
                {{-- Consider using a Rich Text Editor component here --}}
                <textarea id="content" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="content" rows="10"></textarea>
                <x-input-error for="content" class="mt-2" />
            </div>
             <div class="mt-4">
                <x-label for="status" value="{{ __('Status') }}" />
                <select id="status" wire:model.defer="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
                <x-input-error for="status" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="save()" wire:loading.attr="disabled">
                {{ $pageId ? __('Update Page') : __('Save Page') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
