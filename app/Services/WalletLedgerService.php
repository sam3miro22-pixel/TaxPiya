<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WalletLedgerService
{
    public function __construct(private WalletService $legacyWallet)
    {
    }

    public function ensureCuenta(string $tipo, int $refId): ?object
    {
        if (!Schema::hasTable('wallet_cuentas')) {
            return null;
        }

        $query = DB::table('wallet_cuentas')->where('tipo', $tipo);
        match ($tipo) {
            'pasajero'  => $query->where('user_id', $refId),
            'conductor' => $query->where('conductor_id', $refId),
            'empresa'   => $query->where('empresa_id', $refId),
            default     => throw new RuntimeException('Tipo de cuenta inválido'),
        };

        $cuenta = $query->first();
        if ($cuenta) {
            return $cuenta;
        }

        $defaults = $this->defaultsForTipo($tipo, $refId);
        $payload = $this->filterExistingColumns('wallet_cuentas', array_merge($defaults, [
            'tipo'       => $tipo,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]));
        $id = DB::table('wallet_cuentas')->insertGetId($payload);

        if ($tipo === 'conductor') {
            $this->legacyWallet->ensureSaldoRow($refId);
            $legacy = $this->legacyWallet->getSaldo($refId);
            if ($legacy) {
                DB::table('wallet_cuentas')->where('id', $id)->update([
                    'saldo_actual'       => (float) $legacy->saldo_actual,
                    'min_operativo'      => (float) $legacy->min_operativo,
                    'last_movimiento_id' => $legacy->last_movimiento_id ?? null,
                    'last_movimiento_at' => $legacy->last_movimiento_at ?? null,
                    'updated_at'         => now()->toDateTimeString(),
                ]);
            }
        }

        return DB::table('wallet_cuentas')->where('id', $id)->first();
    }

    private function defaultsForTipo(string $tipo, int $refId): array
    {
        $base = [
            'saldo_actual'    => 0,
            'saldo_reservado' => 0,
            'min_operativo'   => 0,
            'moneda'          => 'COP',
            'bloqueado'       => 0,
            'user_id'         => null,
            'conductor_id'    => null,
            'empresa_id'      => null,
        ];

        return match ($tipo) {
            'pasajero' => array_merge($base, [
                'user_id'          => $refId,
                'puede_depositar'  => 1,
                'puede_retirar'    => 0,
                'solo_lectura'     => 0,
            ]),
            'conductor' => $this->conductorDefaults($refId, $base),
            'empresa' => array_merge($base, [
                'empresa_id'       => $refId,
                'puede_depositar'  => 1,
                'puede_retirar'    => 1,
                'solo_lectura'     => 0,
            ]),
            default => $base,
        };
    }

    public function isConductorFlota(?object $conductorRow): bool
    {
        return $conductorRow && isset($conductorRow->empresa_id) && (int) $conductorRow->empresa_id > 0;
    }

    private function conductorDefaults(int $conductorId, array $base): array
    {
        $row = DB::table('conductores')->where('id', $conductorId)->first();
        $isFlota = $this->isConductorFlota($row);

        return array_merge($base, [
            'conductor_id'    => $conductorId,
            'min_operativo'   => $isFlota ? 0 : (float) config('taxpiya.wallet.default_min_operativo', 5000),
            'puede_depositar' => $isFlota ? 0 : 1,
            'puede_retirar'   => $isFlota ? 0 : 1,
            'solo_lectura'    => $isFlota ? 1 : 0,
        ]);
    }

    public function syncConductorPermissions(int $conductorId): void
    {
        $cuenta = $this->ensureCuenta('conductor', $conductorId);
        if (!$cuenta) {
            return;
        }

        $defaults = $this->conductorDefaults($conductorId, []);
        DB::table('wallet_cuentas')->where('id', $cuenta->id)->update([
            'puede_depositar' => $defaults['puede_depositar'],
            'puede_retirar'   => $defaults['puede_retirar'],
            'solo_lectura'    => $defaults['solo_lectura'],
            'min_operativo'   => $defaults['min_operativo'],
            'updated_at'      => now()->toDateTimeString(),
        ]);
    }

    public function getMovimientos(int $cuentaId, int $limit = 50): array
    {
        if (!Schema::hasTable('wallet_movimientos')) {
            return [];
        }

        return DB::table('wallet_movimientos')
            ->where('cuenta_id', $cuentaId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function solicitarDeposito(int $cuentaId, float $monto, ?int $userId = null, ?string $metodo = null, array $extra = []): array
    {
        return $this->crearSolicitud($cuentaId, 'deposito', $monto, $userId, $metodo, $extra);
    }

    public function solicitarRetiro(int $cuentaId, float $monto, ?int $userId = null, ?string $metodo = null, array $extra = []): array
    {
        return $this->crearSolicitud($cuentaId, 'retiro', $monto, $userId, $metodo, $extra);
    }

    public function getSolicitudesForCuenta(int $cuentaId, ?string $estado = null, int $limit = 20): array
    {
        if (!Schema::hasTable('wallet_solicitudes')) {
            return [];
        }

        $query = DB::table('wallet_solicitudes')
            ->where('cuenta_id', $cuentaId)
            ->orderByDesc('id')
            ->limit($limit);

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->get()->all();
    }

    private function crearSolicitud(int $cuentaId, string $operacion, float $monto, ?int $userId, ?string $metodo, array $extra = []): array
    {
        $cuenta = DB::table('wallet_cuentas')->where('id', $cuentaId)->first();
        if (!$cuenta) {
            return ['ok' => false, 'message' => 'Cuenta no encontrada'];
        }

        if ((int) $cuenta->bloqueado === 1) {
            return ['ok' => false, 'message' => 'Billetera bloqueada'];
        }

        if ($operacion === 'deposito' && !(int) $cuenta->puede_depositar) {
            return ['ok' => false, 'message' => 'Depósitos no permitidos en esta cuenta'];
        }

        if ($operacion === 'retiro' && !(int) $cuenta->puede_retirar) {
            return ['ok' => false, 'message' => 'Retiros no permitidos en esta cuenta'];
        }

        if ($monto <= 0) {
            return ['ok' => false, 'message' => 'Monto inválido'];
        }

        if ($operacion === 'retiro' && (float) $cuenta->saldo_actual < $monto) {
            return ['ok' => false, 'message' => 'Saldo insuficiente'];
        }

        if (!Schema::hasTable('wallet_solicitudes')) {
            $movId = $this->registrarMovimientoCuenta($cuentaId, [
                'sentido'        => $operacion === 'deposito' ? 'credito' : 'debito',
                'motivo'         => $operacion,
                'tipo_operacion' => $operacion,
                'monto'          => $monto,
                'moneda'         => $cuenta->moneda ?? 'COP',
                'descripcion'    => ucfirst($operacion) . ' billetera',
                'metodo_pago'    => $metodo ?? 'manual',
                'user_id'        => $userId,
                'estado'         => 'completado',
                'idempotencia'   => $operacion . '_' . $cuentaId . '_' . now()->format('YmdHisu'),
            ]);

            return ['ok' => true, 'movimiento_id' => $movId, 'estado' => 'completado'];
        }

        $autoApprove = (bool) config('taxpiya.wallet.auto_approve_requests', false);
        if ($operacion === 'deposito' && ($metodo ?? '') === 'nequi') {
            $autoApprove = false;
        }

        $solicitudPayload = $this->filterExistingColumns('wallet_solicitudes', [
            'cuenta_id'           => $cuentaId,
            'operacion'           => $operacion,
            'monto'               => round($monto, 2),
            'moneda'              => $cuenta->moneda ?? 'COP',
            'estado'              => $autoApprove ? 'aprobada' : 'pendiente',
            'metodo_pago'         => $metodo ?? 'manual',
            'referencia_pago'     => $extra['referencia_pago'] ?? null,
            'comprobante_path'    => $extra['comprobante_path'] ?? null,
            'solicitante_user_id' => $extra['solicitante_user_id'] ?? $userId,
            'notas'               => $extra['notas'] ?? null,
            'created_at'          => now()->toDateTimeString(),
            'updated_at'          => now()->toDateTimeString(),
        ]);
        $solicitudId = DB::table('wallet_solicitudes')->insertGetId($solicitudPayload);

        if ($autoApprove) {
            $movId = $this->aplicarSolicitud((int) $solicitudId, $userId);
            return ['ok' => true, 'solicitud_id' => $solicitudId, 'movimiento_id' => $movId, 'estado' => 'completado'];
        }

        return ['ok' => true, 'solicitud_id' => $solicitudId, 'estado' => 'pendiente'];
    }

    public function aplicarSolicitud(int $solicitudId, ?int $procesadoPor = null): ?int
    {
        $sol = DB::table('wallet_solicitudes')->where('id', $solicitudId)->first();
        if (!$sol || $sol->estado === 'completado') {
            return null;
        }

        $motivo = $sol->operacion === 'deposito' ? 'deposito' : 'retiro';
        $sentido = $sol->operacion === 'deposito' ? 'credito' : 'debito';

        $movId = $this->registrarMovimientoCuenta((int) $sol->cuenta_id, [
            'sentido'         => $sentido,
            'motivo'          => $motivo,
            'tipo_operacion'  => $sol->operacion,
            'monto'           => (float) $sol->monto,
            'moneda'          => $sol->moneda ?? 'COP',
            'descripcion'     => ucfirst($sol->operacion) . ' billetera',
            'metodo_pago'     => $sol->metodo_pago,
            'referencia_externa' => $sol->referencia_pago,
            'estado'          => 'completado',
            'admin_user_id'   => $procesadoPor,
            'idempotencia'    => "solicitud_{$solicitudId}",
        ]);

        DB::table('wallet_solicitudes')->where('id', $solicitudId)->update([
            'estado'         => 'completado',
            'movimiento_id'  => $movId,
            'procesado_por'  => $procesadoPor,
            'updated_at'     => now()->toDateTimeString(),
        ]);

        return $movId;
    }

    public function rechazarSolicitud(int $solicitudId, ?int $procesadoPor = null, ?string $notas = null): bool
    {
        $sol = DB::table('wallet_solicitudes')->where('id', $solicitudId)->first();
        if (!$sol || in_array($sol->estado, ['completado', 'rechazada'], true)) {
            return false;
        }

        DB::table('wallet_solicitudes')->where('id', $solicitudId)->update([
            'estado'        => 'rechazada',
            'procesado_por' => $procesadoPor,
            'notas'         => $notas ?? $sol->notas,
            'updated_at'    => now()->toDateTimeString(),
        ]);

        return true;
    }

    public function resolveCuentaTitular(?object $cuenta): array
    {
        if (!$cuenta) {
            return ['tipo' => '', 'nombre' => '—', 'detalle' => ''];
        }

        return match ($cuenta->tipo) {
            'pasajero' => $this->titularPasajero((int) $cuenta->user_id),
            'conductor' => $this->titularConductor((int) $cuenta->conductor_id),
            'empresa' => $this->titularEmpresa((int) $cuenta->empresa_id),
            default => ['tipo' => (string) $cuenta->tipo, 'nombre' => '—', 'detalle' => ''],
        };
    }

    private function titularPasajero(int $userId): array
    {
        $user = DB::table('users')->where('id', $userId)->first();

        return [
            'tipo'    => 'Pasajero',
            'nombre'  => $user->name ?? 'Pasajero',
            'detalle' => trim(($user->telefono ?? '') . ' · ' . ($user->email ?? '')),
        ];
    }

    private function titularConductor(int $conductorId): array
    {
        $row = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->where('c.id', $conductorId)
            ->selectRaw('u.name, u.telefono, u.email')
            ->first();

        return [
            'tipo'    => 'Conductor',
            'nombre'  => $row->name ?? 'Conductor',
            'detalle' => trim(($row->telefono ?? '') . ' · ' . ($row->email ?? '')),
        ];
    }

    private function titularEmpresa(int $empresaId): array
    {
        $row = DB::table('empresas')->where('id', $empresaId)->first();

        return [
            'tipo'    => 'Empresa',
            'nombre'  => $row->nombre_comercial ?? 'Empresa',
            'detalle' => trim(($row->telefono ?? '') . ' · ' . ($row->email ?? '')),
        ];
    }

    public function pagarConductorDesdeEmpresa(int $empresaId, int $conductorId, float $monto, ?int $adminUserId, ?string $nota = null): array
    {
        $conductor = DB::table('conductores')
            ->where('id', $conductorId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$conductor) {
            return ['ok' => false, 'message' => 'Conductor no pertenece a esta flota'];
        }

        if ($monto <= 0) {
            return ['ok' => false, 'message' => 'Monto inválido'];
        }

        $cuentaEmpresa = $this->ensureCuenta('empresa', $empresaId);
        $cuentaConductor = $this->ensureCuenta('conductor', $conductorId);

        if ((float) $cuentaEmpresa->saldo_actual < $monto) {
            return ['ok' => false, 'message' => 'Saldo insuficiente en billetera de la empresa'];
        }

        $idempotencia = 'pago_empresa_' . $empresaId . '_conductor_' . $conductorId . '_' . now()->format('YmdHis');

        DB::transaction(function () use ($cuentaEmpresa, $cuentaConductor, $monto, $adminUserId, $nota, $idempotencia, $conductorId, $empresaId) {
            $this->registrarMovimientoCuenta((int) $cuentaEmpresa->id, [
                'sentido'        => 'debito',
                'motivo'         => 'pago_conductor',
                'tipo_operacion' => 'pago_conductor',
                'monto'          => $monto,
                'descripcion'    => $nota ?: "Pago a conductor #{$conductorId}",
                'empresa_id'     => $empresaId,
                'admin_user_id'  => $adminUserId,
                'idempotencia'   => $idempotencia . '_emp',
            ]);

            $this->registrarMovimientoCuenta((int) $cuentaConductor->id, [
                'sentido'        => 'credito',
                'motivo'         => 'pago_empresa',
                'tipo_operacion' => 'pago_empresa',
                'monto'          => $monto,
                'descripcion'    => $nota ?: 'Pago recibido de la empresa',
                'empresa_id'     => $empresaId,
                'admin_user_id'  => $adminUserId,
                'idempotencia'   => $idempotencia . '_cond',
            ]);
        });

        return ['ok' => true];
    }

    public function creditoIngresoViaje(int $conductorId, int $viajeId, float $tarifa, string $moneda = 'COP'): ?int
    {
        $cuenta = $this->ensureCuenta('conductor', $conductorId);
        if (!$cuenta) {
            return null;
        }

        return $this->registrarMovimientoCuenta((int) $cuenta->id, [
            'sentido'        => 'credito',
            'motivo'         => 'ingreso_viaje',
            'tipo_operacion' => 'ingreso_viaje',
            'monto'          => $tarifa,
            'moneda'         => $moneda,
            'viaje_id'       => $viajeId,
            'conductor_id'   => $conductorId,
            'descripcion'    => "Ingreso viaje #{$viajeId}",
            'idempotencia'   => "ingreso_viaje_{$viajeId}",
        ]);
    }

    public function registrarMovimientoCuenta(int $cuentaId, array $data): ?int
    {
        if (!Schema::hasTable('wallet_cuentas')) {
            return null;
        }

        $cuenta = DB::table('wallet_cuentas')->where('id', $cuentaId)->first();
        if (!$cuenta) {
            throw new RuntimeException('Cuenta no encontrada');
        }

        $idempotencia = $data['idempotencia'] ?? null;
        if ($idempotencia) {
            $existing = DB::table('wallet_movimientos')
                ->where('idempotencia', $idempotencia)
                ->where('anulado', 0)
                ->value('id');
            if ($existing) {
                return (int) $existing;
            }
        }

        $movId = null;

        DB::transaction(function () use ($cuentaId, $cuenta, $data, &$movId) {
            $locked = DB::table('wallet_cuentas')->where('id', $cuentaId)->lockForUpdate()->first();
            $saldoAntes = (float) $locked->saldo_actual;
            $monto = round((float) $data['monto'], 2);
            $sentido = $data['sentido'];
            $delta = $sentido === 'credito' ? $monto : -$monto;
            $saldoDespues = round($saldoAntes + $delta, 2);

            if ($sentido === 'debito' && $saldoDespues < 0 && !($data['allow_negative'] ?? false)) {
                throw new RuntimeException('Saldo insuficiente');
            }

            $now = now()->toDateTimeString();
            // wallet_movimientos.conductor_id es NOT NULL en SQLite legacy; pasajero/empresa usan 0.
            $conductorId = (int) ($data['conductor_id'] ?? $locked->conductor_id ?? 0);

            $movPayload = $this->filterExistingColumns('wallet_movimientos', [
                'cuenta_id'          => $cuentaId,
                'conductor_id'       => $conductorId,
                'viaje_id'           => $data['viaje_id'] ?? null,
                'admin_user_id'      => $data['admin_user_id'] ?? null,
                'empresa_id'         => $data['empresa_id'] ?? $locked->empresa_id,
                'user_id'            => $data['user_id'] ?? $locked->user_id,
                'sentido'            => $sentido,
                'motivo'             => $data['motivo'],
                'tipo_operacion'     => $data['tipo_operacion'] ?? $data['motivo'],
                'estado'             => $data['estado'] ?? 'completado',
                'monto'              => $monto,
                'moneda'             => $data['moneda'] ?? $locked->moneda ?? 'COP',
                'saldo_antes'        => $saldoAntes,
                'saldo_despues'      => $saldoDespues,
                'descripcion'        => $data['descripcion'] ?? null,
                'referencia_externa' => $data['referencia_externa'] ?? null,
                'metodo_pago'        => $data['metodo_pago'] ?? null,
                'idempotencia'       => $data['idempotencia'] ?? null,
                'anulado'            => 0,
                'created_at'         => $now,
            ]);
            $movId = DB::table('wallet_movimientos')->insertGetId($movPayload);

            $cuentaPayload = $this->filterExistingColumns('wallet_cuentas', [
                'saldo_actual'       => $saldoDespues,
                'last_movimiento_id' => $movId,
                'last_movimiento_at' => $now,
                'updated_at'         => $now,
            ]);
            DB::table('wallet_cuentas')->where('id', $cuentaId)->update($cuentaPayload);

            if ($locked->tipo === 'conductor' && $locked->conductor_id) {
                DB::table('wallet_saldos')->updateOrInsert(
                    ['conductor_id' => $locked->conductor_id],
                    [
                        'saldo_actual'       => $saldoDespues,
                        'last_movimiento_id' => $movId,
                        'last_movimiento_at' => $now,
                        'updated_at'         => $now,
                    ]
                );
            }
        });

        return $movId;
    }

    public function resumenIngresosConductor(int $conductorId): array
    {
        $cuenta = $this->ensureCuenta('conductor', $conductorId);
        if (!$cuenta) {
            return ['saldo' => 0, 'ingresos_viajes' => 0, 'pagos_empresa' => 0, 'comisiones' => 0];
        }

        $base = DB::table('wallet_movimientos')->where('cuenta_id', $cuenta->id)->where('anulado', 0);

        return [
            'saldo'           => (float) $cuenta->saldo_actual,
            'ingresos_viajes' => (float) (clone $base)->where('motivo', 'ingreso_viaje')->where('sentido', 'credito')->sum('monto'),
            'pagos_empresa'   => (float) (clone $base)->where('motivo', 'pago_empresa')->where('sentido', 'credito')->sum('monto'),
            'comisiones'      => (float) (clone $base)->whereIn('motivo', ['debito_termino', 'debito_aceptacion'])->where('sentido', 'debito')->sum('monto'),
            'depositos'       => (float) (clone $base)->where('motivo', 'deposito')->sum('monto'),
            'retiros'         => (float) (clone $base)->where('motivo', 'retiro')->sum('monto'),
        ];
    }

    /** Evita 500 si la BD en producción aún no tiene columnas nuevas de wallet_cuentas. */
    private function filterExistingColumns(string $table, array $data): array
    {
        $columns = Schema::getColumnListing($table);
        if ($columns === []) {
            return $data;
        }

        return array_intersect_key($data, array_flip($columns));
    }
}
