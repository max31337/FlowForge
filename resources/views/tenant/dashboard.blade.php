@extends('layouts.sidebar-app')

@section('content')
<div class="container mx-auto px-4 py-8">
	<!-- Header -->
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
		<div>
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Team Dashboard</h1>
			@if(tenancy()->initialized)
				<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
					<i class="fas fa-building mr-1"></i>{{ tenancy()->tenant->name }}
				</p>
			@endif
		</div>
		<div class="flex items-center gap-2">
			@can('read_projects')
				<a href="{{ route('tenant.projects.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
					<i class="fas fa-project-diagram mr-2"></i>Projects
				</a>
			@endcan
			@can('read_tasks')
				<a href="{{ route('tenant.tasks.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
					<i class="fas fa-tasks mr-2"></i>Tasks
				</a>
			@endcan
			@can('manage_users')
				<a href="{{ route('tenant.users.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
					<i class="fas fa-users mr-2"></i>Users
				</a>
			@endcan
		</div>
	</div>

	<!-- Stats + Info Banner (Livewire) -->
	<livewire:tenant.dashboard-stats />

	<!-- Quick Actions + Lightweight Chart -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
		<div class="lg:col-span-2 space-y-8">
			<livewire:tenant.quick-actions />
			<livewire:tenant.task-status-distribution />
		</div>

	<!-- Lightweight chart card to visualize throughput (placeholder sparkline) -->
		<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800">
			<div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Throughput (7 days)</h3>
				<p class="text-xs text-gray-500 dark:text-gray-400">Tasks completed per day</p>
			</div>
			<div class="p-6">
				<!-- Sparkline: simple, no external libs; values illustrative until reports module is wired -->
				<div class="h-24 w-full">
					<svg viewBox="0 0 200 60" class="w-full h-full">
						<defs>
							<linearGradient id="spark" x1="0" x2="0" y1="0" y2="1">
								<stop offset="0%" stop-color="#22c55e" stop-opacity="0.8" />
								<stop offset="100%" stop-color="#22c55e" stop-opacity="0.1" />
							</linearGradient>
						</defs>
						<!-- Area -->
						<path d="M0 50 L20 42 L40 45 L60 30 L80 35 L100 20 L120 28 L140 18 L160 24 L180 12 L200 16 L200 60 L0 60 Z"
							  fill="url(#spark)" />
						<!-- Line -->
						<polyline points="0,50 20,42 40,45 60,30 80,35 100,20 120,28 140,18 160,24 180,12 200,16"
								  fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" />
					</svg>
				</div>
				<div class="mt-4 grid grid-cols-3 gap-4 text-sm">
					<div>
						<div class="text-gray-500 dark:text-gray-400">Avg/day</div>
						<div class="font-semibold text-gray-900 dark:text-white">—</div>
					</div>
					<div>
						<div class="text-gray-500 dark:text-gray-400">Best</div>
						<div class="font-semibold text-gray-900 dark:text-white">—</div>
					</div>
					<div>
						<div class="text-gray-500 dark:text-gray-400">Trend</div>
						<div class="font-semibold text-emerald-600">↗</div>
					</div>
				</div>
				<p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Connect Reports to power this chart.</p>
			</div>
		</div>
	</div>

	<!-- Recent activity -->
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
		<livewire:tenant.recent-projects />
		<livewire:tenant.recent-tasks />
	</div>

	@if(config('app.debug'))
		<div class="mt-8">
			<livewire:tenant.debug-info :debug="['route' => request()->path(), 'tenant' => tenancy()->initialized ? tenancy()->tenant->name : 'none']" />
		</div>
	@endif
</div>
@endsection

