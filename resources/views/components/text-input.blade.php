@props(['disabled' => false])

<input
	@disabled($disabled)
	{{
		$attributes->merge([
			'class' => 'w-full border border-gray-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF2D20] dark:focus:border-[#FF2D20] focus:ring-[#FF2D20] dark:focus:ring-[#FF2D20] rounded-md shadow-sm backdrop-blur-sm transition-colors duration-200'
		])
	}}
>
