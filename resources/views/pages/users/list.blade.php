@inject('comp_model', 'App\Models\ComponentsData')
<?php
use Illuminate\Support\Facades\DB;

$can_add    = $user->canAccess("users/add");
$can_edit   = $user->canAccess("users/edit");
$can_view   = $user->canAccess("users/view");
$can_delete = $user->canAccess("users/delete");

$field_name    = request()->segment(3);
$field_value   = request()->segment(4);
$total_records = $records->total();
$limit         = $records->perPage();
$record_count  = count($records);
$pageTitle     = "Users";
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')
<section class="page" data-page-type="list" data-page-url="{{ url()->full() }}">
    <?php if($show_header == true){ ?>
    <div class="users-header-wrap py-4 mb-3">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="users-page-title">Usuarios</div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    @if($can_add)
                        <a class="btn btn-primary users-add-btn" href="<?php print_link('users/add', true) ?>">
                            <i class="fa fa-plus me-1"></i> Agregar nuevo
                        </a>
                    @endif
                    <form class="search users-search" action="{{ url()->current() }}" method="get">
                        <input type="hidden" name="page" value="1" />
                        <div class="input-group input-group-sm">
                            <input value="<?php echo get_value('search'); ?>" class="form-control page-search" type="text" name="search" placeholder="Buscar usuario..." />
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
                    <div class="page-content users-card">
                        <div id="users-list-records">
                            <div id="page-main-content" class="table-responsive users-table-wrap">
                                <?php Html::page_bread_crumb("/users/", $field_name, $field_value); ?>
                                <?php Html::display_page_errors($errors); ?>

                                <div class="filter-tags mb-2">
                                    <?php Html::filter_tag('search', __('Search')); ?>
                                </div>

                                <table class="table txp-users-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <?php if($can_delete){ ?>
                                            <th class="td-checkbox">
                                                <label class="form-check-label mb-0">
                                                    <input class="toggle-check-all form-check-input" type="checkbox" />
                                                </label>
                                            </th>
                                            <?php } ?>
                                            <th class="td-id">ID</th>
                                            <th class="td-name">Nombre</th>
                                            <th class="td-telefono">Teléfono</th>
                                            <th class="td-email">Email</th>
                                            <th class="td-fotoperfil">Foto de perfil</th>
                                            <th class="td-estado">Estado</th>
                                            <th class="td-user_role_id">Rol</th>
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

                                            $estadoTexto  = 'Desconocido';
                                            $estadoClase  = 'badge-estado-unknown';
                                            if($data['estado'] == 1){
                                                $estadoTexto = 'Activo';
                                                $estadoClase = 'badge-estado-activo';
                                            }
                                            elseif($data['estado'] == 2){
                                                $estadoTexto = 'Inactivo';
                                                $estadoClase = 'badge-estado-inactivo';
                                            }

                                            $roleName = 'Sin rol';
                                            if(!empty($data['user_role_id'])){
                                                $roleName = DB::table('roles')
                                                    ->where('role_id', $data['user_role_id'])
                                                    ->value('role_name') ?? 'Sin rol';
                                            }
                                        ?>
                                        <tr>
                                            <?php if($can_delete){ ?>
                                            <td class="td-checkbox">
                                                <label class="form-check-label mb-0">
                                                    <input class="optioncheck form-check-input" name="optioncheck[]" value="<?php echo $data['id'] ?>" type="checkbox" />
                                                </label>
                                            </td>
                                            <?php } ?>

                                            <td class="td-id text-muted fw-semibold">
                                                <a href="<?php print_link("/users/view/$data[id]") ?>" class="users-id-link">
                                                    #<?php echo $data['id']; ?>
                                                </a>
                                            </td>

                                            <td class="td-name">
                                                <div class="users-name">
                                                    <?php echo $data['name']; ?>
                                                </div>
                                            </td>

                                            <td class="td-telefono">
                                                <div class="users-text">
                                                    <?php echo $data['telefono']; ?>
                                                </div>
                                            </td>

                                            <td class="td-email">
                                                <a href="<?php print_link("mailto:$data[email]") ?>" class="users-email">
                                                    <?php echo $data['email']; ?>
                                                </a>
                                            </td>

                                            <td class="td-fotoperfil">
                                                <div class="users-avatar">
                                                    <?php Html::page_img($data['fotoperfil'], '40px', '40px', "small", 1); ?>
                                                </div>
                                            </td>

                                            <td class="td-estado">
                                                <span class="badge users-estado-pill {{ $estadoClase }}">
                                                    {{ $estadoTexto }}
                                                </span>
                                            </td>

                                            <td class="td-user_role_id">
                                                <span class="users-role-text">
                                                    {{ $roleName }}
                                                </span>
                                            </td>

                                            <td class="td-btn text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <?php if($can_view){ ?>
                                                    <a class="btn btn-sm btn-outline-light users-action-btn" href="<?php print_link("users/view/$rec_id"); ?>">
                                                        <i class="fa fa-eye me-1"></i> Ver
                                                    </a>
                                                    <?php } ?>
                                                    <?php if($can_edit){ ?>
                                                    <a class="btn btn-sm btn-users-primary users-action-btn" href="<?php print_link("users/edit/$rec_id"); ?>">
                                                        <i class="fa fa-edit me-1"></i> Editar
                                                    </a>
                                                    <?php } ?>
                                                    <?php if($can_delete){ ?>
                                                    <a class="btn btn-sm btn-outline-danger users-action-btn record-delete-btn"
                                                       data-prompt-msg="¿Seguro que quieres borrar este registro?"
                                                       data-display-style="modal"
                                                       href="<?php print_link("users/delete/$rec_id"); ?>">
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
                                            <td class="text-center text-muted p-3 users-empty-row" colspan="1000">
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
                                                data-url="<?php print_link("users/delete/{sel_ids}"); ?>"
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
    .users-list-page {
        padding: 96px 16px 32px;
        background: radial-gradient(circle at top, #111827 0, #020617 55%, #000 100%);
        min-height: 100vh;
    }

    .users-header-wrap {
        background: transparent;
		margin-top:60px;
    }

    .users-card {
        background: radial-gradient(circle at top left, rgba(30,64,175,.38), rgba(15,23,42,.98));
        border-radius: 18px;
        padding: 16px 18px 18px;
        border: 1px solid rgba(30,64,175,.6);
        box-shadow: 0 18px 38px rgba(15,23,42,.9);
        color: #e5e7eb;
    }

    .text-users-title {
        color: #e5e7eb;
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

    .users-search-input {
        background-color: rgba(15,23,42,.95);
        border-color: rgba(148,163,184,.4);
        color: #e5e7eb;
    }

    .users-search-input::placeholder {
        color: rgba(148,163,184,.8);
    }

    .users-table-wrap {
        margin-top: 8px;
    }

    .txp-users-table {
        border-collapse: separate;
        border-spacing: 0 6px;
        font-size: .85rem;
        color: #e5e7eb;
        width: 100%;
    }

    .txp-users-table,
    .txp-users-table thead,
    .txp-users-table tbody,
    .txp-users-table tr,
    .txp-users-table th,
    .txp-users-table td {
        background-color: transparent !important;
        color: #e5e7eb !important;
    }

    .txp-users-table thead th {
        border-bottom: none;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: rgba(156,163,175,.9) !important;
        padding-top: 4px;
        padding-bottom: 6px;
    }

    .txp-users-table tbody tr {
        background: rgba(15,23,42,.96) !important;
    }

    .txp-users-table tbody tr td {
        border-top: none;
        border-bottom: none;
        padding: 9px 10px;
    }

    .txp-users-table tbody tr:hover {
        background: rgba(30,64,175,.9) !important;
    }

    .txp-users-table tbody tr:first-child td:first-child {
        border-top-left-radius: 10px;
    }

    .txp-users-table tbody tr:first-child td:last-child {
        border-top-right-radius: 10px;
    }

    .txp-users-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
    }

    .txp-users-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
    }

    .users-id-link {
        color: rgba(148,163,184,.95);
        text-decoration: none;
    }

    .users-id-link:hover {
        color: #e5e7eb;
    }

    .users-name {
        font-weight: 600;
        color: #f9fafb;
    }

    .users-text {
        color: rgba(209,213,219,.9);
    }

    .users-email {
        color: #bfdbfe !important;
        text-decoration: none;
    }

    .users-email:hover {
        text-decoration: underline;
        color: #e5e7eb !important;
    }

    .users-avatar img {
        border-radius: 999px;
        object-fit: cover;
        border: 2px solid rgba(30,64,175,.7);
        box-shadow: 0 0 10px rgba(37,99,235,.6);
    }

    .users-estado-pill {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
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

    .users-role-text {
        font-size: .8rem;
        color: rgba(209,213,219,.95);
    }

    .users-action-btn {
        border-radius: 999px;
        font-size: .78rem;
        padding-inline: 12px;
        padding-block: 4px;
    }

    .users-empty-row {
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
</style>
@endsection
