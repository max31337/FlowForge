@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white">
    <!-- Abstract Background Elements -->
    <x-abstract-bg variant="geometric1" position="top-right" />
    <x-abstract-bg variant="dots" position="bottom-left" />
    <x-abstract-bg variant="organic" position="center" />
    
    <div class="relative z-10 container mx-auto px-4 py-8">
        <!-- Tenant Header -->
        <x-pines-card class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ tenancy()->tenant->name ?? 'Dashboard' }}
                    </h1>
                    <p class="text-gray-300 mt-2">
                        Welcome back, {{ auth()->user()->name }}! 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-300 ml-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ auth()->user()->role->name ?? 'No Role' }}
                        </span>
                    </p>
                    @if(tenancy()->initialized)
                        <div class="flex items-center mt-3 text-sm text-gray-400 space-x-6">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Tenant: {{ tenancy()->tenant->slug }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                                </svg>
                                <span>Domain: {{ request()->getHost() }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 012 0v4m6 0V3a1 1 0 012 0v4M8 11h8a4 4 0 110 8H8a4 4 0 110-8z"/>
                                </svg>
                                <span>Today: {{ now()->format('M d, Y') }}</span>
                            </div>
                            @if(config('app.debug'))
                                <div class="flex items-center text-xs bg-blue-500/20 text-blue-300 px-2 py-1 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Tenant ID: {{ tenant('id') }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-red-500/20 text-red-300 rounded border border-red-500/30">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <strong>Warning:</strong> Tenancy not initialized! This may cause dashboard issues.
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    @if(tenancy()->initialized && tenancy()->tenant->active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-300 mb-2">
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
    <x-pines-card class="mb-8">
        <div class="border-b border-red-500/30">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('overview')" 
                        id="overview-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-400 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Overview
                </button>
                <button onclick="showTab('projects')" 
                        id="projects-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-red-300 hover:border-red-500/50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Projects
                </button>
                <button onclick="showTab('tasks')" 
                        id="tasks-tab"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-red-300 hover:border-red-500/50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Tasks
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
                <x-pines-card class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-2 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                Project Management
                            </h2>
                            <p class="text-gray-400">
                                Manage all projects for {{ tenancy()->tenant->name ?? 'your organization' }}.
                            </p>
                        </div>
                        @can('create_projects')
                            <x-pines-button 
                                onclick="document.querySelector('[wire\\:click=\"openCreateModal\"]').click()"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                                Create Project
                            </x-pines-button>
                        @endcan
                    </div>
                </x-pines-card>
                
                @livewire('tenant.projects.project-list')
            </div>

            <!-- Tasks Tab -->
            <div id="tasks-content" class="tab-content hidden">
                <x-pines-card class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-2 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Task Management
                            </h2>
                            <p class="text-gray-400">
                                Manage all tasks for {{ tenancy()->tenant->name ?? 'your organization' }}.
                            </p>
                        </div>
                        @can('create_tasks')
                            <x-pines-button 
                                onclick="document.querySelector('[wire\\:click=\"openCreateModal\"]').click()"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                                Create Task
                            </x-pines-button>
                        @endcan
                    </div>
                </x-pines-card>
                
                @livewire('tenant.tasks.task-list')
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
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
