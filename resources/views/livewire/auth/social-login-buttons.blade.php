<div class="flex space-between justify-between items-center mt-4 space-x-4">
    <a href="{{ url('auth/google') }}" class="relative group btn btn-outline d-inline-flex align-items-center justify-content-center bg-white px-6 py-2 rounded-md border border-gray-300 shadow-sm hover:bg-gray-100 ms-3" x-data="{ showTooltip: false }" @mouseover="showTooltip = true" @mouseleave="showTooltip = false">
        <img src="https://lh3.googleusercontent.com/COxitqgJr1sJnIDe8-jiKhxDx1FrYbtRHKJ9z_hELisAlapwE9LUPh6fcXIfb5vwpbMl4xl9H9TRFPc5NOO8Sb3VSgIBrfRYvW6cUA"
             alt="Google Logo" class="h-5 w-8">
        <span class="absolute bg-gray-800 text-white text-xs rounded px-8 py-4 bottom-full mb-2 transition-opacity duration-300" x-show="showTooltip" x-transition>
            {{ __('Continue with Google') }}
        </span>
    </a>
    <a href="{{ url('auth/github') }}" class="relative group btn btn-outline d-inline-flex align-items-center justify-content-center bg-white px-6 py-2 rounded-md border border-gray-300 shadow-sm hover:bg-gray-100 ms-3" x-data="{ showTooltip: false }" @mouseover="showTooltip = true" @mouseleave="showTooltip = false">
        <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png"
             alt="GitHub Logo" class="h-5 w-8">
        <span class="absolute bg-gray-800 text-white text-xs rounded px-8 py-4 bottom-full mb-2 transition-opacity duration-300" x-show="showTooltip" x-transition>
            {{ __('Continue with GitHub') }}
        </span>
    </a>
</div>
