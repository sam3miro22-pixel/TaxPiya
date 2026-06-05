@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $can_add = $user->canAccess("tarifas/add");
    $can_edit = $user->canAccess("tarifas/edit");
    $can_delete = $user->canAccess("tarifas/delete");

    $field_name = request()->segment(3);
    $field_value = request()->segment(4);
    $pageTitle = "Tarifas";

    $total = $records->total();
    $activas = $records->where('activa',1)->count();
    $inactivas = $records->where('activa',0)->count();
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')

<style>
    body{ color:#e5e7eb !important; }

  
    .tx-resumen-box{
        background:#0f172a;
        padding:18px 22px;
        border-radius:16px;
        border:1px solid #1e293b;
        margin-bottom:25px;
        display:flex;
        justify-content:space-between;
        gap:18px;
    }
    .tx-res-item{
        flex:1;
        background:#1e293b;
        padding:16px;
        border-radius:12px;
        text-align:center;
    }
    .tx-res-item h4{
        font-size:16px;
        margin:0;
        color:#94a3b8;
        font-weight:600;
    }
    .tx-res-item span{
        display:block;
        margin-top:6px;
        font-size:22px;
        color:#38bdf8;
        font-weight:700;
    }

   
    .tx-tarifas-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(360px,1fr));
        gap:22px;
        margin-top:10px;
    }
    .tx-card{
        background:#0f172a;
        border-radius:18px;
        padding:22px;
        border:1px solid #1e293b;
        box-shadow:0 6px 20px rgba(0,0,0,0.45);
        transition:.25s;
        position:relative;
    }
    .tx-card:hover{ background:#16233a; }

    .tx-title{
        font-size:18px;
        font-weight:700;
        margin-bottom:10px;
        color:#38bdf8;
    }

    .tx-item{
        margin-bottom:8px;
        font-size:14px;
    }
    .tx-label{
        font-weight:700;
        color:#94a3b8;
        margin-right:4px;
    }

   
    .tx-tag-scope{
        display:inline-block;
        background:#334155;
        color:#f1f5f9;
        padding:5px 10px;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
        margin-bottom:12px;
    }

  
    .tx-status-activa{ color:#22c55e; font-weight:700; }
    .tx-status-inactiva{ color:#ef4444; font-weight:700; }

    /* -------- BOTONES -------- */
    .tx-actions{
        margin-top:18px;
        display:flex;
        gap:10px;
    }
    .tx-btn{
        flex:1;
        text-align:center;
        padding:8px 0;
        border-radius:10px;
        font-size:14px;
        font-weight:600;
        border:none;
        cursor:pointer;
    }
    .tx-btn-edit{
        background:#0ea5e9;
        color:#fff;
    }
    .tx-btn-delete{
        background:#dc2626;
        color:#fff;
    }
</style>


<section class="page" data-page-type="list" data-page-url="{{ url()->full() }}">

    <!-- HEADER -->
    <div class="py-3 mt-5 mb-2">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">

                <div class="col-auto">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>

                <div class="col">
                    <h4 class="text-primary fw-bold m-0">Tarifas</h4>
                </div>

                @if($can_add)
                <div class="col-auto">
                    <a class="btn btn-primary" href="{{ print_link('tarifas/add', true) }}">
                        <i class="fa fa-plus"></i> Nueva tarifa
                    </a>
                </div>
                @endif

                <div class="col-md-3">
                    <form class="search" action="{{ url()->current() }}" method="get">
                        <input type="hidden" name="page" value="1">
                        <div class="input-group">
                            <input value="{{ get_value('search') }}"
                                   class="form-control page-search"
                                   type="text"
                                   name="search"
                                   placeholder="Buscar tarifa...">
                            <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- RESUMEN -->
    <div class="container-fluid">
        <div class="tx-resumen-box">

            <div class="tx-res-item">
                <h4>Total tarifas</h4>
                <span>{{ $total }}</span>
            </div>

            <div class="tx-res-item">
                <h4>Activas</h4>
                <span>{{ $activas }}</span>
            </div>

            <div class="tx-res-item">
                <h4>Inactivas</h4>
                <span>{{ $inactivas }}</span>
            </div>

        </div>
    </div>


    <div class="container-fluid">
        @if($records->total())

        <div class="tx-tarifas-grid">

            @foreach($records as $data)

                <?php
                    $activo = $data['activa'] == 1 ? "Activa" : "Inactiva";
                    $classActivo = $data['activa'] == 1 ? "tx-status-activa" : "tx-status-inactiva";
                ?>

                <div class="tx-card">

                    <div class="tx-tag-scope">
                        {{ strtoupper($data['scope']) }}
                    </div>

                    <div class="tx-title">{{ $data['nombre'] }}</div>

                    @if($data['descripcion'])
                    <div class="tx-item">
                        <span class="tx-label">Descripción:</span>{{ $data['descripcion'] }}
                    </div>
                    @endif

                    <div class="tx-item"><span class="tx-label">Ciudad:</span>{{ $data['ciudad'] }}</div>
                    <div class="tx-item"><span class="tx-label">Categoría:</span>{{ $data['categoria'] }}</div>
                    <div class="tx-item"><span class="tx-label">Moneda:</span>{{ $data['moneda'] }}</div>
                    <div class="tx-item"><span class="tx-label">Monto fijo:</span>{{ number_format($data['monto_fijo'],0,',','.') }}</div>

                    <div class="tx-item">
                        <span class="tx-label">Recargos:</span>
                        Noc: {{ number_format($data['recargo_nocturno'],0,',','.') }},
                        Fest: {{ number_format($data['recargo_festivo'],0,',','.') }},
                        Aerop: {{ number_format($data['recargo_aeropuerto'],0,',','.') }}
                    </div>

                    <div class="tx-item"><span class="tx-label">Peajes incluidos:</span>{{ $data['incluye_peajes'] ? 'Sí' : 'No' }}</div>

                    <div class="tx-item">
                        <span class="tx-label">Vigencia:</span>
                        {{ $data['vigente_desde'] }} – {{ $data['vigente_hasta'] }}
                    </div>

                    <div class="tx-item">
                        <span class="tx-label">Estado:</span>
                        <span class="{{ $classActivo }}">{{ $activo }}</span>
                    </div>

                    <!-- ACCIONES -->
                    <div class="tx-actions">

                        @if($can_edit)
                        <a class="tx-btn tx-btn-edit"
                           href="{{ print_link("tarifas/edit/".$data['id']) }}">
                           Editar
                        </a>
                        @endif

                        @if($can_delete)
                        <a class="tx-btn tx-btn-delete record-delete-btn"
                           data-display-style="modal"
                           data-prompt-msg="¿Deseas eliminar esta tarifa?"
                           href="{{ print_link("tarifas/delete/".$data['id']) }}">
                           Eliminar
                        </a>
                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>

        @else

        <div class="text-center text-muted py-4">
            <i class="fa fa-ban"></i> No se encontraron tarifas registradas.
        </div>

        @endif

    </div>

</section>

@endsection
