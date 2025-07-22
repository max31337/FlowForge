@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.tenants.show', $tenant) }}" 
           class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Tenant: {{ $tenant->name }}</h1>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tenant Information</h2>
            </div>

            <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization Name *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $tenant->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Slug *
                    </label>
                    <input type="text" 
                           name="slug" 
                           id="slug" 
                           value="{{ old('slug', $tenant->slug) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Warning: Changing the slug may break existing links and domains.
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $tenant->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plan -->
                <div>
                    <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Plan *
                    </label>
                    <select name="plan" 
                            id="plan" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="free" {{ old('plan', $tenant->plan) === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="pro" {{ old('plan', $tenant->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                        <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                    @error('plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="active" 
                               value="1"
                               {{ old('active', $tenant->active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    @error('active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Inactive tenants cannot access their application.
                    </p>
                </div>

                <!-- Current Domains Info -->
                @if($tenant->domains->count() > 0)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Current Domains</h4>
                        <ul class="space-y-1">
                            @foreach($tenant->domains as $domain)
                                <li class="text-sm text-blue-700 dark:text-blue-300">
                                    <i class="fas fa-globe mr-2"></i>{{ $domain->domain }}
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                            Domain management will be available in a future update.
                        </p>
                    </div>
                @endif

                <!-- Submit Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <button type="button" 
                                onclick="confirmDelete()"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition duration-150 ease-in-out">
                            <i class="fas fa-trash mr-2"></i>Delete Tenant
                        </button>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                            Update Tenant
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this tenant? This action cannot be undone and will permanently delete all tenant data including users, projects, and tasks.')) {
        if (confirm('This will permanently delete the tenant "{{ $tenant->name }}" and all associated data. Type the tenant name to confirm.')) {
            const tenantName = prompt('Please type "{{ $tenant->name }}" to confirm deletion:');
            if (tenantName === '{{ $tenant->name }}') {
                document.getElementById('deleteForm').submit();
            } else {
                alert('Tenant name does not match. Deletion cancelled.');
            }
        }
    }
}
</script>
@endsection
