<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('referidos')) {
            return;
        }

        Schema::table('referidos', function (Blueprint $table) {
            if (!Schema::hasColumn('referidos', 'bonus_monto')) {
                $table->decimal('bonus_monto', 14, 2)->nullable()->after('estado');
            }
            if (!Schema::hasColumn('referidos', 'bonus_paid_at')) {
                $table->timestamp('bonus_paid_at')->nullable()->after('bonus_monto');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('referidos')) {
            return;
        }

        Schema::table('referidos', function (Blueprint $table) {
            if (Schema::hasColumn('referidos', 'bonus_paid_at')) {
                $table->dropColumn('bonus_paid_at');
            }
            if (Schema::hasColumn('referidos', 'bonus_monto')) {
                $table->dropColumn('bonus_monto');
            }
        });
    }
};
