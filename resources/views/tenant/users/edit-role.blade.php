@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Edit User Role
        </h1>
        <a href="{{ route('tenant.users.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- User Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 h-12 w-12">
                    <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 dark:text-gray-300 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p><strong>Current Role:</strong> 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($user->role)
                            @if($user->role->name === 'owner') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif($user->role->name === 'admin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @elseif($user->role->name === 'manager') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ $user->role->name ?? 'No Role' }}
                    </span>
                </p>
                <p class="mt-2"><strong>Member since:</strong> {{ $user->created_at->format('M j, Y') }}</p>
            </div>
        </div>

        <!-- Role Update Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Role</h3>
            
            <form method="POST" action="{{ route('tenant.users.update-role', $user) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-6">
                    <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select New Role
                    </label>
                    <select id="role_id" name="role_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" 
                                    {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} - {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Descriptions -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Role Permissions:</h4>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        @foreach($roles as $role)
                            <div class="role-description" data-role="{{ $role->id }}" style="display: none;">
                                <strong>{{ $role->name }}:</strong> {{ $role->description }}
                                @if($role->permissions->count() > 0)
                                    <br><em>Permissions:</em> {{ $role->permissions->pluck('name')->join(', ') }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('tenant.users.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i>Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const roleDescriptions = document.querySelectorAll('.role-description');
    
    function showRoleDescription() {
        // Hide all descriptions
        roleDescriptions.forEach(desc => desc.style.display = 'none');
        
        // Show selected role description
        const selectedRole = roleSelect.value;
        if (selectedRole) {
            const selectedDesc = document.querySelector(`[data-role="${selectedRole}"]`);
            if (selectedDesc) {
                selectedDesc.style.display = 'block';
            }
        }
    }
    
    // Show initial description
    showRoleDescription();
    
    // Update on change
    roleSelect.addEventListener('change', showRoleDescription);
});
</script>
@endsection
