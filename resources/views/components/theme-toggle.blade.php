<div x-data="themeToggle()" x-init="init()" class="relative">
    <button 
        @click="toggleTheme()" 
        class="p-2 rounded-lg bg-white dark:bg-zinc-900 text-gray-700 dark:text-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:ring-black/20 dark:hover:ring-zinc-700 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] dark:focus:ring-[#FF2D20] focus:ring-offset-2 dark:focus:ring-offset-black transition duration-300 backdrop-blur-sm"
        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <!-- Sun Icon (Light Mode) -->
        <svg x-show="!isDark" x-transition class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg x-show="isDark" x-transition class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
            </path>
        </svg>
    </button>
</div>

<script>
function themeToggle() {
    return {
        isDark: false,
        
        init() {
            // Check for saved theme preference or default to system preference
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                this.isDark = true;
                document.documentElement.classList.add('dark');
            } else {
                this.isDark = false;
                document.documentElement.classList.remove('dark');
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    this.isDark = e.matches;
                    this.updateTheme();
                }
            });
        },
        
        toggleTheme() {
            this.isDark = !this.isDark;
            this.updateTheme();
            
            // Save preference to localStorage
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        },
        
        updateTheme() {
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}
</script>
