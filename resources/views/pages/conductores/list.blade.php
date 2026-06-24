@inject('comp_model', 'App\Models\ComponentsData')
<?php
use Illuminate\Support\Facades\DB;

//check if current user role is allowed access to the pages
$can_add    = $user->canAccess("conductores/add");
$can_edit   = $user->canAccess("conductores/edit");
$can_view   = $user->canAccess("conductores/view");
$can_delete = $user->canAccess("conductores/delete");

$field_name    = request()->segment(3);
$field_value   = request()->segment(4);
$total_records = $records->total();
$limit         = $records->perPage();
$record_count  = count($records);
$pageTitle     = "Conductores";

// Mapa rápido de usuarios para mostrar nombre
$userIds   = collect($records)->pluck('user_id')->filter()->unique()->all();
$userNames = [];
if (!empty($userIds)) {
    $userNames = DB::table('users')
        ->whereIn('id', $userIds)
        ->pluck('name', 'id')
        ->toArray();
}
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')
<section class="page txp-conductores-page" data-page-type="list" data-page-url="{{ url()->full() }}">
    <?php if($show_header == true){ ?>
    <div class="txp-conductores-header py-4 mb-3">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url('home') }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="d-flex flex-column">
                        <span class="txp-conductores-kicker">Gestión de flota</span>
                        <h1 class="txp-conductores-title mb-0">Conductores</h1>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    @if($can_add)
                        <a class="btn btn-conductores-primary" href="<?php print_link("conductores/add", true) ?>">
                            <i class="fa fa-plus me-1"></i> Agregar nuevo
                        </a>
                    @endif
                    <form class="search txp-conductores-search" action="{{ url()->current() }}" method="get">
                        <input type="hidden" name="page" value="1" />
                        <div class="input-group input-group-sm">
                            <input value="<?php echo get_value('search'); ?>" class="form-control txp-conductores-search-input" type="text" name="search" placeholder="Buscar conductor..." />
                            <button class="btn btn-outline-light">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="mb-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col comp-grid">
                    <div class="page-content txp-conductores-card">
                        <div id="conductores-list-records">
                            <div id="page-main-content" class="table-responsive txp-conductores-table-wrap">
                                <?php Html::page_bread_crumb("/conductores/", $field_name, $field_value); ?>
                                <?php Html::display_page_errors($errors); ?>

                                <div class="filter-tags mb-2">
                                    <?php Html::filter_tag('search', __('Search')); ?>
                                </div>

                                <table class="table txp-conductores-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <?php if($can_delete){ ?>
                                            <th class="td-checkbox">
                                                <label class="form-check-label mb-0">
                                                    <input class="toggle-check-all form-check-input" type="checkbox" />
                                                </label>
                                            </th>
                                            <?php } ?>
                                           
                                            <th class="td-user_id">Usuario</th>
                                            <th class="td-estado_operitivo">Estado operativo</th>
                                            <th class="td-disponible">Disponible</th>
                                            <th class="td-last_online_at">Última conexión</th>
                                            <th class="td-rating_promedio">Rating</th>
                                            <th class="td-total_viajes">Total viajes</th>
                                            <th class="td-licencia_numero">Licencia</th>
                                            <th class="td-licencia_categoria">Categoría</th>
                                            <th class="td-licencia_expira">Licencia expira</th>
                                            <th class="td-soat_numero">SOAT</th>
                                            <th class="td-soat_expira">SOAT expira</th>
                                            <th class="td-seguro_numero">Seguro</th>
                                            <th class="td-verificacion_estado">Verificación</th>
                                            
                                          
                                            <th class="td-contacto_emergencia_telefono">Teléfono emergencia</th>
                                            
                                            <th class="td-created_at">Creado</th>
                                            <th class="td-updated_at">Actualizado</th>
                                            <th class="td-btn text-end">Acciones</th>
                                        </tr>
                                    </thead>

                                    <?php if($total_records){ ?>
                                    <tbody class="page-data">
                                        <?php
                                        $counter = 0;
                                        foreach($records as $data){
                                            $rec_id = ($data['id'] ? urlencode($data['id']) : null);
                                            $counter++;

                                            $userId   = $data['user_id'] ?? null;
$userName = 'Sin usuario';

if ($userId) {
    $userName = DB::table('users')
        ->where('id', $userId)
        ->value('name') ?? 'Sin usuario';
}
                                            // Disponible
                                            $dispTexto = ($data['disponible'] ?? 0) ? 'Disponible' : 'No disponible';
                                            $dispClase = ($data['disponible'] ?? 0) ? 'badge-disp-on' : 'badge-disp-off';

                                            // Estado operativo
                                            $estadoOper = ($data['estado_operitivo'] ?? 0) ? 'Operativo' : 'No operativo';

                                            // Rating
                                            $rating    = (float)($data['rating_promedio'] ?? 0);
                                            if ($rating < 0) $rating = 0;
                                            if ($rating > 5) $rating = 5;
                                        ?>
                                        <tr>
                                            <?php if($can_delete){ ?>
                                            <td class="td-checkbox">
                                                <label class="form-check-label mb-0">
                                                    <input class="optioncheck form-check-input" name="optioncheck[]" value="<?php echo $data['id'] ?>" type="checkbox" />
                                                </label>
                                            </td>
                                            <?php } ?>

                                            

                                            <td class="td-user_id">
                                                <div class="txp-conductores-user">
                                                    <a href="<?php print_link("users/view/$data[user_id]"); ?>" class="txp-conductores-user-link">
                                                        <i class="fa fa-user-circle me-1"></i><?php echo $userName; ?>
                                                    </a>
                                                </div>
                                            </td>

                                            <td class="td-estado_operitivo">
                                                <span class="txp-conductores-pill">
                                                    <?php echo $estadoOper; ?>
                                                </span>
                                            </td>

                                            <td class="td-disponible">
                                                <span class="txp-conductores-badge-disp <?php echo $dispClase; ?>">
                                                    <?php echo $dispTexto; ?>
                                                </span>
                                            </td>

                                            <td class="td-last_online_at">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['last_online_at'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-rating_promedio">
                                                <div class="txp-conductores-rating">
                                                    <?php for($i=1; $i<=5; $i++): ?>
                                                        <?php
                                                            $starClass = $i <= floor($rating)
                                                                ? 'txp-star-full'
                                                                : ($i - $rating < 1 && $i > $rating ? 'txp-star-half' : 'txp-star-empty');
                                                        ?>
                                                        <i class="fa fa-star <?php echo $starClass; ?>"></i>
                                                    <?php endfor; ?>
                                                    <span class="txp-conductores-rating-num">
                                                        <?php echo number_format($rating, 1); ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="td-total_viajes">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['total_viajes'] ?? 0; ?>
                                                </span>
                                            </td>

                                            <td class="td-licencia_numero">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['licencia_numero'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-licencia_categoria">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['licencia_categoria'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-licencia_expira">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['licencia_expira'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-soat_numero">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['soat_numero'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-soat_expira">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['soat_expira'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-seguro_numero">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['seguro_numero'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-verificacion_estado">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['verificacion_estado'] ?? '—'; ?>
                                                </span>
                                            </td>

                                          

                                            <td class="td-contacto_emergencia_telefono">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['contacto_emergencia_telefono'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            

                                            <td class="td-created_at">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['created_at'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-updated_at">
                                                <span class="txp-conductores-text">
                                                    <?php echo $data['updated_at'] ?? '—'; ?>
                                                </span>
                                            </td>

                                            <td class="td-btn text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <?php if($can_view){ ?>
                                                    <a class="btn btn-sm btn-outline-light conductores-action-btn" href="<?php print_link("conductores/view/$rec_id"); ?>">
                                                        <i class="fa fa-eye me-1"></i> Ver
                                                    </a>
                                                    <?php } ?>
                                                    <?php if($can_edit){ ?>
                                                    <a class="btn btn-sm btn-conductores-primary conductores-action-btn" href="<?php print_link("conductores/edit/$rec_id"); ?>">
                                                        <i class="fa fa-edit me-1"></i> Editar
                                                    </a>
                                                    <?php } ?>
                                                    <?php if($can_delete){ ?>
                                                    <a class="btn btn-sm btn-outline-danger conductores-action-btn record-delete-btn"
                                                       data-prompt-msg="¿Seguro que quieres borrar este registro?"
                                                       data-display-style="modal"
                                                       href="<?php print_link("conductores/delete/$rec_id"); ?>">
                                                        <i class="fa fa-times me-1"></i> Borrar
                                                    </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tbody class="search-data"></tbody>
                                    <?php } else { ?>
                                    <tbody class="page-data">
                                        <tr>
                                            <td class="text-center text-muted p-3 txp-conductores-empty-row" colspan="1000">
                                                <i class="fa fa-ban me-1"></i> Ningún registro fue encontrado
                                            </td>
                                        </tr>
                                    </tbody>
                                    <?php } ?>
                                </table>
                            </div>

                            <?php if($show_footer){ ?>
                            <div class="mt-3">
                                <div class="row align-items-center justify-content-between g-2">
                                    <div class="col-md-auto d-flex gap-2">
                                        <?php if($can_delete){ ?>
                                        <button data-prompt-msg="¿Está seguro de que desea eliminar estos registros?"
                                                data-display-style="modal"
                                                data-url="<?php print_link("conductores/delete/{sel_ids}"); ?>"
                                                class="btn btn-danger btn-delete-selected d-none">
                                            <i class="fa fa-times me-1"></i> Eliminar seleccionado
                                        </button>
                                        <?php } ?>
                                    </div>
                                    <div class="col">
                                        <?php
                                        if($show_pagination == true){
                                            $pager = new Pagination($total_records, $record_count);
                                            $pager->show_page_count       = false;
                                            $pager->show_record_count     = true;
                                            $pager->show_page_limit       = false;
                                            $pager->limit                 = $limit;
                                            $pager->show_page_number_list = true;
                                            $pager->pager_link_range      = 5;
                                            $pager->render();
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@section('pagecss')
<style>
    .txp-conductores-page {
        background: radial-gradient(circle at top, #111827 0, #020617 55%, #000 100%);
        min-height: 100vh;
    }

    .txp-conductores-header {
        margin-top: 60px;
        background: transparent;
    }

    .txp-conductores-kicker {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .16em;
        color: rgba(148,163,184,.9);
    }

    .txp-conductores-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #f9fafb;
    }

    .txp-conductores-card {
        background: radial-gradient(circle at top left, rgba(30,64,175,.38), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 16px 18px 18px;
        border: 1px solid rgba(30,64,175,.6);
        box-shadow: 0 18px 38px rgba(15,23,42,.9);
        color: #e5e7eb;
    }

    .btn-conductores-primary {
        background: linear-gradient(135deg,#fbbf24,#f97316);
        border: none;
        color: #111827;
        font-weight: 600;
        border-radius: 999px;
        padding-inline: 16px;
        font-size: .8rem;
    }

    .btn-conductores-primary:hover {
        filter: brightness(1.05);
        color: #020617;
    }

    .txp-conductores-search-input {
        background-color: rgba(15,23,42,.95);
        border-color: rgba(148,163,184,.4);
        color: #e5e7eb;
    }

    .txp-conductores-search-input::placeholder {
        color: rgba(148,163,184,.8);
    }

    .txp-conductores-table-wrap {
        margin-top: 8px;
    }

    .txp-conductores-table {
        border-collapse: separate;
        border-spacing: 0 6px;
        font-size: .85rem;
        color: #e5e7eb;
        width: 100%;
    }

    .txp-conductores-table,
    .txp-conductores-table thead,
    .txp-conductores-table tbody,
    .txp-conductores-table tr,
    .txp-conductores-table th,
    .txp-conductores-table td {
        background-color: transparent !important;
        color: #e5e7eb !important;
    }

    .txp-conductores-table thead th {
        border-bottom: none;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: rgba(156,163,175,.9) !important;
        padding-top: 4px;
        padding-bottom: 6px;
    }

    .txp-conductores-table tbody tr {
        background: rgba(15,23,42,.96) !important;
    }

    .txp-conductores-table tbody tr td {
        border-top: none;
        border-bottom: none;
        padding: 9px 10px;
    }

    .txp-conductores-table tbody tr:hover {
        background: rgba(30,64,175,.9) !important;
    }

    .txp-conductores-id-link {
        color: rgba(148,163,184,.95);
        text-decoration: none;
    }

    .txp-conductores-id-link:hover {
        color: #e5e7eb;
    }

    .txp-conductores-user-link {
        color: #bfdbfe;
        text-decoration: none;
        font-size: .84rem;
    }

    .txp-conductores-user-link:hover {
        color: #e5e7eb;
        text-decoration: underline;
    }

    .txp-conductores-pill {
        padding: 3px 8px;
        border-radius: 999px;
        font-size: .74rem;
        background: rgba(30,64,175,.35);
        color: #bfdbfe;
    }

    .txp-conductores-badge-disp {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge-disp-on {
        background: rgba(22,163,74,.3);
        color: #bbf7d0;
    }

    .badge-disp-off {
        background: rgba(148,163,184,.35);
        color: #e5e7eb;
    }

    .txp-conductores-text {
        color: rgba(209,213,219,.9);
        font-size: .82rem;
    }

    .txp-conductores-rating {
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    .txp-conductores-rating i {
        font-size: .7rem;
    }

    .txp-star-full {
        color: #facc15;
    }

    .txp-star-half {
        color: #fde68a;
    }

    .txp-star-empty {
        color: #4b5563;
    }

    .txp-conductores-rating-num {
        margin-left: 4px;
        font-size: .74rem;
        color: rgba(148,163,184,.95);
    }

    .conductores-action-btn {
        border-radius: 999px;
        font-size: .78rem;
        padding-inline: 12px;
        padding-block: 4px;
    }

    .txp-conductores-empty-row {
        background: rgba(15,23,42,.96) !important;
        border-radius: 10px;
    }

    .page .pagination {
        margin-bottom: 0;
    }

    .page .pagination .page-link {
        background-color: #020617;
        border-color: rgba(148,163,184,.5);
        color: #e5e7eb;
    }

    .page .pagination .page-link:hover {
        background-color: #111827;
    }

    .page .pagination .page-item.active .page-link {
        background: linear-gradient(135deg,#fbbf24,#f97316);
        border-color: transparent;
        color: #111827;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .txp-conductores-page {
            padding-top: 88px;
        }
        .txp-conductores-card {
            padding: 12px 10px 12px;
        }
    }
</style>
@endsection
