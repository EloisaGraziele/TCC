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
        Schema::dropIfExists('alunos');
        
        // Criar nova tabela alunos
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->string('matricula', 50)->unique();
            $table->date('data_nascimento');
            $table->enum('status', ['ativo', 'inativo', 'transferido'])->default('ativo');
            $table->unsignedBigInteger('turma_id')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_token')->nullable()->unique();
            $table->timestamps();
            
            // Foreign key para turmas
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};