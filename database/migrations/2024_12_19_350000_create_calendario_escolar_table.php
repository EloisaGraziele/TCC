<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendario_escolar', function (Blueprint $table) {
            $table->id();
            $table->integer('ano');
            $table->date('data');
            $table->enum('tipo_dia', ['letivo', 'sabado', 'domingo', 'feriado', 'sabado_letivo', 'ponto_facultativo', 'evento', 'reuniao', 'ferias'])->default('letivo');
            $table->string('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->unique(['ano', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario_escolar');
    }
};