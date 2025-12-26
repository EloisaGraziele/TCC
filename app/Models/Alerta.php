<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_alerta',
        'descricao',
        'parametros',
        'destinatarios',
        'turmas_aplicaveis',
        'ativo'
    ];

    protected $casts = [
        'parametros' => 'array',
        'turmas_aplicaveis' => 'array',
        'ativo' => 'boolean'
    ];
}
