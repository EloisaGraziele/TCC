<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';
    
    protected $fillable = [
        'alerta_id',
        'aluno_id', 
        'destinatario_tipo',
        'mensagem',
        'enviado',
        'enviado_em',
        'lida',
        'lida_em'
    ];

    protected $casts = [
        'enviado' => 'boolean',
        'enviado_em' => 'datetime',
        'lida' => 'boolean',
        'lida_em' => 'datetime'
    ];

    public function alerta(): BelongsTo
    {
        return $this->belongsTo(Alerta::class);
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }
}