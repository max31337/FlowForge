<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile') }}
            </h2>

            <!-- Theme Toggle Button -->
            <div x-data="{ darkMode: @entangle('darkMode') }" x-init="
                darkMode = localStorage.getItem('theme') === 'dark';
                $watch('darkMode', value => {
                    localStorage.setItem('theme', value ? 'dark' : 'light');
                    @this.set('darkMode', value);  // Update Livewire property
                })
            ">
                <button @click="darkMode = !darkMode" :class="{ 'bg-gray-800 text-white': darkMode, 'bg-white text-black': !darkMode }" class="p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8-8h1m-16 0h1m2.293-5.707l-.707.707m12.828-.707l-.707.707M16.243 5.757l-.707-.707m-8.486 0l-.707.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
