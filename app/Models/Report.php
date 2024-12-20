<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['tenant_id', 'type', 'data'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
