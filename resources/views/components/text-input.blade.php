@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:border-[#FF2D20] dark:focus:border-[#FF2D20] focus:ring-[#FF2D20] dark:focus:ring-[#FF2D20] rounded-md shadow-sm backdrop-blur-sm']) }}>
