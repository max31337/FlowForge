<?php

namespace App\Livewire\Tenant;

use Livewire\Component;

class DebugInfo extends Component
{
    public function render()
    {
        $debug = [
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenancy()->initialized ? tenant('id') : 'N/A',
            'tenant_name' => tenancy()->initialized ? tenancy()->tenant->getAttribute('name') : 'N/A',
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'N/A',
            'user_tenant_id' => auth()->user() ? auth()->user()->getAttribute('tenant_id') : 'N/A',
            'current_time' => now()->toDateTimeString(),
        ];

        return view('livewire.tenant.debug-info', compact('debug'));
    }
}
