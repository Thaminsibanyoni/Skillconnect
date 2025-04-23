<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('FAQ Management') }}
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

        {{-- Button to open modal --}}
        <div class="mb-4">
            <x-button wire:click="create()">
                {{ __('Add New FAQ') }}
            </x-button>
        </div>

        {{-- FAQs Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Question</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Published</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($faqs as $faq)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $faq->display_order }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $faq->category ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ Str::limit($faq->question, 60) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $faq->is_published ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                    {{ $faq->is_published ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $faq->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                <button wire:click="delete({{ $faq->id }})" wire:confirm="Are you sure you want to delete this FAQ?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No FAQs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $faqs->links() }}
        </div>
    </div>

     {{-- Create/Edit Modal --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $faqId ? 'Edit FAQ' : 'Add New FAQ' }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                    <x-label for="category" value="{{ __('Category (Optional)') }}" class="dark:text-gray-300"/>
                    <x-input id="category" type="text" class="mt-1 block w-full" wire:model.defer="category" placeholder="e.g., General, Provider"/>
                    <x-input-error for="category" class="mt-2" />
                </div>
                 <div>
                    <x-label for="display_order" value="{{ __('Display Order') }}" class="dark:text-gray-300"/>
                    <x-input id="display_order" type="number" class="mt-1 block w-full" wire:model.defer="display_order" />
                    <x-input-error for="display_order" class="mt-2" />
                </div>
            </div>
             <div class="mt-4">
                <x-label for="question" value="{{ __('Question') }}" class="dark:text-gray-300"/>
                <textarea id="question" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model.defer="question" rows="2"></textarea>
                <x-input-error for="question" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-label for="answer" value="{{ __('Answer') }}" class="dark:text-gray-300"/>
                {{-- Consider using a Rich Text Editor component here --}}
                <textarea id="answer" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model.defer="answer" rows="6"></textarea>
                <x-input-error for="answer" class="mt-2" />
            </div>
             <div class="mt-4">
                 <label for="is_published" class="flex items-center">
                    <x-checkbox id="is_published" wire:model.defer="is_published" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Published') }}</span>
                </label>
                <x-input-error for="is_published" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="save()" wire:loading.attr="disabled">
                {{ $faqId ? __('Update FAQ') : __('Save FAQ') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
