<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('turmas')) {
            Schema::create('turmas', function (Blueprint $table) {
                $table->id();
                $table->string('ano');
                $table->string('turma');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('turmas');
    }
};