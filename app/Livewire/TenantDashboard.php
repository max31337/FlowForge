<?php

namespace App\Livewire;

use Livewire\Component;

class TenantDashboard extends Component
{

   public $widgets;
   public $tenantinfo;
   public $tenantmembers;

public function mount(){
 $this->widgets = getWidgetinfo()
$this->tenantinfo = getTenantinfo()
&this->tenantmembers = getTenantmembers()
}

    public function render()
    {
        return view('livewire.tenant-dashboard');
        
    }
}
