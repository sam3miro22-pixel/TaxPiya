<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wallet_cuentas')) {
            Schema::create('wallet_cuentas', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 16);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('conductor_id')->nullable();
                $table->unsignedBigInteger('empresa_id')->nullable();
                $table->decimal('saldo_actual', 14, 2)->default(0);
                $table->decimal('saldo_reservado', 14, 2)->default(0);
                $table->decimal('min_operativo', 14, 2)->default(0);
                $table->string('moneda', 8)->default('COP');
                $table->boolean('bloqueado')->default(false);
                $table->string('motivo_bloqueo', 255)->nullable();
                $table->boolean('puede_depositar')->default(true);
                $table->boolean('puede_retirar')->default(false);
                $table->boolean('solo_lectura')->default(false);
                $table->unsignedBigInteger('last_movimiento_id')->nullable();
                $table->timestamp('last_movimiento_at')->nullable();
                $table->timestamps();
                $table->unique(['tipo', 'user_id']);
                $table->unique(['tipo', 'conductor_id']);
                $table->unique(['tipo', 'empresa_id']);
            });
        }

        if (Schema::hasTable('wallet_movimientos')) {
            if (!Schema::hasColumn('wallet_movimientos', 'cuenta_id')) {
                Schema::table('wallet_movimientos', function (Blueprint $table) {
                    $table->unsignedBigInteger('cuenta_id')->nullable()->after('id');
                    $table->string('tipo_operacion', 24)->nullable()->after('motivo');
                    $table->string('estado', 16)->default('completado')->after('tipo_operacion');
                    $table->string('metodo_pago', 32)->nullable()->after('referencia_externa');
                    $table->unsignedBigInteger('empresa_id')->nullable()->after('admin_user_id');
                    $table->unsignedBigInteger('user_id')->nullable()->after('empresa_id');
                });
            }
        }

        if (!Schema::hasTable('wallet_solicitudes')) {
            Schema::create('wallet_solicitudes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cuenta_id');
                $table->string('operacion', 16);
                $table->decimal('monto', 14, 2);
                $table->string('moneda', 8)->default('COP');
                $table->string('estado', 16)->default('pendiente');
                $table->string('metodo_pago', 32)->nullable();
                $table->string('referencia_pago', 128)->nullable();
                $table->text('notas')->nullable();
                $table->unsignedBigInteger('procesado_por')->nullable();
                $table->unsignedBigInteger('movimiento_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_solicitudes');
        Schema::dropIfExists('wallet_cuentas');
    }
};
