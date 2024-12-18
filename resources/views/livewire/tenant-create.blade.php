<div class="p-6 max-w-4xl mx-auto bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Create Tenant</h2>

    @if (session()->has('message'))
        <div class="alert alert-success mb-4 bg-green-100 text-green-800 border-l-4 border-green-500 p-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="createTenant">

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Tenant Name</label>
            <input type="text" id="name" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="name" />
            @error('name') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
            <input type="text" id="slug" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="slug" />
            @error('slug') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="domain" class="block text-sm font-medium text-gray-700">Domain (e.g., https://acme-inc.com)</label>
            <input type="text" id="domain" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="domain" />
            @error('domain') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Admin Email</label>
            <input type="email" id="email" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="email" />
            @error('email') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="password" />
            @error('password') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" wire:model="password_confirmation" />
        </div>

        <button type="submit" class="btn btn-primary w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
            Create Tenant
        </button>
    </form>
</div>
