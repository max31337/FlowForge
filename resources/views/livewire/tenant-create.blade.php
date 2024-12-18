<div class="p-6 max-w-4xl mx-auto bg-white rounded-lg shadow-md" x-data="{ showSuccessMessage: false }">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Create Tenant</h2>

    @if (session()->has('message'))
        <div 
            class="alert alert-success mb-4 bg-green-100 text-green-800 border-l-4 border-green-500 p-4" 
            x-show="showSuccessMessage"
            x-init="showSuccessMessage = true; setTimeout(() => showSuccessMessage = false, 3000)">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="createTenant" x-data="{ name: '', slug: '', domain: '', email: '', password: '', confirmPassword: '' }">

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Tenant Name</label>
            <input 
                type="text" 
                id="name" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                wire:model="name"
                x-model="name"
            />
            <span class="text-red-500 text-xs mt-1" x-show="name === ''">
                Tenant name is required.
            </span>
            @error('name') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
            <input 
                type="text" 
                id="slug" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                wire:model="slug"
                x-model="slug"
            />
            @error('slug') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="domain" class="block text-sm font-medium text-gray-700">Domain (e.g., https://acme-inc.com)</label>
            <input 
                type="text" 
                id="domain" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                wire:model="domain"
                x-model="domain"
            />
            @error('domain') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Admin Email</label>
            <input 
                type="email" 
                id="email" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                wire:model="email"
                x-model="email"
            />
            @error('email') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input 
                type="password" 
                id="password" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                wire:model="password"
                x-model="password"
            />
            @error('password') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input 
                type="password" 
                id="password_confirmation" 
                class="form-input mt-2 block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
                x-model="confirmPassword"
            />
        </div>

        <button 
            type="submit" 
            class="btn btn-primary w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300"
            x-bind:disabled="password !== confirmPassword || password === ''">
            Create Tenant
        </button>
    </form>
</div>
