<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#FF2D20] dark:bg-[#FF2D20] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#FF2D20]/90 dark:hover:bg-[#FF2D20]/90 focus:bg-[#FF2D20]/90 dark:focus:bg-[#FF2D20]/90 active:bg-[#FF2D20]/80 dark:active:bg-[#FF2D20]/80 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] dark:focus:ring-[#FF2D20] focus:ring-offset-2 dark:focus:ring-offset-black transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
