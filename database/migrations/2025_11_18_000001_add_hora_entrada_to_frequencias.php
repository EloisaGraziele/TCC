<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('frequencias')) {
            return;
        }

        Schema::table('frequencias', function (Blueprint $table) {
            if (! Schema::hasColumn('frequencias', 'hora_entrada')) {
                $table->time('hora_entrada')->nullable()->after('frequencia');
            }
        });

        // Se houver dados em data_entrada, copie a parte hora para hora_entrada
        try {
            DB::statement("UPDATE frequencias SET hora_entrada = TIME(data_entrada) WHERE data_entrada IS NOT NULL AND (hora_entrada IS NULL OR hora_entrada = '')");
        } catch (\Exception $e) {
            // ignore copy errors
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frequencias', function (Blueprint $table) {
            if (Schema::hasColumn('frequencias', 'hora_entrada')) {
                $table->dropColumn('hora_entrada');
            }
        });
    }
};
