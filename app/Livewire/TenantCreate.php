<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class TenantCreate extends Component
{
    use WithFileUploads;

    public $name, $slug, $domain, $email, $password;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:tenants,slug',
        'domain' => 'required|url|unique:tenants,domain',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ];

    public function createTenant()
    {
        $this->validate(); 

        $tenant = Tenant::create([
            'name' => $this->name,
            'slug' => $this->slug ?? Str::slug($this->name),
            'domain' => $this->domain,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => 'admin',
        ]);

        session()->flash('message', 'Tenant created successfully!');
        
        return redirect()->route('tenant.index'); 
    }

    public function render()
    {
        return view('livewire.tenant-create');
    }
}
