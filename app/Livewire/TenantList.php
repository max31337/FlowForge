<?php

namespace App\Livewire;

use Livewire\Component;
 
 public $tenantmembers

public function mount(){
 $this->tenantmembers = getTenantmembers()

class TenantList extends Component
{
    public function render()
    {
        return view('livewire.tenant-list');
    }
}
