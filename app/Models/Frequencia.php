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
        'data_entrada',
        'horario_saida',
    ];

    protected $casts = [
        'data_entrada' => 'datetime',
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