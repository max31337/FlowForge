@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Tenant Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i>
                    {{ tenancy()->tenant->name ?? 'Dashboard' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Welcome back, {{ auth()->user()->name }}! 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ml-2">
                        <i class="fas fa-user-tag mr-1"></i>
                        {{ auth()->user()->role->name ?? 'No Role' }}
                    </span>
                </p>
                @if(tenancy()->initialized)
                    <div class="flex items-center mt-3 text-sm text-gray-500 dark:text-gray-400 space-x-6">
                        <div class="flex items-center">
                            <i class="fas fa-building mr-2"></i>
                            <span>Tenant: {{ tenancy()->tenant->slug }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-globe mr-2"></i>
                            <span>Domain: {{ request()->getHost() }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>Today: {{ now()->format('M d, Y') }}</span>
                        </div>
                        @if(config('app.debug'))
                            <div class="flex items-center text-xs bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">
                                <i class="fas fa-bug mr-1"></i>
                                <span>Tenant ID: {{ tenant('id') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-3 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> Tenancy not initialized! This may cause dashboard issues.
                    </div>
                @endif
            </div>
            <div class="text-right">
                @if(tenancy()->initialized && tenancy()->tenant->active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-2">
                        <i class="fas fa-check-circle mr-1"></i>Active Tenant
                    </span>
                @endif
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-fingerprint mr-1"></i>ID: {{ tenant('id') }}
                </div>
                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    <i class="fas fa-clock mr-1"></i>{{ now()->format('g:i A T') }}
                </div>
            </div>
        </div>
    </div>


    <!-- Livewire Dashboard Stats Component -->
    @livewire('tenant.dashboard-stats')

    <!-- Main Dashboard Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Projects Component -->
        @livewire('tenant.recent-projects')

        <!-- Recent Tasks Component -->
        @livewire('tenant.recent-tasks')
    </div>

    <!-- Quick Actions Component -->
    @livewire('tenant.quick-actions')
</div>
@endsection
