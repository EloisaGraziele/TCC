<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Aluno extends Model
{
    use HasFactory;

    // Define o nome da tabela no banco de dados
    protected $table = 'alunos';

    // Campos que podem ser preenchidos via atribuição em massa (mass assignment)
    protected $fillable = [
        'nome',
        'cpf',
        'matricula',
        'data_nascimento',
        'status',
        'turma_id', // Referência para turma
        'qr_code_token',
        'qr_code_path',
    ];

    /**
     * Relacionamento com Turma
     */
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    // Mapeamento de campos que devem ser tratados como datas
    protected $casts = [
        'data_nascimento' => 'date',
    ];

    /**
     * Gera e define um token de QR Code único antes de salvar um novo aluno.
     */
    protected static function booted()
    {
        static::creating(function ($aluno) {
            // Cria um token único combinando a matrícula (se houver) e uma string aleatória
            $aluno->qr_code_token = Str::uuid() . '-' . $aluno->matricula;
        });
    }
}