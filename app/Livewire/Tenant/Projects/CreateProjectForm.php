<?php

namespace App\Livewire\Tenant\Projects;

use App\Livewire\TenantAwareComponent;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class CreateProjectForm extends TenantAwareComponent
{
    public $projectForm = [
        'name' => '',
        'description' => '',
        'category_id' => null,
        'status' => 'active',
    ];

    public $showModal = false;

    protected $listeners = [
        'open-project-modal' => 'openModal',
        'close-project-modal' => 'closeModal',
    ];

    protected function rules()
    {
        $tenantId = $this->getTenantId();

        if (!$tenantId) {
            return [];
        }

        return [
            'projectForm.name' => 'required|string|max:255',
            'projectForm.description' => 'nullable|string',
            'projectForm.status' => 'required|in:active,on_hold,completed,cancelled',
            'projectForm.category_id' => 'nullable|exists:categories,id,tenant_id,' . $tenantId,
        ];
    }

    public function openModal()
    {
        $this->resetProjectForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetProjectForm();
        $this->resetValidation();
        $this->dispatch('$refresh');
    }

    public function createProject()
    {
        try {
            $this->validate();

            $data = $this->projectForm;
            $data['created_by'] = Auth::id();

            $tenantId = $this->getTenantId();
            if (!$tenantId) {
                $host = request()->getHost();
                $tenant = \App\Models\Tenant::whereHas('domains', function ($q) use ($host) {
                    $q->where('domain', $host);
                })->first();
                if ($tenant) {
                    $tenantId = $tenant->id;
                } else {
                    throw new \Exception('Unable to determine tenant context');
                }
            }

            $data['tenant_id'] = $tenantId;

            $data = array_filter($data, function ($value, $key) {
                return in_array($key, ['tenant_id', 'created_by']) || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            Project::create($data);

            $this->dispatch('toast', type: 'success', title: 'Project Created', message: 'Project created successfully!', duration: 4000);

            $this->closeModal();
            $this->dispatch('project-created');
            $this->dispatch('refresh-project-list');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Create Failed', message: 'Unable to create project: ' . $e->getMessage(), duration: 6000);
        }
    }

    private function resetProjectForm()
    {
        $this->projectForm = [
            'name' => '',
            'description' => '',
            'category_id' => null,
            'status' => 'active',
        ];
    }

    #[Computed]
    public function categories()
    {
        $tenantId = $this->getTenantId();
        if (!$tenantId) {
            return collect();
        }

        return Category::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.tenant.projects.create-project-form');
    }
}
