<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['tenant_id', 'plan'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
