<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- ESTA LINHA É CRÍTICA!
use Illuminate\Notifications\Notifiable;

class Secretaria extends Authenticatable
{
    protected $guarded = [];

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Caso queira adicionar timestamps
    public $timestamps = true;
}
