<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioEscolar extends Model
{
    use HasFactory;

    protected $table = 'calendario_escolar';

    protected $fillable = [
        'ano',
        'data',
        'tipo_dia',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'data' => 'date',
        'ativo' => 'boolean'
    ];
}