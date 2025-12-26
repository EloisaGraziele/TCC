<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notificacoes');

        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alerta_id')->constrained('alertas');
            $table->foreignId('aluno_id')->constrained('alunos');
            $table->string('destinatario_tipo'); // 'pais' ou 'secretaria'
            $table->text('mensagem');
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
