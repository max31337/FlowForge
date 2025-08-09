<?php

namespace App\Livewire\Tenant;

use App\Livewire\TenantAwareComponent;

class QuickActions extends TenantAwareComponent
{
    public function render()
    {
        // Use TenantAwareComponent method for safe tenant access
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return view('livewire.tenant.quick-actions', ['actions' => []]);
        }
        
        $actions = $this->getAvailableActions();
        
        return view('livewire.tenant.quick-actions', compact('actions'));
    }

    private function getAvailableActions(): array
    {
        $user = auth()->user();
        $actions = [];
        
        $tenantId = $this->getTenantId();

        // Only show actions if user belongs to current tenant
        if (!$user || !$tenantId || $user->getAttribute('tenant_id') !== $tenantId) {
            return [];
        }

        // Project management actions
    if ($user->hasPermission('manage_projects')) {
            $actions[] = [
                'title' => 'New Project',
                'description' => 'Create a new project for your team',
                'icon' => 'fas fa-project-diagram',
                'color' => 'blue',
                'action' => 'create-project',
        // Send to Projects; page can open a modal or have a create button
        'url' => route('tenant.projects.index'),
            ];
        }

        // Task management actions
    if ($user->hasPermission('manage_tasks')) {
            $actions[] = [
                'title' => 'New Task',
                'description' => 'Add a task to an existing project',
                'icon' => 'fas fa-plus-circle',
                'color' => 'green',
                'action' => 'create-task',
        // Send to Tasks; page can open a create form/modal
        'url' => route('tenant.tasks.index'),
            ];
        }

        // User management actions
        if ($user->hasPermission('manage_users')) {
            $actions[] = [
                'title' => 'Manage Users',
                'description' => 'Invite and manage team members',
                'icon' => 'fas fa-users',
                'color' => 'purple',
                'action' => 'manage-users',
                'url' => route('tenant.users.index'),
            ];
        }

        // Category management actions
        if ($user->hasPermission('manage_categories')) {
            $actions[] = [
                'title' => 'Categories',
                'description' => 'Organize tasks with categories',
                'icon' => 'fas fa-tags',
                'color' => 'indigo',
                'action' => 'manage-categories',
                'url' => '#', // TODO: Replace with actual route
            ];
        }

        // Reports access
        if ($user->hasPermission('view_reports')) {
            $actions[] = [
                'title' => 'View Reports',
                'description' => 'Analytics and progress reports',
                'icon' => 'fas fa-chart-bar',
                'color' => 'yellow',
                'action' => 'view-reports',
                'url' => '#', // TODO: Replace with actual route
            ];
        }

        // Settings access (for owners/admins)
        if ($user->hasPermission('manage_tenant_settings')) {
            $actions[] = [
                'title' => 'Settings',
                'description' => 'Manage tenant configuration',
                'icon' => 'fas fa-cog',
                'color' => 'gray',
                'action' => 'manage-settings',
                'url' => '#', // TODO: Replace with actual route
            ];
        }

        return $actions;
    }

    public function handleAction($action)
    {
        switch ($action) {
            case 'create-project':
                $this->dispatch('open-project-modal');
                break;
            case 'create-task':
                $this->dispatch('open-task-modal');
                break;
            case 'manage-users':
                return redirect()->route('tenant.users.index');
            default:
                $this->dispatch('info', 'This feature is coming soon!');
        }
    }
}
