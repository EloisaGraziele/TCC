<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'aluno_id',
        'frequencia',
        'hora_entrada',
        'horario_saida',
    ];

    protected $casts = [
        'hora_entrada' => 'string',
        'horario_saida' => 'datetime',
    ];

    /**
     * Relacionamento com Aluno
     */
    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}