<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PasswordResetToken extends Model
{
    use HasFactory;


    protected $fillable = [
        'email',
        'token',
    ];

    protected $guarded = [
        'created_at',
    ];

    public $timestamps = false; 


    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
