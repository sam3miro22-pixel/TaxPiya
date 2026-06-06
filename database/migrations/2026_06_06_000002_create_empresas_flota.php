<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('empresas')) {
            Schema::create('empresas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('nombre_comercial', 160);
                $table->string('razon_social', 160)->nullable();
                $table->string('nit', 32)->nullable();
                $table->string('telefono', 50)->nullable();
                $table->string('email', 120)->nullable();
                $table->string('ciudad', 80)->default('Medellín');
                $table->string('direccion', 255)->nullable();
                $table->string('estado', 24)->default('pendiente');
                $table->string('verificacion_estado', 24)->default('pendiente');
                $table->text('notas')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('conductores') && !Schema::hasColumn('conductores', 'empresa_id')) {
            Schema::table('conductores', function (Blueprint $table) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('user_id');
            });
        }

        $roleExists = DB::table('roles')->where('role_name', 'Empresa')->exists();
        if (!$roleExists && Schema::hasTable('roles')) {
            DB::table('roles')->insert([
                'role_id'   => 4,
                'role_name' => 'Empresa',
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('conductores') && Schema::hasColumn('conductores', 'empresa_id')) {
            Schema::table('conductores', function (Blueprint $table) {
                $table->dropColumn('empresa_id');
            });
        }

        Schema::dropIfExists('empresas');

        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('role_name', 'Empresa')->delete();
        }
    }
};
