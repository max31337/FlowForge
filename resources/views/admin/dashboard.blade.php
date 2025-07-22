@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            FlowForge Admin Dashboard
        </h1>
        <a href="{{ route('admin.tenants.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
            <i class="fas fa-plus mr-2"></i>Create Tenant
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                    <i class="fas fa-building text-blue-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Tenants</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_tenants'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Tenants</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['active_tenants'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                    <i class="fas fa-users text-purple-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                    <i class="fas fa-chart-bar text-yellow-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Rate</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Tenants -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Tenants</h3>
            </div>
            <div class="p-6">
                @if($stats['recent_tenants']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_tenants'] as $tenant)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $tenant->domains->first()->domain ?? 'No domain' }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $tenant->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full capitalize">
                                        {{ $tenant->plan }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.tenants.index') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View all tenants →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No tenants found.</p>
                @endif
            </div>
        </div>

        <!-- Plan Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Plan Distribution</h3>
            </div>
            <div class="p-6">
                @if(count($planStats) > 0)
                    <div class="space-y-4">
                        @foreach($planStats as $plan => $count)
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $plan }}</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $stats['total_tenants'] > 0 ? ($count / $stats['total_tenants']) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No plan data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
