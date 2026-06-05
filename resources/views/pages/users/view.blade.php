@inject('comp_model', 'App\Models\ComponentsData')
<?php
use Illuminate\Support\Facades\DB;

$can_add    = $user->canAccess("users/add");
$can_edit   = $user->canAccess("users/edit");
$can_view   = $user->canAccess("users/view");
$can_delete = $user->canAccess("users/delete");

$pageTitle  = "Ver";

$recId = $data['id'] ?? null;

$roleName = 'Sin rol';
if (!empty($data['user_role_id'])) {
    $roleName = DB::table('roles')
        ->where('role_id', $data['user_role_id'])
        ->value('role_name') ?? 'Sin rol';
}

$estadoTexto = 'Desconocido';
$estadoClase = 'badge-estado-unknown';
if (($data['estado'] ?? null) == 1) {
    $estadoTexto = 'Activo';
    $estadoClase = 'badge-estado-activo';
} elseif (($data['estado'] ?? null) == 2) {
    $estadoTexto = 'Inactivo';
    $estadoClase = 'badge-estado-inactivo';
}

$isPasajero  = (($data['user_role_id'] ?? null) == 2);
$isConductor = (($data['user_role_id'] ?? null) == 3);

$viajesPasajero   = [];
$conductorPerfil  = null;
$vehiculo         = null;
$documentos       = [];
$viajesConductor  = [];
$ultimaPos        = null;

if ($isPasajero && $recId) {
    $viajesPasajero = DB::table('viajes')
        ->where('pasajero_id', $recId)
        ->orderByDesc('id')
        ->limit(10)
        ->get();
}

if ($isConductor && $recId) {
    $conductorPerfil = DB::table('conductores')
        ->where('user_id', $recId)
        ->first();

    if ($conductorPerfil) {
        $vehiculo = DB::table('vehiculos')
            ->where('conductor_id', $conductorPerfil->id)
            ->first();

        $documentos = DB::table('documentos_conductor')
            ->where('conductor_id', $conductorPerfil->id)
            ->orderBy('tipo')
            ->get();

        $viajesConductor = DB::table('viajes')
            ->where('conductor_id', $conductorPerfil->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $ultimaPos = DB::table('conductor_posicion_actual')
            ->where('conductor_id', $conductorPerfil->id)
            ->first();
    }
}
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')
<section class="page txp-user-view-page" data-page-type="view" data-page-url="{{ url()->full() }}">
    <?php if($show_header == true){ ?>
    <div class="txp-user-view-header py-3 mb-3">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="d-flex flex-column">
                        <span class="txp-user-view-kicker">Detalle de usuario</span>
                        <h1 class="txp-user-view-title mb-0">
                            {{ $data['name'] ?? 'Usuario' }}
                        </h1>
                    </div>
                </div>
                <div class="col-auto d-flex gap-2 align-items-center">
                    <?php if($can_edit && $recId){ ?>
                        <a class="btn btn-sm btn-users-primary" href="<?php print_link("users/edit/$recId"); ?>">
                            <i class="fa fa-edit me-1"></i> Editar
                        </a>
                    <?php } ?>
                    <?php if($can_delete && $recId){ ?>
                        <a class="btn btn-sm btn-outline-danger"
                           data-prompt-msg="¿Seguro que quieres borrar este registro?"
                           data-display-style="modal"
                           href="<?php print_link("users/delete/$recId?redirect=users"); ?>">
                            <i class="fa fa-times me-1"></i> Borrar
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="mb-3">
        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="txp-user-main-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="txp-user-avatar-big">
                                <?php Html::page_img($data['fotoperfil'] ?? '', '80px', '80px', "small", 1); ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="txp-user-main-name">
                                    {{ $data['name'] ?? 'Usuario' }}
                                </div>
                                <div class="txp-user-main-email">
                                    {{ $data['email'] ?? 'Sin correo' }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="txp-user-chip-role">
                                        {{ $roleName }}
                                    </span>
                                    <span class="txp-user-chip-estado {{ $estadoClase }}">
                                        {{ $estadoTexto }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="txp-user-meta mt-3">
                            <div class="txp-user-meta-row">
                                <span class="txp-user-meta-label">ID</span>
                                <span class="txp-user-meta-value">#{{ $data['id'] ?? '—' }}</span>
                            </div>
                            <div class="txp-user-meta-row">
                                <span class="txp-user-meta-label">Teléfono</span>
                                <span class="txp-user-meta-value">
                                    {{ $data['telefono'] ?? 'No registrado' }}
                                </span>
                            </div>
                            <div class="txp-user-meta-row">
                                <span class="txp-user-meta-label">Creado</span>
                                <span class="txp-user-meta-value">
                                    {{ $data['date_created'] ?? ($data['created_at'] ?? '—') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="txp-user-summary mt-3">
                        @if($isPasajero)
                            <div class="txp-user-summary-title">
                                Resumen como pasajero
                            </div>
                            <div class="txp-user-summary-body">
                                <div class="txp-user-summary-item">
                                    <span class="label">Viajes registrados</span>
                                    <span class="value">
                                        @if(count($viajesPasajero))
                                            {{ count($viajesPasajero) }} (últimos 10)
                                        @else
                                            Sin viajes registrados
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @elseif($isConductor)
                            <div class="txp-user-summary-title">
                                Resumen como conductor
                            </div>
                            <div class="txp-user-summary-body">
                                <div class="txp-user-summary-item">
                                    <span class="label">Perfil de conductor</span>
                                    <span class="value">
                                        {{ $conductorPerfil ? 'Registrado' : 'No configurado' }}
                                    </span>
                                </div>
                                <div class="txp-user-summary-item">
                                    <span class="label">Vehículo asociado</span>
                                    <span class="value">
                                        @if($vehiculo)
                                            {{ $vehiculo->placa }} · {{ $vehiculo->marca }} {{ $vehiculo->linea }}
                                        @else
                                            No asociado
                                        @endif
                                    </span>
                                </div>
                                <div class="txp-user-summary-item">
                                    <span class="label">Documentos cargados</span>
                                    <span class="value">
                                        {{ count($documentos) }}
                                    </span>
                                </div>
                                <div class="txp-user-summary-item">
                                    <span class="label">Viajes como conductor</span>
                                    <span class="value">
                                        @if(count($viajesConductor))
                                            {{ count($viajesConductor) }} (últimos 10)
                                        @else
                                            Sin viajes registrados
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="txp-user-summary-title">
                                Resumen general
                            </div>
                            <div class="txp-user-summary-body">
                                <div class="txp-user-summary-item">
                                    <span class="label">Rol</span>
                                    <span class="value">{{ $roleName }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="txp-user-panel">
                        @if($isPasajero)
                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Viajes del pasajero</div>
                                        <div class="txp-user-block-subtitle">
                                            Últimos viajes asociados a este usuario como pasajero.
                                        </div>
                                    </div>
                                </div>
                                <div class="txp-user-table-wrap">
                                    @if(count($viajesPasajero))
                                        <table class="table txp-user-subtable mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Estado</th>
                                                    <th>Origen</th>
                                                    <th>Destino</th>
                                                    <th>Valor</th>
                                                    <th>Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($viajesPasajero as $v)
                                                    @php
                                                        $valorViaje = $v->valor_pagado ?? $v->tarifa_aplicada;
                                                    @endphp
                                                    <tr>
                                                        <td>#{{ $v->id }}</td>
                                                        <td>{{ $v->estado }}</td>
                                                        <td>{{ $v->origen_texto ?? '—' }}</td>
                                                        <td>{{ $v->destino_texto ?? '—' }}</td>
                                                        <td>
                                                            @if(!is_null($valorViaje))
                                                                ${{ number_format($valorViaje, 0, ',', '.') }}
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ $v->created_at }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="txp-user-empty">
                                            <i class="fa fa-info-circle me-2"></i>
                                            Este pasajero aún no tiene viajes registrados.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($isConductor)
                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Perfil de conductor</div>
                                        <div class="txp-user-block-subtitle">
                                            Información básica asociada al conductor.
                                        </div>
                                    </div>
                                    @if($conductorPerfil)
                                        <div class="txp-user-block-actions">
                                            <a href="{{ url('conductores/view/'.$conductorPerfil->id) }}" class="btn btn-sm btn-outline-light me-1">
                                                <i class="fa fa-id-card-o me-1"></i> Ver perfil
                                            </a>
                                            <a href="{{ url('conductores/edit/'.$conductorPerfil->id) }}" class="btn btn-sm btn-users-primary">
                                                <i class="fa fa-edit me-1"></i> Editar perfil
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="txp-user-meta-grid">
                                    <div class="txp-user-meta-card">
                                        <div class="label">ID Conductor</div>
                                        <div class="value">
                                            {{ $conductorPerfil ? $conductorPerfil->id : '—' }}
                                        </div>
                                    </div>
                                    <div class="txp-user-meta-card">
                                        <div class="label">Estado operativo</div>
                                        <div class="value">
                                            @if($conductorPerfil)
                                                {{ $conductorPerfil->estado_operitivo ? 'Operativo' : 'No operativo' }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="txp-user-meta-card">
                                        <div class="label">Disponible</div>
                                        <div class="value">
                                            @if($conductorPerfil)
                                                {{ $conductorPerfil->disponible ? 'Disponible' : 'No disponible' }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Vehículo asociado</div>
                                        <div class="txp-user-block-subtitle">
                                            Datos del vehículo registrado para este conductor.
                                        </div>
                                    </div>
                                </div>
                                @if($vehiculo)
                                    <div class="txp-user-meta-grid">
                                        <div class="txp-user-meta-card">
                                            <div class="label">Placa</div>
                                            <div class="value">{{ $vehiculo->placa }}</div>
                                        </div>
                                        <div class="txp-user-meta-card">
                                            <div class="label">Marca</div>
                                            <div class="value">{{ $vehiculo->marca }}</div>
                                        </div>
                                        <div class="txp-user-meta-card">
                                            <div class="label">Línea</div>
                                            <div class="value">{{ $vehiculo->linea }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="txp-user-empty">
                                        <i class="fa fa-car me-2"></i>
                                        No hay vehículo asociado a este conductor.
                                    </div>
                                @endif
                            </div>

                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Última ubicación del conductor</div>
                                        <div class="txp-user-block-subtitle">
                                            Posición más reciente registrada por la app
                                        </div>
                                    </div>
                                </div>
                                @if($ultimaPos)
                                    <div class="row g-3">
                                        <div class="col-12 col-md-7">
                                            <div id="txp-user-map-lastpos"></div>
                                        </div>
                                        <div class="col-12 col-md-5">
                                            <div class="txp-user-meta-grid">
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Lat / Lng</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->lat }}, {{ $ultimaPos->lng }}
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Precisión</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->precision_m ?? '—' }} m
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Velocidad</div>
                                                    <div class="value">
                                                        @if(!is_null($ultimaPos->velocidad_kmh))
                                                            {{ $ultimaPos->velocidad_kmh }} km/h
                                                        @else
                                                            —
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Origen posición</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->origen ?? '—' }}
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Proveedor</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->provider ?? '—' }}
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Batería</div>
                                                    <div class="value">
                                                        @if(!is_null($ultimaPos->bateria))
                                                            {{ $ultimaPos->bateria }}%
                                                        @else
                                                            —
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Estado app</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->app_estado ?? '—' }}
                                                    </div>
                                                </div>
                                                <div class="txp-user-meta-card">
                                                    <div class="label">Actualizada</div>
                                                    <div class="value">
                                                        {{ $ultimaPos->actualizada_at }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="txp-user-empty">
                                        <i class="fa fa-map-marker me-2"></i>
                                        Aún no hay posición registrada para este conductor.
                                    </div>
                                @endif
                            </div>

                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Documentos del conductor</div>
                                        <div class="txp-user-block-subtitle">
                                            Documentación cargada para validación y control operativo.
                                        </div>
                                    </div>
                                </div>
                                <div class="txp-user-table-wrap">
                                    @if(count($documentos))
                                        <table class="table txp-user-subtable mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Número</th>
                                                    <th>Vencimiento</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($documentos as $doc)
                                                    <tr>
                                                        <td>{{ $doc->tipo }}</td>
                                                        <td>{{ $doc->numero ?? '—' }}</td>
                                                        <td>{{ $doc->fecha_vencimiento ?? '—' }}</td>
                                                        <td>{{ $doc->estado ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="txp-user-empty">
                                            <i class="fa fa-file-text-o me-2"></i>
                                            Este conductor aún no tiene documentos cargados.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Viajes del conductor</div>
                                        <div class="txp-user-block-subtitle">
                                            Últimos viajes realizados por este conductor.
                                        </div>
                                    </div>
                                </div>
                                <div class="txp-user-table-wrap">
                                    @if(count($viajesConductor))
                                        <table class="table txp-user-subtable mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Estado</th>
                                                    <th>Origen</th>
                                                    <th>Destino</th>
                                                    <th>Valor</th>
                                                    <th>Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($viajesConductor as $v)
                                                    @php
                                                        $valorViaje = $v->valor_pagado ?? $v->tarifa_aplicada;
                                                    @endphp
                                                    <tr>
                                                        <td>#{{ $v->id }}</td>
                                                        <td>{{ $v->estado }}</td>
                                                        <td>{{ $v->origen_texto ?? '—' }}</td>
                                                        <td>{{ $v->destino_texto ?? '—' }}</td>
                                                        <td>
                                                            @if(!is_null($valorViaje))
                                                                ${{ number_format($valorViaje, 0, ',', '.') }}
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ $v->created_at }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="txp-user-empty">
                                            <i class="fa fa-road me-2"></i>
                                            Este conductor aún no tiene viajes registrados.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="txp-user-block">
                                <div class="txp-user-block-header">
                                    <div>
                                        <div class="txp-user-block-title">Información del usuario</div>
                                        <div class="txp-user-block-subtitle">
                                            Detalle básico del usuario en la plataforma.
                                        </div>
                                    </div>
                                </div>
                                <div class="txp-user-meta-grid">
                                    <div class="txp-user-meta-card">
                                        <div class="label">Rol</div>
                                        <div class="value">{{ $roleName }}</div>
                                    </div>
                                    <div class="txp-user-meta-card">
                                        <div class="label">Estado</div>
                                        <div class="value">{{ $estadoTexto }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <?php if(!$data){ ?>
                    <div class="txp-user-empty mt-3">
                        <i class="fa fa-ban me-2"></i> Ningún registro fue encontrado
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pagecss')
<style>
    .txp-user-view-page {
        background: radial-gradient(circle at top, #111827 0, #020617 55%, #000 100%);
        min-height: 100vh;
    }
    .txp-user-view-header {
        margin-top: 60px;
        background: transparent;
    }
    .txp-user-view-kicker {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .16em;
        color: rgba(148,163,184,.9);
    }
    .txp-user-view-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #f9fafb;
    }
    .txp-user-main-card {
        background: radial-gradient(circle at top left, rgba(30,64,175,.5), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 18px 18px 16px;
        border: 1px solid rgba(30,64,175,.7);
        box-shadow: 0 18px 38px rgba(15,23,42,.9);
        color: #e5e7eb;
    }
    .txp-user-avatar-big img {
        width: 80px;
        height: 80px;
        border-radius: 999px;
        object-fit: cover;
        border: 2px solid rgba(250,204,21,.9);
        box-shadow: 0 0 18px rgba(250,204,21,.55);
    }
    .txp-user-main-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #f9fafb;
    }
    .txp-user-main-email {
        font-size: .85rem;
        color: #bfdbfe;
    }
    .txp-user-chip-role {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        background: rgba(59,130,246,.2);
        color: #bfdbfe;
    }
    .txp-user-chip-estado {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
    }
    .badge-estado-activo {
        background: rgba(22,163,74,.3);
        color: #bbf7d0;
    }
    .badge-estado-inactivo {
        background: rgba(148,163,184,.35);
        color: #e5e7eb;
    }
    .badge-estado-unknown {
        background: rgba(248,113,113,.28);
        color: #fecaca;
    }
    .txp-user-meta {
        margin-top: 10px;
        border-top: 1px solid rgba(55,65,81,.6);
        padding-top: 10px;
    }
    .txp-user-meta-row {
        display: flex;
        justify-content: space-between;
        font-size: .8rem;
        margin-bottom: 4px;
    }
    .txp-user-meta-label {
        color: rgba(156,163,175,.9);
    }
    .txp-user-meta-value {
        color: #e5e7eb;
        font-weight: 500;
    }
    .txp-user-summary {
        background: rgba(15,23,42,.96);
        border-radius: 14px;
        padding: 14px 14px 12px;
        border: 1px solid rgba(31,41,55,.9);
        color: #e5e7eb;
        box-shadow: 0 10px 26px rgba(15,23,42,.85);
    }
    .txp-user-summary-title {
        font-size: .85rem;
        font-weight: 600;
        color: #f9fafb;
        margin-bottom: 6px;
    }
    .txp-user-summary-body {
        font-size: .8rem;
    }
    .txp-user-summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .txp-user-summary-item .label {
        color: rgba(156,163,175,.9);
    }
    .txp-user-summary-item .value {
        color: #e5e7eb;
        font-weight: 500;
    }
    .txp-user-panel {
        background: radial-gradient(circle at top right, rgba(30,64,175,.35), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 16px 18px 18px;
        border: 1px solid rgba(30,64,175,.7);
        box-shadow: 0 20px 38px rgba(15,23,42,.95);
        color: #e5e7eb;
    }
    .txp-user-block {
        border-radius: 14px;
        padding: 14px 12px 12px;
        background: rgba(15,23,42,.95);
        border: 1px solid rgba(31,41,55,.9);
        margin-bottom: 12px;
    }
    .txp-user-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        gap: 8px;
    }
    .txp-user-block-title {
        font-size: .9rem;
        font-weight: 600;
        color: #f9fafb;
    }
    .txp-user-block-subtitle {
        font-size: .78rem;
        color: rgba(156,163,175,.9);
    }
    .txp-user-block-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .txp-user-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 8px;
        margin-top: 6px;
    }
    .txp-user-meta-card {
        background: rgba(15,23,42,.98);
        border-radius: 10px;
        padding: 8px 10px;
        border: 1px solid rgba(31,41,55,.9);
        font-size: .8rem;
    }
    .txp-user-meta-card .label {
        color: rgba(156,163,175,.9);
        font-size: .75rem;
    }
    .txp-user-meta-card .value {
        color: #e5e7eb;
        font-weight: 500;
        margin-top: 2px;
        word-break: break-word;
    }
    .txp-user-table-wrap {
        margin-top: 6px;
    }
    .txp-user-subtable {
        border-collapse: separate;
        border-spacing: 0 4px;
        font-size: .78rem;
        width: 100%;
    }
    .txp-user-subtable,
    .txp-user-subtable thead,
    .txp-user-subtable tbody,
    .txp-user-subtable tr,
    .txp-user-subtable th,
    .txp-user-subtable td {
        background-color: transparent !important;
        color: #e5e7eb !important;
    }
    .txp-user-subtable thead th {
        border-bottom: none;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .7rem;
        color: rgba(156,163,175,.9) !important;
        padding-top: 4px;
        padding-bottom: 4px;
    }
    .txp-user-subtable tbody tr {
        background: rgba(15,23,42,.98) !important;
    }
    .txp-user-subtable tbody tr td {
        border-top: none;
        border-bottom: none;
        padding: 6px 8px;
    }
    .txp-user-subtable tbody tr:hover {
        background: rgba(30,64,175,.9) !important;
    }
    .txp-user-empty {
        font-size: .8rem;
        color: rgba(156,163,175,.95);
        padding: 10px 8px;
        border-radius: 10px;
        background: rgba(15,23,42,.98);
        border: 1px dashed rgba(55,65,81,.9);
    }
    .btn-users-primary {
        background: linear-gradient(135deg,#fbbf24,#f97316);
        border: none;
        color: #111827;
        font-weight: 600;
        border-radius: 999px;
        padding-inline: 16px;
    }
    .btn-users-primary:hover {
        filter: brightness(1.05);
        color: #020617;
    }
    .txp-user-view-page .table,
    .txp-user-view-page .card,
    .txp-user-view-page .page-content {
        background-color: transparent !important;
        color: #e5e7eb !important;
    }
    #txp-user-map-lastpos {
        width: 100%;
        min-height: 220px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(30,64,175,.8);
        box-shadow: 0 12px 28px rgba(15,23,42,.9);
    }
    @media (max-width: 767.98px) {
        .txp-user-view-page {
            padding-top: 88px;
        }
        .txp-user-panel {
            padding: 12px 10px 12px;
        }
        .txp-user-main-card {
            padding: 14px 12px 12px;
        }
    }
</style>
@endsection

@section('pagejs')
@if($isConductor && $conductorPerfil && $ultimaPos)
<script>
    function initTxpUserLastPosMap() {
        var el = document.getElementById('txp-user-map-lastpos');
        if (!el) return;

        var center = {
            lat: {{ (float) $ultimaPos->lat }},
            lng: {{ (float) $ultimaPos->lng }}
        };

        var mapStyles = [
            { elementType: "geometry", stylers: [{ color: "#0b1120" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#e5e7eb" }] },
            { elementType: "labels.text.stroke", stylers: [{ color: "#020617" }] },
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [{ color: "#111827" }]
            },
            {
                featureType: "road",
                elementType: "labels.text.fill",
                stylers: [{ color: "#9ca3af" }]
            },
            {
                featureType: "poi",
                elementType: "geometry",
                stylers: [{ color: "#020617" }]
            },
            {
                featureType: "water",
                elementType: "geometry",
                stylers: [{ color: "#020617" }]
            },
            {
                featureType: "transit",
                stylers: [{ visibility: "off" }]
            }
        ];

        var map = new google.maps.Map(el, {
            center: center,
            zoom: 15,
            styles: mapStyles,
            disableDefaultUI: true
        });

        new google.maps.Marker({
            position: center,
            map: map,
            title: "Última ubicación"
        });
    }
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('taxpiya.google_maps_key') }}&callback=initTxpUserLastPosMap"></script>

@endif
@endsection
