<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notificacoes')) {
            return;
        }

        Schema::table('notificacoes', function (Blueprint $table) {
            if (!Schema::hasColumn('notificacoes', 'lida')) {
                $table->boolean('lida')->default(false)->after('mensagem');
            }

            if (!Schema::hasColumn('notificacoes', 'lida_em')) {
                $table->timestamp('lida_em')->nullable()->after('lida');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notificacoes')) {
            return;
        }

        Schema::table('notificacoes', function (Blueprint $table) {
            if (Schema::hasColumn('notificacoes', 'lida')) {
                $table->dropColumn('lida');
            }
            if (Schema::hasColumn('notificacoes', 'lida_em')) {
                $table->dropColumn('lida_em');
            }
        });
    }
};
