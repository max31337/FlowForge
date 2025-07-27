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

    <!-- Dashboard Navigation Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('overview')" 
                        id="overview-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
                <button onclick="showTab('projects')" 
                        id="projects-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-project-diagram mr-2"></i>Projects
                </button>
                <button onclick="showTab('tasks')" 
                        id="tasks-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-tasks mr-2"></i>Tasks
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div id="overview-content" class="tab-content">
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

            <!-- Projects Tab -->
            <div id="projects-content" class="tab-content hidden">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-project-diagram mr-2 text-blue-500"></i>
                        Project Management
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Manage all projects for {{ tenancy()->tenant->name ?? 'your organization' }}.
                    </p>
                </div>
                
                @livewire('tenant.projects.project-list')
            </div>

            <!-- Tasks Tab -->
            <div id="tasks-content" class="tab-content hidden">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-tasks mr-2 text-blue-500"></i>
                        Task Management
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Manage all tasks for {{ tenancy()->tenant->name ?? 'your organization' }}.
                    </p>
                </div>
                
                @livewire('tenant.tasks.task-list')
            </div>
        </div>
    </div>

    <!-- Tab Switching JavaScript -->
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'dark:border-blue-400');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active state to selected tab button
            const activeButton = document.getElementById(tabName + '-tab');
            activeButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'dark:border-blue-400');
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set overview tab as active by default
            showTab('overview');
        });
    </script>
</div>
@endsection
