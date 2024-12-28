<?php

namespace App\Livewire;

use Livewire\Component;

class TenantDashboard extends Component
{

   public $widgets;
   public $tenantinfo;
   public $tenantmembers;

public mount(){
 $this->widgets
}

    public function render()
    {
        return view('livewire.tenant-dashboard');
    }
}
