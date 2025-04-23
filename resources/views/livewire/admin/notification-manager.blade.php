<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Send Notification') }}
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

        <form wire:submit.prevent="sendNotification">
            <div class="grid grid-cols-1 gap-6">
                {{-- Target Group --}}
                <div>
                    <x-label for="targetGroup" value="{{ __('Send To') }}" class="dark:text-gray-300"/>
                    <select id="targetGroup" wire:model="targetGroup" class="block mt-1 w-full md:w-1/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="all">All Users</option>
                        <option value="seekers">Seekers Only</option>
                        <option value="providers">Providers Only</option>
                    </select>
                    <x-input-error for="targetGroup" class="mt-2" />
                </div>

                {{-- Title --}}
                <div>
                    <x-label for="title" value="{{ __('Notification Title') }}" class="dark:text-gray-300"/>
                    <x-input id="title" type="text" class="mt-1 block w-full" wire:model.defer="title" />
                    <x-input-error for="title" class="mt-2" />
                </div>

                 {{-- Message --}}
                <div>
                    <x-label for="message" value="{{ __('Notification Message') }}" class="dark:text-gray-300"/>
                    <textarea id="message" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model.defer="message" rows="5"></textarea>
                    <x-input-error for="message" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit" wire:loading.attr="disabled" wire:target="sendNotification">
                    {{ __('Send Notification') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
