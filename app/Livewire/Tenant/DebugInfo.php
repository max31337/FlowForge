<?php

namespace App\Livewire\Tenant;

use App\Livewire\TenantAwareComponent;

class DebugInfo extends TenantAwareComponent
{
    public function render()
    {
        $tenantId = $this->getTenantId();
        
        $debug = [
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => $tenantId ?? 'N/A',
            'tenant_name' => $this->safeTenant('name') ?? 'N/A',
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'N/A',
            'user_tenant_id' => auth()->user() ? auth()->user()->getAttribute('tenant_id') : 'N/A',
            'current_time' => now()->toDateTimeString(),
        ];

        return view('livewire.tenant.debug-info', compact('debug'));
    }
}
