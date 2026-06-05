@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Viajes";

    $total_records = $records->total();
    $limit = $records->perPage();
    $record_count = count($records);
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')

<style>
    body{
        color:#e5e7eb !important;
    }
    .txp-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(420px,1fr));
        gap:20px;
        margin-top:25px;
    }
    .txp-card{
        background:#0f172a;
        border-radius:18px;
        padding:20px;
        border:1px solid #1e293b;
        box-shadow:0 6px 20px rgba(0,0,0,0.45);
        transition:0.25s ease;
    }
    .txp-card:hover{
        background:#16233a;
    }
    .txp-flex{
        display:flex;
        align-items:center;
        justify-content:space-between;
        margin-bottom:14px;
    }
    .txp-title{
        font-size:18px;
        font-weight:700;
    }
    .txp-chip{
        padding:5px 12px;
        border-radius:20px;
        font-size:11px;
        font-weight:700;
        color:#fff;
    }
    .txp-chip-pendiente{ background:#ea580c; }
    .txp-chip-en_camino{ background:#2563eb; }
    .txp-chip-terminado{ background:#16a34a; }
    .txp-chip-cancelado{ background:#dc2626; }
    .txp-info-block{
        margin-bottom:8px;
        font-size:14px;
    }
    .txp-label{
        font-weight:600;
        color:#94a3b8;
        margin-right:4px;
    }
    .txp-map{
        width:100%;
        height:260px;
        border-radius:14px;
        margin-top:14px;
        border:1px solid #334155;
    }
</style>

<section class="page" data-page-type="list" data-page-url="{{ url()->full() }}">

    <div class="py-3 mt-5 mb-2">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">

                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>

                <div class="col">
                    <div class="h5 font-weight-bold text-primary m-0">Viajes</div>
                </div>

                <div class="col-md-3">
                    <form class="search" action="{{ url()->current() }}" method="get">
                        <input type="hidden" name="page" value="1">
                        <div class="input-group">
                            <input value="{{ get_value('search') }}"
                                   class="form-control page-search"
                                   type="text"
                                   name="search"
                                   placeholder="Buscar viaje...">
                            <button class="btn btn-primary">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid">

        @if($records->total())

        <div class="txp-grid">

            @foreach($records as $data)

                <?php
                    $pasajero  = DB::table('users')->where('id', $data['pasajero_id'])->first();
                    $conductor = DB::table('users')->where('id', $data['conductor_id'])->first();
                    $vehiculo  = DB::table('vehiculos')->where('id', $data['vehiculo_id'])->first();

                    $estado = strtolower($data['estado']);
                    $chip = "txp-chip-pendiente";
                    if($estado=="en_camino") $chip = "txp-chip-en_camino";
                    if($estado=="terminado") $chip = "txp-chip-terminado";
                    if($estado=="cancelado") $chip = "txp-chip-cancelado";

                    $mapId = "map_" . $data['id'];
                ?>

                <div class="txp-card">

                    <div class="txp-flex">
                        <div class="txp-title">Viaje #{{ $data['id'] }}</div>
                        <span class="txp-chip {{ $chip }}">{{ strtoupper($data['estado']) }}</span>
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Pasajero:</span>
                        {{ $pasajero->name ?? 'N/D' }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Conductor:</span>
                        {{ $conductor->name ?? 'N/D' }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Vehículo:</span>
                        {{ $vehiculo->placa ?? 'N/D' }} —
                        {{ $vehiculo->marca ?? '' }} {{ $vehiculo->modelo ?? '' }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Estimado:</span>
                        {{ $data['distancia_km_estimada'] }} km /
                        {{ $data['duracion_min_estimada'] }} min
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Real:</span>
                        {{ $data['distancia_km_real'] }} km /
                        {{ $data['duracion_min_real'] }} min
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Origen:</span>
                        {{ $data['origen_texto'] }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Destino:</span>
                        {{ $data['destino_texto'] }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Valor pagado:</span>
                        {{ $data['moneda'] }} {{ number_format($data['valor_pagado'], 0, ',', '.') }}
                    </div>

                    <div class="txp-info-block">
                        <span class="txp-label">Fecha:</span>
                        {{ $data['created_at'] }}
                    </div>

                    <div id="{{ $mapId }}" class="txp-map"></div>

                    <script>
    function initMap_{{ $data['id'] }}() {
        const origin = { lat: parseFloat("{{ $data['origen_lat'] }}"), lng: parseFloat("{{ $data['origen_lng'] }}") };
        const dest   = { lat: parseFloat("{{ $data['destino_lat'] }}"), lng: parseFloat("{{ $data['destino_lng'] }}") };

        const map = new google.maps.Map(document.getElementById("{{ $mapId }}"), {
            zoom: 14,
            center: origin,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#0f172a" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#0f172a" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#e2e8f0" }] },

                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [
                        { color: "#1e293b" },
                        { visibility: "on" }
                    ]
                },
                {
                    featureType: "road",
                    elementType: "geometry.stroke",
                    stylers: [
                        { color: "#334155" },
                        { weight: 1.3 }
                    ]
                },
                {
                    featureType: "road",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#cbd5e1" }]
                },

                {
                    featureType: "administrative",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#334155" }]
                },

                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{ color: "#1e293b" }]
                },

                {
                    featureType: "poi",
                    elementType: "geometry",
                    stylers: [{ color: "#152238" }]
                }
            ]
        });

        new google.maps.Marker({
            position: origin,
            map,
            label: "O"
        });

        new google.maps.Marker({
            position: dest,
            map,
            label: "D"
        });

        new google.maps.Polyline({
            path: [origin, dest],
            geodesic: true,
            strokeColor: "#0ea5e9",
            strokeOpacity: 1.0,
            strokeWeight: 4,
            map
        });
    }

    document.addEventListener("DOMContentLoaded", initMap_{{ $data['id'] }});
</script>


                </div>

            @endforeach

        </div>

        <div class="mt-4">

            <?php
                $pager = new Pagination($total_records, $record_count);
                $pager->show_page_count = false;
                $pager->show_record_count = true;
                $pager->show_page_limit = false;
                $pager->limit = $limit;
                $pager->show_page_number_list = true;
                $pager->pager_link_range = 5;
                $pager->render();
            ?>

        </div>

        @else

        <div class="text-center text-muted py-4">
            <i class="fa fa-ban"></i> No se encontraron viajes.
        </div>

        @endif

    </div>

</section>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('taxpiya.google_maps_key') }}"></script>

@endsection
