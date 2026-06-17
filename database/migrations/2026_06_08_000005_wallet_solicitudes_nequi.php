<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wallet_solicitudes')) {
            return;
        }

        if (!Schema::hasColumn('wallet_solicitudes', 'comprobante_path')) {
            Schema::table('wallet_solicitudes', function (Blueprint $table) {
                $table->string('comprobante_path', 255)->nullable()->after('referencia_pago');
            });
        }

        if (!Schema::hasColumn('wallet_solicitudes', 'solicitante_user_id')) {
            Schema::table('wallet_solicitudes', function (Blueprint $table) {
                $table->unsignedBigInteger('solicitante_user_id')->nullable()->after('comprobante_path');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('wallet_solicitudes')) {
            return;
        }

        Schema::table('wallet_solicitudes', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_solicitudes', 'solicitante_user_id')) {
                $table->dropColumn('solicitante_user_id');
            }
            if (Schema::hasColumn('wallet_solicitudes', 'comprobante_path')) {
                $table->dropColumn('comprobante_path');
            }
        });
    }
};
