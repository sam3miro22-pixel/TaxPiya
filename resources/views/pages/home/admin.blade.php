@inject('comp_model', 'App\Models\ComponentsData')

<?php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$pageTitle = "Admin";

$user = auth()->user();
$nombreAdmin = $user?->name ?? 'Admin';
$hoy = Carbon::now();
$hoyTextoLargo = $hoy->locale('es')->translatedFormat('d M Y');

$totalUsuarios = DB::table('users')->count();

$totalConductores = DB::table('conductores')->count();

$conductoresActivosAhora = DB::table('conductores')
    ->where('estado_operitivo', 1)
    ->where('disponible', 1)
    ->count();

$totalViajes = DB::table('viajes')->count();

$viajesHoy = DB::table('viajes')
    ->whereDate('created_at', $hoy->toDateString())
    ->count();

$viajesEnCurso = DB::table('viajes')
    ->whereIn('estado', ['buscando', 'asignado', 'en_camino', 'llego', 'iniciado'])
    ->count();

$ratingRow = DB::table('calificaciones')
    ->selectRaw('AVG(puntuacion) as avg_rating, COUNT(*) as total_califs')
    ->first();

$ratingPromedio = $ratingRow && $ratingRow->avg_rating ? round($ratingRow->avg_rating, 1) : null;
$totalCalificaciones = $ratingRow->total_califs ?? 0;

$estadosViajes = DB::table('viajes')
    ->select('estado', DB::raw('COUNT(*) as total'))
    ->groupBy('estado')
    ->pluck('total', 'estado')
    ->toArray();

$viajesTerminados = $estadosViajes['terminado'] ?? 0;
$viajesCancelPasajero = $estadosViajes['cancelado_pasajero'] ?? 0;

$desde = (clone $hoy)->subDays(6)->startOfDay();

$rows7 = DB::table('viajes')
    ->selectRaw('DATE(created_at) as f, COUNT(*) as total')
    ->whereBetween('created_at', [$desde, $hoy->endOfDay()])
    ->groupBy(DB::raw('DATE(created_at)'))
    ->orderBy('f')
    ->get()
    ->pluck('total', 'f')
    ->toArray();

$chart7Labels = [];
$chart7Data   = [];

for ($i = 0; $i < 7; $i++) {
    $d = (clone $desde)->addDays($i);
    $key = $d->toDateString();
    $chart7Labels[] = $d->format('d/m');
    $chart7Data[]   = $rows7[$key] ?? 0;
}

$viajesRecientes = DB::table('viajes as v')
    ->leftJoin('users as up', 'up.id', '=', 'v.pasajero_id')
    ->leftJoin('conductores as c', 'c.id', '=', 'v.conductor_id')
    ->leftJoin('users as uc', 'uc.id', '=', 'c.user_id')
    ->select(
        'v.id',
        'v.estado',
        'v.created_at',
        'up.name as pasajero',
        'uc.name as conductor'
    )
    ->orderBy('v.created_at', 'desc')
    ->limit(5)
    ->get();

$ultimosSos = DB::table('sos_incidentes')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

$totalSos = DB::table('sos_incidentes')->count();

$walletMovsHoy = DB::table('wallet_movimientos')
    ->whereDate('created_at', $hoy->toDateString())
    ->count();

$conductoresConSaldo = DB::table('wallet_saldos')
    ->where('saldo_actual', '>', 0)
    ->where('bloqueado', 0)
    ->count();

$conductoresMapa = DB::table('conductores as c')
    ->join('users as u', 'u.id', '=', 'c.user_id')
    ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
    ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
    ->where('c.estado_operitivo', 1)
    ->where('c.disponible', 1)
    ->selectRaw('
        c.id as conductor_id,
        u.name as nombre,
        p.lat,
        p.lng,
        p.velocidad_kmh,
        p.heading,
        COALESCE(p.actualizada_at, c.last_online_at) as last_at,
        v.placa,
        v.marca,
        v.linea
    ')
    ->get();

$mapCenter = [
    'lat' => 1.853711,
    'lng' => -76.050399
];
?>

@extends($layout)

@section('title', $pageTitle)

@section('content')
<div class="txp-admin-wrap">
    <div class="container-fluid">
        <div class="txp-admin-hero d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <div class="txp-admin-hello">Hola, {{ $nombreAdmin }}</div>
                <div class="txp-admin-subtitle">
                    Visión general de la operación Taxpiya: usuarios, conductores, viajes, SOS y wallet.
                </div>
            </div>
            <div class="txp-admin-date-pill">
                {{ $hoyTextoLargo }}
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12 col-md-3">
                <div class="txp-card txp-card-kpi">
                    <div class="txp-kpi-icon users">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="txp-kpi-label">Usuarios totales</div>
                    <div class="txp-kpi-value">{{ $totalUsuarios }}</div>
                    <a href="{{ url('users') }}" class="txp-kpi-link">
                        Ver usuarios <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="txp-card txp-card-kpi">
                    <div class="txp-kpi-icon drivers">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <div class="txp-kpi-label">Conductores registrados</div>
                    <div class="txp-kpi-value">{{ $totalConductores }}</div>
                    <div class="txp-kpi-sub">Activos ahora: {{ $conductoresActivosAhora }}</div>
                    <a href="{{ url('conductores') }}" class="txp-kpi-link">
                        Ver conductores <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="txp-card txp-card-kpi">
                    <div class="txp-kpi-icon rides">
                        <i class="fa fa-taxi"></i>
                    </div>
                    <div class="txp-kpi-label">Viajes</div>
                    <div class="txp-kpi-value">{{ $totalViajes }}</div>
                    <div class="txp-kpi-sub">
                        Hoy: {{ $viajesHoy }} · En curso: {{ $viajesEnCurso }}
                    </div>
                    <a href="{{ url('viajes') }}" class="txp-kpi-link">
                        Ver viajes <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="txp-card txp-card-kpi">
                    <div class="txp-kpi-icon rating">
                        <i class="fa fa-star"></i>
                    </div>
                    <div class="txp-kpi-label">Calificación global</div>
                    <div class="txp-kpi-value">
                        @if($ratingPromedio)
                            {{ $ratingPromedio }} <span class="txp-kpi-scale">/ 5</span>
                        @else
                            –
                        @endif
                    </div>
                    <div class="txp-kpi-sub">
                        {{ $totalCalificaciones }} calificaciones registradas
                    </div>
                    <a href="{{ url('calificaciones') }}" class="txp-kpi-link">
                        Ver calificaciones <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12 col-lg-7">
                <div class="txp-card h-100">
                    <div class="txp-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="txp-card-title">Conductores activos en mapa</div>
                            <div class="txp-card-subtitle">
                                Posición en tiempo real de conductores disponibles.
                            </div>
                        </div>
                        <div class="txp-pill-small">
                            Refresh 5s
                        </div>
                    </div>
                    <div id="txp-drivers-map" class="txp-map"></div>
                    <div class="txp-map-legend small mt-2">
                        <span class="me-3">
                            <span class="txp-dot txp-dot-driver"></span> Conductor activo
                        </span>
                        <span class="txp-text-muted">
                            Última actualización mostrada al hacer clic en el marcador.
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="txp-card mb-3">
                    <div class="txp-card-header">
                        <div class="txp-card-title">Distribución por estado</div>
                        <div class="txp-card-subtitle">
                            Estados actuales de los viajes.
                        </div>
                    </div>
                    <div class="txp-chart-wrap txp-chart-wrap-square">
                        <canvas id="txp-chart-estados"></canvas>
                    </div>
                    <div class="txp-chart-footer small">
                        <span>
                            Terminados:
                            <span class="text-success fw-semibold">{{ $viajesTerminados }}</span>
                        </span>
                        <span class="ms-3">
                            Cancelados pasajero:
                            <span class="text-danger fw-semibold">{{ $viajesCancelPasajero }}</span>
                        </span>
                    </div>
                </div>

                <div class="txp-card">
                    <div class="txp-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="txp-card-title">Tarifa activa</div>
                            <div class="txp-card-subtitle">Taxi base de la operación.</div>
                        </div>
                        <a href="{{ url('tarifas') }}" class="txp-pill-link">
                            Administrar tarifas <i class="fa fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                    @php
                        $tarifaActiva = DB::table('tarifas')
                            ->where('activa', 1)
                            ->first();
                    @endphp
                    @if($tarifaActiva)
                        <div class="txp-tarifa-body">
                            <div class="txp-tarifa-name">{{ $tarifaActiva->nombre }}</div>
                            <div class="txp-tarifa-city">
                                Ciudad: {{ $tarifaActiva->ciudad ?? 'No especificada' }}
                            </div>
                            <div class="txp-tarifa-price">
                                Monto fijo: {{ number_format($tarifaActiva->monto_fijo, 0, ',', '.') }} COP
                            </div>
                        </div>
                    @else
                        <div class="txp-empty small">
                            No hay una tarifa activa configurada.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12 col-lg-7">
                <div class="txp-card h-100">
                    <div class="txp-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="txp-card-title">Viajes recientes</div>
                            <div class="txp-card-subtitle">Últimos 5 viajes registrados.</div>
                        </div>
                        <a href="{{ url('viajes') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Ver todos
                        </a>
                    </div>
                    <div class="table-responsive txp-table-wrap">
                        <table class="table txp-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="text-uppercase small">ID</th>
                                    <th class="text-uppercase small">Pasajero</th>
                                    <th class="text-uppercase small">Conductor</th>
                                    <th class="text-uppercase small text-center">Estado</th>
                                    <th class="text-uppercase small text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($viajesRecientes as $v)
                                    @php
                                        $estadoLabel = $v->estado ?? '-';
                                        $estadoClass = 'badge-secondary';
                                        if ($v->estado === 'terminado') {
                                            $estadoClass = 'txp-badge-success';
                                        } elseif ($v->estado === 'cancelado_pasajero') {
                                            $estadoClass = 'txp-badge-danger';
                                        } elseif (in_array($v->estado, ['asignado', 'en_camino', 'iniciado'])) {
                                            $estadoClass = 'txp-badge-info';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-muted">#{{ $v->id }}</td>
                                        <td>{{ $v->pasajero ?? 'Sin pasajero' }}</td>
                                        <td>{{ $v->conductor ?? 'Sin asignar' }}</td>
                                        <td class="text-center">
                                            <span class="txp-badge {{ $estadoClass }}">
                                                {{ str_replace('_', ' ', $estadoLabel) }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">
                                            {{ $v->created_at ? Carbon::parse($v->created_at)->format('d/m H:i') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay viajes registrados aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                <div class="txp-card">
                    <div class="txp-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="txp-card-title">SOS e incidentes</div>
                            <div class="txp-card-subtitle">Últimas alertas de la operación.</div>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            Ver SOS
                        </a>
                    </div>
                    @if($totalSos == 0)
                        <div class="txp-empty small">
                            No hay incidentes SOS registrados.
                        </div>
                    @else
                        <ul class="list-unstyled mb-0 txp-sos-list">
                            @foreach($ultimosSos as $s)
                                <li class="txp-sos-item">
                                    <div class="txp-sos-dot"></div>
                                    <div class="txp-sos-text">
                                        <div class="small">
                                            Viaje #{{ $s->viaje_id }} · {{ $s->tipo ?? 'SOS' }}
                                        </div>
                                        <div class="txp-text-muted xsmall">
                                            {{ $s->created_at ? Carbon::parse($s->created_at)->format('d/m H:i') : '' }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="txp-card">
                    <div class="txp-card-header">
                        <div class="txp-card-title">Wallet conductores</div>
                        <div class="txp-card-subtitle">
                            Movimientos y saldos de los conductores.
                        </div>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col-6">
                            <div class="txp-metric-title">Movimientos hoy</div>
                            <div class="txp-metric-value">{{ $walletMovsHoy }}</div>
                            <a href="#" class="txp-kpi-link small">
                                Ver movimientos <i class="fa fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                        <div class="col-6">
                            <div class="txp-metric-title">Conductores con saldo</div>
                            <div class="txp-metric-value">{{ $conductoresConSaldo }}</div>
                            <a href="#" class="txp-kpi-link small">
                                Ver saldos <i class="fa fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3 mb-3">
            <div class="col-12">
                <div class="txp-card">
                    <div class="txp-card-header">
                        <div class="txp-card-title">Viajes últimos 7 días</div>
                        <div class="txp-card-subtitle">Tendencia de uso de la app.</div>
                    </div>
                    <div class="txp-chart-wrap txp-chart-wrap-large">
                        <canvas id="txp-chart-7dias"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('pagecss')
<style>
    .txp-admin-wrap {
        padding: 96px 16px 32px;
        background: radial-gradient(circle at top, #111827 0, #020617 55%, #000 100%);
        min-height: 100vh;
    }
    .txp-admin-hero {
        background: linear-gradient(135deg, rgba(55,65,81,.9), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 18px 20px;
        box-shadow: 0 16px 40px rgba(15,23,42,.9);
        border: 1px solid rgba(148,163,184,.4);
        color: #e5e7eb;
    }
    .txp-admin-hello {
        font-size: 1.35rem;
        font-weight: 700;
    }
    .txp-admin-subtitle {
        font-size: .9rem;
        color: rgba(226,232,240,.75);
    }
    .txp-admin-date-pill {
        background: #020617;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: .85rem;
        color: #e5e7eb;
        border: 1px solid rgba(148,163,184,.5);
    }

    .txp-card {
        background: radial-gradient(circle at top left, rgba(30,64,175,.35), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 16px 18px 14px;
        border: 1px solid rgba(30,64,175,.55);
        box-shadow: 0 18px 40px rgba(15,23,42,.85);
        color: #e5e7eb;
    }
    .txp-card-header {
        margin-bottom: .75rem;
    }
    .txp-card-title {
        font-size: .95rem;
        font-weight: 700;
    }
    .txp-card-subtitle {
        font-size: .8rem;
        color: rgba(148,163,184,.9);
    }
    .txp-text-muted {
        color: rgba(148,163,184,.9);
    }

    .txp-card-kpi {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .txp-kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
        box-shadow: 0 10px 26px rgba(15,23,42,.9);
        color: #111827;
    }
    .txp-kpi-icon.users {
        background: linear-gradient(135deg, #fbbf24, #f97316);
    }
    .txp-kpi-icon.drivers {
        background: linear-gradient(135deg, #fde68a, #fb923c);
    }
    .txp-kpi-icon.rides {
        background: linear-gradient(135deg, #38bdf8, #0ea5e9);
    }
    .txp-kpi-icon.rating {
        background: linear-gradient(135deg, #facc15, #f97316);
    }
    .txp-kpi-label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: rgba(209,213,219,.9);
    }
    .txp-kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.1;
    }
    .txp-kpi-scale {
        font-size: .9rem;
        font-weight: 600;
        color: rgba(209,213,219,.9);
    }
    .txp-kpi-sub {
        font-size: .8rem;
        color: rgba(148,163,184,.95);
    }
    .txp-kpi-link {
        margin-top: 4px;
        font-size: .78rem;
        color: #bfdbfe;
        text-decoration: none;
    }
    .txp-kpi-link:hover {
        color: #e5e7eb;
        text-decoration: underline;
    }

    .txp-pill-small {
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(129,140,248,.7);
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #a5b4fc;
    }
    .txp-pill-link {
        font-size: .75rem;
        border-radius: 999px;
        border: 1px solid rgba(248,250,252,.18);
        padding: 5px 10px;
        text-decoration: none;
        color: #e5e7eb;
    }
    .txp-pill-link:hover {
        background: rgba(15,23,42,.9);
    }

    .txp-chart-wrap {
        position: relative;
        height: 260px;
    }
    .txp-chart-wrap canvas {
        position: absolute;
        inset: 0;
        width: 100% !important;
        height: 100% !important;
        display: block;
    }
    .txp-chart-wrap-square {
        max-width: 260px;
        margin: 0 auto;
    }
    .txp-chart-wrap-large {
        height: 320px;
    }
    .txp-chart-footer {
        margin-top: .5rem;
        border-top: 1px solid rgba(30,64,175,.4);
        padding-top: .4rem;
        display: flex;
        justify-content: flex-start;
        gap: 1.5rem;
    }

    .txp-tarifa-body {
        margin-top: .35rem;
        font-size: .87rem;
    }
    .txp-tarifa-name {
        font-weight: 600;
    }
    .txp-tarifa-city,
    .txp-tarifa-price {
        color: rgba(226,232,240,.85);
    }

    .txp-empty {
        padding: 10px 4px;
        color: rgba(148,163,184,.95);
    }

    .txp-table-wrap {
        background: transparent;
    }
    .txp-table {
        border-collapse: separate;
        border-spacing: 0 6px;
        font-size: .83rem;
        color: #e5e7eb;
        background: transparent;
    }
    .txp-table thead {
        background: transparent;
    }
    .txp-table thead tr {
        background: transparent;
    }
    .txp-table thead th {
        border-bottom: none;
        color: rgba(148,163,184,.95);
        font-weight: 600;
        background-color: transparent !important;
    }
    .txp-table tbody {
        background-color: transparent !important;
    }
    .txp-table tbody tr {
        background: rgba(15,23,42,.9);
    }
    .txp-table tbody tr td {
        border-top: none;
        border-bottom: none;
        padding: 8px 10px;
		color:#fff;
        background-color: transparent !important;
    }
    .txp-table tbody tr:hover {
        background: rgba(30,64,175,.75);
    }
    .txp-table tbody tr:first-child td:first-child {
        border-top-left-radius: 10px;
    }
    .txp-table tbody tr:first-child td:last-child {
        border-top-right-radius: 10px;
    }
    .txp-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
    }
    .txp-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
    }

    .txp-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        text-transform: capitalize;
    }
    .txp-badge-success {
        background: rgba(22,163,74,.18);
        color: #4ade80;
    }
    .txp-badge-danger {
        background: rgba(239,68,68,.18);
        color: #fb7185;
    }
    .txp-badge-info {
        background: rgba(59,130,246,.18);
        color: #60a5fa;
    }

    .txp-sos-list {
        margin: 0;
        padding: 0;
    }
    .txp-sos-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 4px 0;
    }
    .txp-sos-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #fb7185;
        margin-top: 6px;
        box-shadow: 0 0 10px rgba(248,113,113,.9);
    }
    .txp-sos-text .xsmall {
        font-size: .7rem;
    }

    .txp-metric-title {
        font-size: .8rem;
        color: rgba(209,213,219,.9);
    }
    .txp-metric-value {
        font-size: 1.4rem;
        font-weight: 700;
    }

    .txp-map {
        width: 100%;
        height: 340px;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 14px 30px rgba(15,23,42,.8);
    }
    .txp-map-legend {
        color: rgba(148,163,184,.95);
    }
    .txp-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 999px;
    }
    .txp-dot-driver {
        background: #22d3ee;
        box-shadow: 0 0 12px rgba(56,189,248,.9);
    }

    @media (max-width: 768px) {
        .txp-admin-wrap {
            padding-top: 100px;
        }
        .txp-map {
            height: 260px;
        }
        .txp-chart-wrap {
            height: 220px;
        }
        .txp-chart-wrap-large {
            height: 260px;
        }
    }
</style>
@endsection

@section('pagejs')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const TXP_ESTADOS_DATA = {
        terminados: {{ $viajesTerminados }},
        cancelado_pasajero: {{ $viajesCancelPasajero }}
    };
    const TXP_7_LABELS = @json($chart7Labels);
    const TXP_7_DATA   = @json($chart7Data);

    const TXP_DRIVERS_INIT = @json($conductoresMapa);
    const TXP_MAP_CENTER   = @json($mapCenter);
    const TXP_DRIVERS_API  = "{{ url('/api/admin/active-drivers') }}";

    document.addEventListener('DOMContentLoaded', function () {
        const ctxEstados = document.getElementById('txp-chart-estados');
        if (ctxEstados) {
            new Chart(ctxEstados, {
                type: 'doughnut',
                data: {
                    labels: ['Terminado', 'Cancelado pasajero'],
                    datasets: [{
                        data: [TXP_ESTADOS_DATA.terminados, TXP_ESTADOS_DATA.cancelado_pasajero],
                        backgroundColor: ['#38bdf8', '#fb7185'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#e5e7eb',
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        const ctx7 = document.getElementById('txp-chart-7dias');
        if (ctx7) {
            new Chart(ctx7, {
                type: 'line',
                data: {
                    labels: TXP_7_LABELS,
                    datasets: [{
                        label: 'Viajes',
                        data: TXP_7_DATA,
                        tension: 0.35,
                        fill: true,
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56,189,248,0.15)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#38bdf8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(31,41,55,.5)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(31,41,55,.4)'
                            },
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            });
        }
    });

    let txpMap = null;
    let txpMarkers = {};

    function initMap() {
        const center = {
            lat: parseFloat(TXP_MAP_CENTER.lat) || 1.853711,
            lng: parseFloat(TXP_MAP_CENTER.lng) || -76.050399
        };

        txpMap = new google.maps.Map(document.getElementById('txp-drivers-map'), {
            center: center,
            zoom: 14,
            styles: [
                { elementType: 'geometry', stylers: [{ color: '#0b1120' }] },
                { elementType: 'labels.text.fill', stylers: [{ color: '#e5e7eb' }] },
                { elementType: 'labels.text.stroke', stylers: [{ color: '#020617' }] },
                { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#1e293b' }] },
                { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#020617' }] },
                { featureType: 'poi', elementType: 'labels.icon', stylers: [{ visibility: 'off' }] }
            ]
        });

        if (Array.isArray(TXP_DRIVERS_INIT) && TXP_DRIVERS_INIT.length) {
            updateDriversOnMap(TXP_DRIVERS_INIT);
        }

        fetchDriversRealtime();
        setInterval(fetchDriversRealtime, 5000);
    }

    function fetchDriversRealtime() {
        if (!TXP_DRIVERS_API) {
            return;
        }
        fetch(TXP_DRIVERS_API, { headers: { Accept: 'application/json' } })
            .then(function (r) {
                return r.ok ? r.json() : [];
            })
            .then(function (data) {
                if (Array.isArray(data)) {
                    updateDriversOnMap(data);
                }
            })
            .catch(function () {});
    }

    function updateDriversOnMap(drivers) {
        if (!txpMap) {
            return;
        }

        const seenIds = new Set();

        drivers.forEach(function (d) {
            const id = d.conductor_id;
            if (!id || !d.lat || !d.lng) {
                return;
            }

            const pos = { lat: parseFloat(d.lat), lng: parseFloat(d.lng) };
            seenIds.add(String(id));

            if (!txpMarkers[id]) {
                const marker = new google.maps.Marker({
                    position: pos,
                    map: txpMap,
                    icon: { url: "{{ asset('images/carrotaxpiya.png') }}", scaledSize: new google.maps.Size(26, 60), anchor: new google.maps.Point(13, 60) }

                });

                const info = new google.maps.InfoWindow();
                marker.addListener('click', function () {
                    info.open(txpMap, marker);
                });

                txpMarkers[id] = { marker: marker, info: info };
            } else {
                txpMarkers[id].marker.setPosition(pos);
            }

            const lastAt = d.last_at ? d.last_at : '';
            const vehiculo = [d.placa, d.marca, d.linea].filter(Boolean).join(' · ');

            const content =
                '<div style="min-width:200px;color:#0f172a;">' +
                    '<div style="font-weight:600;margin-bottom:2px;">' + (d.nombre || 'Conductor') + '</div>' +
                    '<div style="font-size:12px;margin-bottom:2px;">' + (vehiculo || 'Sin vehículo asociado') + '</div>' +
                    '<div style="font-size:11px;color:#4b5563;">Última actualización: ' + lastAt + '</div>' +
                    '<div style="font-size:11px;color:#4b5563;">Velocidad: ' + (d.velocidad_kmh || 0) + ' km/h</div>' +
                '</div>';

            txpMarkers[id].info.setContent(content);
        });

        Object.keys(txpMarkers).forEach(function (id) {
            if (!seenIds.has(String(id))) {
                txpMarkers[id].marker.setMap(null);
                delete txpMarkers[id];
            }
        });
    }
</script>


<script src="https://maps.googleapis.com/maps/api/js?key={{ config('taxpiya.google_maps_key') }}&callback=initMap" async defer></script>
@endsection
