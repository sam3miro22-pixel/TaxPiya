@inject('comp_model', 'App\Models\ComponentsData')
<?php
use App\Models\Users;
use Illuminate\Support\Facades\DB;

$can_add    = $user->canAccess("conductores/add");
$can_edit   = $user->canAccess("conductores/edit");
$can_view   = $user->canAccess("conductores/view");
$can_delete = $user->canAccess("conductores/delete");
$pageTitle  = "Ver";

$viajes = [];
$vehiculos = [];

if (!empty($data) && !empty($data['id'])) {
    $viajes = DB::table('viajes')
        ->leftJoin('users as u', 'u.id', '=', 'viajes.pasajero_id')
        ->where('viajes.conductor_id', $data['id'])
        ->orderByDesc('viajes.created_at')
        ->limit(100)
        ->select(
            'viajes.*',
            'u.name as pasajero_nombre'
        )
        ->get();

    $vehiculos = DB::table('vehiculos')
        ->where('conductor_id', $data['id'])
        ->whereNull('deleted_at')
        ->orderByDesc('created_at')
        ->get();
}

$userRecord = null;
$userName   = 'Sin usuario';
if (!empty($data) && !empty($data['user_id'])) {
    $userRecord = Users::find($data['user_id']);
    if ($userRecord) {
        $userName = $userRecord->name;
    }
}
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')

<style>
.tax-driver-view {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.tax-page-wrap {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
}

.tax-main-grid {
    width: 100% !important;
    grid-template-columns: minmax(0, 1fr) !important;
}

.tax-card-grid {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.tax-table-wrap {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.tax-table-wrap .table-responsive {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

body {
    color: #e5e7eb !important;
}

#main #main-content {
    width: 100% !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
    position: relative !important;
    right: auto !important;
    left: 0 !important;
    padding: 16px 16px 40px !important;
}

:root {
    --tax-bg: #020617;
    --tax-surface: #020617;
    --tax-surface-soft: #0b1120;
    --tax-card: #020617;
    --tax-border: #1e293b;
    --tax-accent: #22c55e;
    --tax-accent-soft: rgba(34, 197, 94, 0.18);
    --tax-accent-strong: #a855f7;
    --tax-danger: #f97373;
    --tax-text-muted: #9ca3af;
}

.tax-driver-view {
    min-height: 100vh;
    padding-top: 80px;
}

.tax-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 10px;
}

.tax-page-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.tax-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: radial-gradient(circle at top left, rgba(34, 197, 94, 0.16), rgba(15, 23, 42, 1));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e5e7eb;
    transition: all 0.18s ease-out;
}

.tax-back-btn i {
    font-size: 18px;
}

.tax-back-btn:hover {
    transform: translateX(-1px) translateY(-1px);
    border-color: rgba(34, 197, 94, 0.7);
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.4);
}

.tax-title-block h1 {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.02em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tax-title-chip {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, rgba(56, 189, 248, 0.3), rgba(37, 99, 235, 0.08));
    box-shadow: 0 0 24px rgba(56, 189, 248, 0.4);
}

.tax-title-chip i {
    font-size: 16px;
    color: #e5e7eb;
}

.tax-title-sub {
    font-size: 13px;
    color: var(--tax-text-muted);
    margin-top: 1px;
}

.tax-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tax-actions .btn {
    border-radius: 999px;
    padding-inline: 14px;
}

.tax-actions .btn i {
    margin-right: 4px;
}

.tax-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 18px;
    margin-top: 6px;
}

.tax-card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px 12px;
}

@media (max-width: 1199.98px) {
    .tax-card-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .tax-page-wrap {
        padding: 14px 12px 18px;
        border-radius: 18px;
    }

    .tax-main-grid {
        gap: 12px;
    }

    .tax-card-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .tax-page-header {
        align-items: flex-start;
    }
}

.tax-card {
    background: radial-gradient(circle at top left, rgba(148, 163, 184, 0.1), rgba(15, 23, 42, 1));
    border-radius: 16px;
    border: 1px solid var(--tax-border);
    padding: 10px 11px;
    min-height: 64px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.tax-card-strong {
    border-color: rgba(34, 197, 94, 0.6);
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.18), 0 18px 30px rgba(15, 23, 42, 0.9);
}

.tax-card-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--tax-text-muted);
    margin-bottom: 2px;
}

.tax-card-value {
    font-size: 15px;
    font-weight: 600;
    color: #f9fafb;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.tax-badge-pill {
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.45);
    color: #e5e7eb;
    background: rgba(15, 23, 42, 0.85);
}

.tax-badge-pill.ok {
    border-color: rgba(34, 197, 94, 0.7);
    background: rgba(22, 163, 74, 0.22);
    color: #bbf7d0;
}

.tax-badge-pill.warn {
    border-color: rgba(245, 158, 11, 0.8);
    background: rgba(251, 191, 36, 0.16);
    color: #fed7aa;
}

.tax-badge-pill.bad {
    border-color: rgba(248, 113, 113, 0.85);
    background: rgba(239, 68, 68, 0.18);
    color: #fecaca;
}

.tax-link-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.5);
    font-size: 12px;
    background: rgba(15, 23, 42, 0.9);
    color: #e5e7eb;
    text-decoration: none;
    transition: all 0.18s ease;
}

.tax-link-chip i {
    font-size: 13px;
}

.tax-link-chip:hover {
    border-color: rgba(56, 189, 248, 0.85);
    color: #f9fafb;
    box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.6);
}

.tax-rating {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.tax-rating i {
    font-size: 0.78rem;
}

.tax-star-full {
    color: #facc15;
}

.tax-star-half {
    color: #fde68a;
}

.tax-star-empty {
    color: #4b5563;
}

.tax-rating-num {
    font-size: 12px;
    color: rgba(148, 163, 184, 0.95);
    margin-left: 4px;
}

.tax-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 18px 2px 8px;
}

.tax-section-title h2 {
    font-size: 15px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #9ca3af;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tax-section-title h2 i {
    font-size: 15px;
    color: rgba(56, 189, 248, 0.9);
}

.tax-section-tag {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.55);
    color: #cbd5f5;
    background: rgba(15, 23, 42, 0.9);
}

.tax-help-text {
    font-size: 12px;
    color: var(--tax-text-muted);
    margin: 0 2px 10px;
}

.tax-table-wrap {
    margin-top: 10px;
    border-radius: 18px;
    border: 1px solid rgba(30, 64, 175, 0.65);
    background: radial-gradient(circle at top left, rgba(30, 64, 175, 0.25), rgba(15, 23, 42, 0.98));
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.95);
    width: 100%;
}

.tax-table-wrap .table {
    margin-bottom: 0;
    width: 100%;
}

.tax-table-wrap thead {
    background: linear-gradient(90deg, rgba(30, 64, 175, 0.95), rgba(56, 189, 248, 0.9));
}

.tax-table-wrap thead th {
    border-bottom: none;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #e5e7eb;
    padding-block: 9px;
    white-space: nowrap;
}

.tax-table-wrap tbody tr {
    background: transparent;
}

.tax-table-wrap tbody tr:nth-child(even) {
    background: rgba(15, 23, 42, 0.85);
}

.tax-table-wrap tbody tr:nth-child(odd) {
    background: rgba(15, 23, 42, 0.6);
}

.tax-table-wrap tbody td {
    border-top: 1px solid rgba(30, 64, 175, 0.55);
    font-size: 13px;
    color: #e5e7eb;
    vertical-align: middle;
    padding-block: 8px;
}

.tax-empty-state {
    padding: 14px 16px;
    font-size: 13px;
    color: var(--tax-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.tax-empty-state i {
    color: rgba(148, 163, 184, 0.9);
}

/* Vehículos */
.tax-veh-header-ops {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tax-veh-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 8px;
}

@media (max-width: 991.98px) {
    .tax-veh-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

.tax-veh-card {
    border-radius: 18px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: radial-gradient(circle at top left, rgba(34, 197, 94, 0.12), rgba(15, 23, 42, 0.98));
    padding: 12px 14px;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 10px 14px;
    align-items: flex-start;
}

.tax-veh-icon {
    width: 42px;
    height: 42px;
    border-radius: 16px;
    background: radial-gradient(circle at top, rgba(56, 189, 248, 0.3), rgba(15, 23, 42, 1));
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 24px rgba(56, 189, 248, 0.35);
}

.tax-veh-icon i {
    font-size: 20px;
    color: #e5e7eb;
}

.tax-veh-main {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.tax-veh-title {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: baseline;
}

.tax-veh-title-main {
    font-size: 15px;
    font-weight: 700;
    color: #f9fafb;
}

.tax-veh-title-sub {
    font-size: 12px;
    color: var(--tax-text-muted);
}

.tax-veh-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
}

.tax-veh-chip {
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.4);
    background: rgba(15, 23, 42, 0.9);
    color: #e5e7eb;
}

.tax-veh-chip.green {
    border-color: rgba(34, 197, 94, 0.8);
    background: rgba(22, 163, 74, 0.18);
    color: #bbf7d0;
}

.tax-veh-chip.red {
    border-color: rgba(248, 113, 113, 0.85);
    background: rgba(239, 68, 68, 0.2);
    color: #fee2e2;
}

.tax-veh-chip.amber {
    border-color: rgba(245, 158, 11, 0.9);
    background: rgba(251, 191, 36, 0.18);
    color: #ffedd5;
}

.tax-veh-meta {
    margin-top: 4px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 4px 10px;
    font-size: 11px;
    color: var(--tax-text-muted);
}

@media (max-width: 575.98px) {
    .tax-veh-meta {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.tax-veh-meta span {
    white-space: nowrap;
}
</style>

<section class="page tax-driver-view" data-page-type="view" data-page-url="{{ url()->full() }}">
    <div class="tax-page-wrap mb-3">
        <div class="tax-page-header">
            <div class="tax-page-header-left">
                <a href="{{ url()->previous() }}" class="tax-back-btn">
                    <i class="fa fa-angle-left"></i>
                </a>
                <div class="tax-title-block">
                    <h1>
                        <span class="tax-title-chip">
                            <i class="fa fa-car"></i>
                        </span>
                        Detalle del Conductor
                    </h1>
                    <div class="tax-title-sub">
                        Revisa el estado operativo, verificaciones y sus viajes completados en Taxpiya.
                    </div>
                </div>
            </div>
            <div class="tax-actions">
                @if($data && $can_edit)
                    <?php $rec_id = ($data['id'] ? urlencode($data['id']) : null); ?>
                    <a href="{{ url("conductores/edit/$rec_id") }}" class="btn btn-success btn-sm">
                        <i class="fa fa-edit"></i> Editar
                    </a>
                @endif
                @if($data && $can_delete)
                    <?php $rec_id = ($data['id'] ? urlencode($data['id']) : null); ?>
                    <a href="{{ url("conductores/delete/$rec_id?redirect=conductores") }}"
                       class="btn btn-danger btn-sm record-delete-btn"
                       data-prompt-msg="¿Seguro que quieres borrar este registro?"
                       data-display-style="modal">
                        <i class="fa fa-times"></i> Eliminar
                    </a>
                @endif
            </div>
        </div>

        <div class="tax-main-grid">
            <div>
                @if($data)
                    <?php $rec_id = ($data['id'] ? urlencode($data['id']) : null); ?>
                    <div id="page-main-content" class="mb-2">
                        <div class="tax-card-grid">
                            <div class="tax-card tax-card-strong">
                                <div class="tax-card-label">ID Conductor</div>
                                <div class="tax-card-value">
                                    {{ $data['id'] }}
                                    <span class="tax-badge-pill">
                                        Perfil interno
                                    </span>
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Usuario vinculado</div>
                                <div class="tax-card-value">
                                    @if($userRecord)
                                        <a href="{{ url("users/view/" . $data['user_id']) }}"
                                           class="tax-link-chip">
                                            <i class="fa fa-user"></i>
                                            <span>{{ $userName }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">Sin usuario</span>
                                    @endif
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Estado Operitivo</div>
                                <div class="tax-card-value">
                                    {{ $data['estado_operitivo'] }}
                                    @php
                                        $estado = strtolower((string)$data['estado_operitivo']);
                                    @endphp
                                    @if(strpos($estado, 'activo') !== false || strpos($estado, 'apto') !== false)
                                        <span class="tax-badge-pill ok">
                                            <i class="fa fa-check-circle"></i> Activo
                                        </span>
                                    @elseif(strpos($estado, 'suspend') !== false)
                                        <span class="tax-badge-pill bad">
                                            <i class="fa fa-ban"></i> Suspendido
                                        </span>
                                    @else
                                        <span class="tax-badge-pill warn">
                                            <i class="fa fa-circle"></i> Revisar
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Disponible</div>
                                <div class="tax-card-value">
                                    @php
                                        $disp = (int)($data['disponible'] ?? 0);
                                    @endphp
                                    @if($disp === 1)
                                        <span class="tax-badge-pill ok">
                                            <i class="fa fa-toggle-on"></i> Disponible
                                        </span>
                                    @else
                                        <span class="tax-badge-pill warn">
                                            <i class="fa fa-toggle-off"></i> No disponible
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Última conexión</div>
                                <div class="tax-card-value">
                                    {{ $data['last_online_at'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Rating promedio</div>
                                <div class="tax-card-value">
                                    @php
                                        $rating = (float)($data['rating_promedio'] ?? 0);
                                        if ($rating < 0) $rating = 0;
                                        if ($rating > 5) $rating = 5;
                                    @endphp
                                    <div class="tax-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @php
                                                $class = 'tax-star-empty';
                                                if ($i <= floor($rating)) {
                                                    $class = 'tax-star-full';
                                                } elseif ($i - $rating < 1 && $i > $rating) {
                                                    $class = 'tax-star-half';
                                                }
                                            @endphp
                                            <i class="fa fa-star {{ $class }}"></i>
                                        @endfor
                                        <span class="tax-rating-num">
                                            {{ number_format($rating, 1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Total viajes</div>
                                <div class="tax-card-value">
                                    {{ $data['total_viajes'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Licencia número</div>
                                <div class="tax-card-value">
                                    {{ $data['licencia_numero'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Licencia categoría</div>
                                <div class="tax-card-value">
                                    {{ $data['licencia_categoria'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Licencia expira</div>
                                <div class="tax-card-value">
                                    {{ $data['licencia_expira'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">SOAT número</div>
                                <div class="tax-card-value">
                                    {{ $data['soat_numero'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">SOAT expira</div>
                                <div class="tax-card-value">
                                    {{ $data['soat_expira'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Seguro número</div>
                                <div class="tax-card-value">
                                    {{ $data['seguro_numero'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Verificación estado</div>
                                <div class="tax-card-value">
                                    {{ $data['verificacion_estado'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Verificación notas</div>
                                <div class="tax-card-value">
                                    {{ $data['verificacion_notas'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Contacto emergencia nombre</div>
                                <div class="tax-card-value">
                                    {{ $data['contacto_emergencia_nombre'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Contacto emergencia teléfono</div>
                                <div class="tax-card-value">
                                    {{ $data['contacto_emergencia_telefono'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Creado en</div>
                                <div class="tax-card-value">
                                    {{ $data['created_at'] }}
                                </div>
                            </div>

                            <div class="tax-card">
                                <div class="tax-card-label">Actualizado en</div>
                                <div class="tax-card-value">
                                    {{ $data['updated_at'] }}
                                </div>
                            </div>
                        </div>

                        <div class="tax-section-title">
                            <h2>
                                <i class="fa fa-taxi"></i>
                                Vehículos del conductor
                            </h2>
                            <div class="tax-veh-header-ops">
                                <span class="tax-section-tag">
                                    Información del vehículo asociado al perfil
                                </span>
                                @if($data)
                                    <a href="{{ url('vehiculos/add?conductor_id=' . $data['id']) }}"
                                       class="btn btn-success btn-sm rounded-pill">
                                        <i class="fa fa-plus"></i> Agregar vehículo
                                    </a>
                                @endif
                            </div>
                        </div>

                        <p class="tax-card-value">
                            Gestiona aquí el vehículo con el que este conductor presta el servicio. Mantén actualizados
                            datos como placa, modelo, pólizas y estado del vehículo para un control operativo claro.
                        </p>

                        @if(!empty($vehiculos) && count($vehiculos))
                            <div class="tax-veh-grid">
                                @foreach($vehiculos as $veh)
                                    @php
                                        $estadoVeh = strtolower($veh->estado_vehiculo ?? '');
                                        $verifVeh  = strtolower($veh->verificacion_estado ?? '');
                                        $estadoClass = 'green';
                                        $estadoText  = ucfirst($veh->estado_vehiculo ?? 'activo');
                                        if ($estadoVeh === 'inactivo') {
                                            $estadoClass = 'amber';
                                        } elseif ($estadoVeh === 'suspendido') {
                                            $estadoClass = 'red';
                                        }
                                        $verifClass = 'amber';
                                        $verifText  = ucfirst($veh->verificacion_estado ?? 'pendiente');
                                        if ($verifVeh === 'verificado') {
                                            $verifClass = 'green';
                                        } elseif ($verifVeh === 'rechazado') {
                                            $verifClass = 'red';
                                        }
                                    @endphp
                                    <div class="tax-veh-card">
                                        <div class="tax-veh-icon">
                                            <i class="fa fa-taxi"></i>
                                        </div>
                                        <div class="tax-veh-main">
                                            <div class="tax-veh-title">
                                                <span class="tax-veh-title-main">
                                                    {{ $veh->placa }}
                                                </span>
                                                <span class="tax-veh-title-sub">
                                                    {{ $veh->marca ?? 'Marca no registrada' }}
                                                    @if($veh->linea)
                                                        · {{ $veh->linea }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="tax-veh-chips">
                                                <span class="tax-veh-chip">
                                                    <i class="fa fa-calendar-o"></i>
                                                    Modelo {{ $veh->modelo_anio ?? 'N/D' }}
                                                </span>
                                                @if($veh->color)
                                                    <span class="tax-veh-chip">
                                                        <i class="fa fa-paint-brush"></i>
                                                        {{ $veh->color }}
                                                    </span>
                                                @endif
                                                <span class="tax-veh-chip">
                                                    <i class="fa fa-users"></i>
                                                    {{ $veh->asientos ?? 4 }} asientos
                                                </span>
                                                <span class="tax-veh-chip">
                                                    <i class="fa fa-tags"></i>
                                                    {{ strtoupper(str_replace('_', ' ', $veh->categoria)) }}
                                                </span>
                                                <span class="tax-veh-chip {{ $estadoClass }}">
                                                    <i class="fa fa-circle"></i>
                                                    Estado: {{ $estadoText }}
                                                </span>
                                                <span class="tax-veh-chip {{ $verifClass }}">
                                                    <i class="fa fa-shield"></i>
                                                    Verificación: {{ $verifText }}
                                                </span>
                                            </div>
                                            <div class="tax-veh-meta">
                                                <span>
                                                    SOAT:
                                                    @if($veh->soat_expira)
                                                        {{ $veh->soat_expira }}
                                                    @else
                                                        N/D
                                                    @endif
                                                </span>
                                                <span>
                                                    Tecnomecánica:
                                                    @if($veh->tecnomecanica_expira)
                                                        {{ $veh->tecnomecanica_expira }}
                                                    @else
                                                        N/D
                                                    @endif
                                                </span>
                                                <span>
                                                    Extracontractual:
                                                    @if($veh->seguro_extracontractual_expira)
                                                        {{ $veh->seguro_extracontractual_expira }}
                                                    @else
                                                        N/D
                                                    @endif
                                                </span>
                                                <span>
                                                    VIN:
                                                    @if($veh->vin)
                                                        {{ $veh->vin }}
                                                    @else
                                                        N/D
                                                    @endif
                                                </span>
                                                <span>
                                                    Creado: {{ $veh->created_at ?? '-' }}
                                                </span>
                                                <span>
                                                    Actualizado: {{ $veh->updated_at ?? '-' }}
                                                </span>
                                            </div>
                                            @if($veh->verificacion_notas)
                                                <div class="tax-help-text" style="margin-top:6px;">
                                                    <strong>Notas verificación:</strong>
                                                    {{ $veh->verificacion_notas }}
                                                </div>
                                            @endif
											<div class="mt-2">
    <a href="{{ url('vehiculos/edit/' . $veh->id) }}" class="btn btn-outline-light btn-xs rounded-pill">
        <i class="fa fa-edit"></i> Editar vehículo
    </a>
</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tax-empty-state">
                                <i class="fa fa-car"></i>
                                <span>
                                    Este conductor aún no tiene un vehículo registrado.
                                    Usa el botón <strong>«Agregar vehículo»</strong> para vincular uno a su perfil.
                                </span>
                            </div>
                        @endif

                        <div class="tax-section-title">
                            <h2>
                                <i class="fa fa-route"></i>
                                Viajes del conductor
                            </h2>
                            <span class="tax-section-tag">
                                Historial de todos los viajes
                            </span>
                        </div>

                        <div class="tax-table-wrap">
                            @if(!empty($viajes) && count($viajes))
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pasajero</th>
                                                <th>Origen</th>
                                                <th>Destino</th>
                                                <th>Estado</th>
                                                <th>Tarifa aplicada</th>
                                                <th>Valor pagado</th>
                                                <th>Moneda</th>
                                                <th>Pago regist.</th>
                                                <th>Fecha creación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($viajes as $viaje)
                                                <tr>
                                                    <td>{{ $viaje->id }}</td>
                                                    <td>{{ $viaje->pasajero_nombre ?? $viaje->pasajero_id ?? '-' }}</td>
                                                    <td>{{ $viaje->origen_texto ?? '-' }}</td>
                                                    <td>{{ $viaje->destino_texto ?? '-' }}</td>
                                                    <td>{{ $viaje->estado ?? '-' }}</td>
                                                    <td>
                                                        @if($viaje->tarifa_aplicada !== null)
                                                            {{ number_format($viaje->tarifa_aplicada, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($viaje->valor_pagado !== null)
                                                            {{ number_format($viaje->valor_pagado, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $viaje->moneda ?? 'COP' }}</td>
                                                    <td>
                                                        @if((int)($viaje->pago_registrado ?? 0) === 1)
                                                            <span class="tax-badge-pill ok">
                                                                <i class="fa fa-check"></i> Sí
                                                            </span>
                                                        @else
                                                            <span class="tax-badge-pill warn">
                                                                No
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $viaje->created_at ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="tax-empty-state">
                                    <i class="fa fa-info-circle"></i>
                                    <span>Este conductor aún no tiene viajes registrados.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="tax-empty-state mt-2">
                        <i class="fa fa-ban"></i>
                        <span>Ningún registro fue encontrado.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
