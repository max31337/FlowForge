<div x-data="{ darkMode: @entangle('darkMode') }" x-init="
    darkMode = localStorage.getItem('theme') === 'dark';
    $watch('darkMode', value => {
        localStorage.setItem('theme', value ? 'dark' : 'light');
        @this.set('darkMode', value);  // Update Livewire property
    })
">
    <button 
        @click="darkMode = !darkMode" 
        :class="{ 'bg-gray-800 text-white': darkMode, 'bg-white text-black': !darkMode }">
        Toggle Theme
    </button>
</div>
