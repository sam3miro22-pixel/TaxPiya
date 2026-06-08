<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'codigo_referido')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('codigo_referido', 20)->nullable()->unique()->after('firebase_uid');
            });
        }

        if (Schema::hasTable('empresas') && !Schema::hasColumn('empresas', 'codigo_referido')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->string('codigo_referido', 20)->nullable()->unique()->after('user_id');
            });
        }

        if (!Schema::hasTable('referidos')) {
            Schema::create('referidos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo_usado', 20);
                $table->string('referrer_tipo', 16);
                $table->unsignedBigInteger('referrer_user_id')->nullable();
                $table->unsignedBigInteger('referrer_empresa_id')->nullable();
                $table->unsignedBigInteger('referred_user_id');
                $table->string('tipo_referido', 16);
                $table->string('estado', 16)->default('registrado');
                $table->text('notas')->nullable();
                $table->timestamps();
                $table->index('referred_user_id');
                $table->index('codigo_usado');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('referidos');

        if (Schema::hasColumn('users', 'codigo_referido')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('codigo_referido');
            });
        }

        if (Schema::hasTable('empresas') && Schema::hasColumn('empresas', 'codigo_referido')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->dropColumn('codigo_referido');
            });
        }
    }
};
