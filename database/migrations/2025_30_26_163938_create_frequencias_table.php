<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Apagar tabela antiga se existir
        Schema::dropIfExists('frequencias');
        
        // Criar nova tabela frequencias
        Schema::create('frequencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aluno_id');
            $table->enum('frequencia', ['presente', 'ausente', 'nao_registrada'])->default('nao_registrada');
            $table->datetime('data_entrada')->nullable();
            $table->datetime('horario_saida')->nullable();
            $table->timestamps();
            
            // Foreign key para alunos
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencias');
    }
};
