<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Garante que a tabela antiga seja derrubada antes de recriar
        Schema::dropIfExists('users');
        
        // Criar a nova tabela users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); // Essencial para o Laravel
            $table->string('cpf')->unique();
            $table->string('telefone')->nullable(); // Deixado como nullable
            $table->string('password');
            $table->rememberToken(); // Essencial para "Lembrar-me"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Apenas derruba a tabela
        Schema::dropIfExists('users');
    }
};