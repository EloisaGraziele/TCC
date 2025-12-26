<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Responsavel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'responsaveis';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'telefone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}