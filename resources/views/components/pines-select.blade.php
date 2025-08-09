@props(['label' => '', 'error' => '', 'required' => false, 'placeholder' => 'Select an option'])

<div class="space-y-2">
    @if($label)
        <label {{ $attributes->only('for') }} class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-600 dark:text-red-400">*</span>
            @endif
        </label>
    @endif
    
    <select {{ $attributes->merge([
        'class' => 'block w-full px-4 py-3 rounded-lg backdrop-blur-sm transition-all duration-200 '
            . 'bg-white text-gray-900 border border-gray-300 '
            . 'focus:ring-2 focus:ring-red-500 focus:border-red-500 hover:border-gray-400 '
            . 'dark:bg-gray-800/80 dark:text-white dark:border-red-500/40 '
            . 'dark:hover:border-red-500/60 '
            . ($error ? ' border-red-500 ring-2 ring-red-500/50' : '')
    ]) }}>
        <option value="" class="bg-white text-gray-500 dark:bg-gray-800 dark:text-gray-300">{{ $placeholder }}</option>
        {{ $slot }}
    </select>
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
