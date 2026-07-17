<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('viajes', 'pickup_code')) {
            Schema::table('viajes', function (Blueprint $table) {
                $table->string('pickup_code', 6)->nullable()->after('estado');
            });
        }
        if (!Schema::hasColumn('viajes', 'pickup_code_verified')) {
            Schema::table('viajes', function (Blueprint $table) {
                $table->boolean('pickup_code_verified')->default(false)->after('pickup_code');
            });
        }
    }

    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            if (Schema::hasColumn('viajes', 'pickup_code')) {
                $table->dropColumn('pickup_code');
            }
            if (Schema::hasColumn('viajes', 'pickup_code_verified')) {
                $table->dropColumn('pickup_code_verified');
            }
        });
    }
};
