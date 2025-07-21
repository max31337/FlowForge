<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 backdrop-blur-sm border border-gray-200 dark:border-zinc-800 overflow-hidden shadow-xl sm:rounded-xl ring-1 ring-white/[0.05] dark:ring-zinc-800">
                <div class="p-6 text-gray-900 dark:text-white">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
