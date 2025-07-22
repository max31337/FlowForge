@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 py-16">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <div class="mb-8">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-4">
                    Welcome to
                </h1>
                <h2 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    {{ tenancy()->tenant->name ?? 'Your Organization' }}
                </h2>
            </div>
            
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                Your comprehensive project management and collaboration platform. 
                Streamline your workflow, manage tasks, and boost team productivity.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('tenant.dashboard') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-white hover:bg-gray-50 text-blue-600 font-semibold py-3 px-8 rounded-lg border border-blue-600 transition duration-200 transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>
                        Get Started
                    </a>
                @endauth
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-200">
                <div class="text-blue-600 mb-4">
                    <i class="fas fa-tasks text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Task Management</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Organize, prioritize, and track your tasks with intuitive project boards and real-time updates.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-200">
                <div class="text-green-600 mb-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Team Collaboration</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Work together seamlessly with role-based permissions, comments, and file sharing.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-200">
                <div class="text-purple-600 mb-4">
                    <i class="fas fa-chart-bar text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Analytics & Reports</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Get insights into your team's productivity with comprehensive analytics and reporting.
                </p>
            </div>
        </div>

        @if(tenancy()->initialized)
        <!-- Tenant Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organization Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Organization:</span>
                        <p class="font-medium text-gray-900 dark:text-white">{{ tenancy()->tenant->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Domain:</span>
                        <p class="font-medium text-gray-900 dark:text-white">{{ request()->getHost() }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <p class="font-medium text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Active
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
