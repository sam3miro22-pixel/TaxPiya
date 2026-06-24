@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $can_add = $user->canAccess("calificaciones/add");
    $can_edit = $user->canAccess("calificaciones/edit");
    $can_view = $user->canAccess("calificaciones/view");
    $can_delete = $user->canAccess("calificaciones/delete");
    $field_name = request()->segment(3);
    $field_value = request()->segment(4);
    $total_records = $records->total();
    $limit = $records->perPage();
    $record_count = count($records);
    $pageTitle = "Calificaciones";
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')

<style>
    body{
        color:#e5e7eb !important;
    }
    .cali-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(350px,1fr));
        gap:22px;
        margin-top:25px;
    }
    .cali-card{
        background:#0f172a;
        border-radius:18px;
        padding:22px;
        border:1px solid #1e293b;
        box-shadow:0 6px 20px rgba(0,0,0,0.45);
        transition:0.25s ease;
    }
    .cali-card:hover{
        background:#16233a;
    }
    .cali-title{
        font-size:18px;
        font-weight:700;
        margin-bottom:10px;
        color:#fbbf24;
    }
    .cali-block{
        margin-bottom:10px;
        font-size:14px;
    }
    .cali-label{
        font-weight:600;
        color:#94a3b8;
        margin-right:4px;
    }
    .star{
        color:#facc15;
        font-size:18px;
        margin-right:2px;
    }
    .cali-tags{
        background:#1e293b;
        padding:8px 12px;
        border-radius:10px;
        font-size:13px;
        color:#cbd5e1;
        margin-bottom:8px;
    }
</style>

<section class="page" data-page-type="list" data-page-url="{{ url()->full() }}">

    <div class="py-3 mt-5 mb-2">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">

                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url('home') }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>

                <div class="col">
                    <div class="h5 font-weight-bold text-primary m-0">Calificaciones</div>
                </div>

                <div class="col-md-3">
                    <form class="search" action="{{ url()->current() }}" method="get">
                        <input type="hidden" name="page" value="1">
                        <div class="input-group">
                            <input value="{{ get_value('search') }}"
                                   class="form-control page-search"
                                   type="text"
                                   name="search"
                                   placeholder="Buscar calificación...">
                            <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid">

        <?php Html::page_bread_crumb("/calificaciones/", $field_name, $field_value); ?>
        <?php Html::display_page_errors($errors); ?>

        @if($records->total())

        <div class="cali-grid">

            @foreach($records as $data)

                <?php
                    $rater = DB::table('users')->where('id', $data['rater_id'])->first();
                    $ratee = DB::table('users')->where('id', $data['ratee_id'])->first();

                    $raterName = $rater->name ?? 'Usuario desconocido';
                    $rateeName = $ratee->name ?? 'Usuario desconocido';

                    $stars = intval($data['puntuacion']);
                ?>

                <div class="cali-card">

                    <div class="cali-title">
                        Calificación de {{ $raterName }}
                    </div>

                    <div class="cali-block">
                        <span class="cali-label">Rol del que califica:</span>
                        {{ ucfirst($data['rater_rol']) }}
                    </div>

                    <div class="cali-block">
                        <span class="cali-label">Calificado:</span>
                        {{ $rateeName }}
                    </div>

                    <div class="cali-block">
                        <span class="cali-label">Rol del calificado:</span>
                        {{ ucfirst($data['ratee_rol']) }}
                    </div>

                    <div class="cali-block">
                        <span class="cali-label">Puntuación:</span>
                        @for($i=1; $i<=5; $i++)
                            <span class="star">{!! $i <= $stars ? '★' : '☆' !!}</span>
                        @endfor
                    </div>

                    @if($data['comentario'])
                    <div class="cali-block">
                        <span class="cali-label">Comentario:</span>
                        {{ $data['comentario'] }}
                    </div>
                    @endif

                    @if($data['etiquetas_json'])
                    <div class="cali-tags">
                        {{ $data['etiquetas_json'] }}
                    </div>
                    @endif

                    <div class="cali-block">
                        <span class="cali-label">Visible:</span>
                        {{ $data['visible'] ? 'Sí' : 'No' }}
                    </div>

                    <div class="cali-block">
                        <span class="cali-label">Moderado:</span>
                        {{ $data['moderado'] ? 'Sí' : 'No' }}
                    </div>

                    @if($data['moderado_motivo'])
                    <div class="cali-block">
                        <span class="cali-label">Motivo moderación:</span>
                        {{ $data['moderado_motivo'] }}
                    </div>
                    @endif

                    <div class="cali-block">
                        <span class="cali-label">Fecha:</span>
                        {{ $data['created_at'] }}
                    </div>

                </div>

            @endforeach

        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>

        @else

        <div class="text-center text-muted py-4">
            <i class="fa fa-ban"></i> No se encontraron calificaciones.
        </div>

        @endif

    </div>

</section>

@endsection
