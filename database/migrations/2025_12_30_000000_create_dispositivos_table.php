<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('dispositivos')) {
            Schema::create('dispositivos', function (Blueprint $table) {
                $table->id();
                $table->string('mac_address')->unique();
                $table->boolean('autorizado')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('dispositivos');
    }
};