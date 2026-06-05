@inject('comp_model', 'App\Models\ComponentsData')
<?php
$pageTitle = "Editar";
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')
<section class="page txp-users-edit-page" data-page-type="edit" data-page-url="{{ url()->full() }}">
    <?php if($show_header == true){ ?>
    <div class="users-edit-header-wrap py-3 mb-3">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center gap-2">
                        <div class="users-edit-icon">
                            <i class="fa fa-edit"></i>
                        </div>
                        <div>
                            <div class="users-edit-title">Editar usuario</div>
                            <div class="users-edit-subtitle">Actualiza la información del usuario interno</div>
                        </div>
                    </div>
                </div>
                <div class="col-auto d-none d-md-block">
                    <span class="users-edit-pill">Panel de Administración</span>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="mb-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-6 comp-grid">
                    <div class="page-content users-edit-card">
                        <form novalidate
                              id="users-edit-form"
                              role="form"
                              enctype="multipart/form-data"
                              class="form page-form form-horizontal needs-validation"
                              action="<?php print_link("users/edit/$rec_id"); ?>"
                              method="post">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label users-label" for="name">
                                            Nombre de Usuario <span class="text-danger">*</span>
                                        </label>
                                        <div id="ctrl-name-holder">
                                            <input id="ctrl-name"
                                                   data-field="name"
                                                   value="<?php echo $data['name']; ?>"
                                                   type="text"
                                                   placeholder="Escribir Nombre de Usuario"
                                                   required
                                                   name="name"
                                                   data-url="componentsdata/users_name_value_exist/"
                                                   data-loading-msg="Comprobando disponibilidad ..."
                                                   data-available-msg="Disponible"
                                                   data-unavailable-msg="No disponible"
                                                   class="form-control ctrl-check-duplicate users-input" />
                                            <div class="check-status"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label users-label" for="fotoperfil">
                                            Foto Perfil <span class="text-danger">*</span>
                                        </label>
                                        <div id="ctrl-fotoperfil-holder">
                                            <div class="dropzone required users-dropzone"
                                                 input="#ctrl-fotoperfil"
                                                 fieldname="fotoperfil"
                                                 uploadurl="{{ url('fileuploader/upload/fotoperfil') }}"
                                                 data-multiple="false"
                                                 dropmsg="Elija archivos o suelte archivos aquí"
                                                 btntext="Vistazo"
                                                 extensions=".jpg,.png,.gif,.jpeg"
                                                 filesize="80"
                                                 maximum="1">
                                                <input name="fotoperfil"
                                                       id="ctrl-fotoperfil"
                                                       data-field="fotoperfil"
                                                       required
                                                       class="dropzone-input form-control"
                                                       value="<?php echo $data['fotoperfil']; ?>"
                                                       type="text" />
                                                <div class="dz-file-limit text-center text-danger"></div>
                                            </div>
                                        </div>
                                        <?php Html::uploaded_files_list($data['fotoperfil'], '#ctrl-fotoperfil'); ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="control-label users-label" for="estado">
                                            Estado <span class="text-danger">*</span>
                                        </label>
                                        <div id="ctrl-estado-holder">
                                            <select required
                                                    id="ctrl-estado"
                                                    data-field="estado"
                                                    name="estado"
                                                    class="form-select users-select">
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                $options      = Menu::estado2();
                                                $field_value  = $data['estado'];
                                                if(!empty($options)){
                                                    foreach($options as $option){
                                                        $value    = $option['value'];
                                                        $label    = $option['label'];
                                                        $selected = Html::get_record_selected($field_value, $value);
                                                ?>
                                                <option <?php echo $selected ?> value="<?php echo $value ?>">
                                                    <?php echo $label ?>
                                                </option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="control-label users-label" for="telefono">
                                            Teléfono <span class="text-danger">*</span>
                                        </label>
                                        <div id="ctrl-telefono-holder">
                                            <input id="ctrl-telefono"
                                                   data-field="telefono"
                                                   value="<?php echo $data['telefono']; ?>"
                                                   type="text"
                                                   placeholder="Escribir Teléfono"
                                                   required
                                                   name="telefono"
                                                   class="form-control users-input" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label users-label" for="user_role_id">
                                            Rol de Usuario
                                        </label>
                                        <div id="ctrl-user_role_id-holder">
                                            <select id="ctrl-user_role_id"
                                                    data-field="user_role_id"
                                                    name="user_role_id"
                                                    class="form-select users-select"
                                                    placeholder="Seleccione un valor">
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                $options = $comp_model->role_id_option_list() ?? [];
                                                foreach($options as $option){
                                                    $value    = $option->value;
                                                    $label    = $option->label ?? $value;
                                                    $selected = ($value == $data['user_role_id'] ? 'selected' : null);
                                                ?>
                                                <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                    <?php echo $label; ?>
                                                </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-ajax-status mt-2"></div>

                            <div class="form-group text-center mt-4">
                                <button class="btn users-submit-btn" type="submit">
                                    <span>Actualizar usuario</span>
                                    <i class="fa fa-send ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pagecss')
<style>
    .txp-users-edit-page {
        background: radial-gradient(circle at top, #111827 0, #020617 55%, #000 100%);
        min-height: 100vh;
        padding: 0 16px 32px;
    }

    .users-edit-header-wrap {
        background: transparent;
        margin-top: 60px;
    }

    .users-edit-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        background: radial-gradient(circle at 0 0, #fbbf24, #f97316);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #020617;
        box-shadow: 0 0 22px rgba(251,191,36,0.5);
        font-size: 1.1rem;
    }

    .users-edit-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f9fafb;
    }

    .users-edit-subtitle {
        font-size: 0.8rem;
        color: rgba(156,163,175,0.9);
    }

    .users-edit-pill {
        padding: 4px 12px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,0.5);
        font-size: 0.75rem;
        color: #e5e7eb;
        background: rgba(15,23,42,0.8);
    }

    .users-edit-card {
        background: radial-gradient(circle at top left, rgba(30,64,175,0.35), rgba(15,23,42,0.98));
        border-radius: 20px;
        padding: 20px 20px 22px;
        border: 1px solid rgba(30,64,175,0.6);
        box-shadow: 0 18px 40px rgba(15,23,42,0.9);
        color: #e5e7eb;
    }

    .users-label {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(209,213,219,0.9);
        margin-bottom: 4px;
    }

    .users-input,
    .users-select {
        background-color: rgba(15,23,42,0.95);
        border-color: rgba(148,163,184,0.5);
        color: #f9fafb;
        font-size: 0.9rem;
    }

    .users-input:focus,
    .users-select:focus {
        background-color: rgba(15,23,42,1);
        border-color: #f97316;
        box-shadow: 0 0 0 1px rgba(249,115,22,0.8);
        color: #f9fafb;
    }

    .users-input::placeholder {
        color: rgba(148,163,184,0.8);
    }

    .users-select option {
        background-color: #020617;
        color: #e5e7eb;
    }

    .check-status {
        font-size: 0.75rem;
        margin-top: 3px;
        color: rgba(148,163,184,0.9);
    }

    .users-dropzone {
        border-radius: 16px;
        border: 1px dashed rgba(148,163,184,0.7);
        background: rgba(15,23,42,0.9);
        padding: 10px;
        color: #e5e7eb;
    }

    .users-dropzone .dz-message {
        color: rgba(156,163,175,0.9);
        font-size: 0.8rem;
    }

    .users-dropzone .dropzone-input {
        background: transparent;
        border: none;
        color: #e5e7eb;
        font-size: 0.85rem;
        padding: 4px 0 0;
    }

    .users-submit-btn {
        background: linear-gradient(135deg, #fbbf24, #f97316);
        border: none;
        color: #111827;
        font-weight: 600;
        border-radius: 999px;
        padding-inline: 24px;
        padding-block: 7px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 10px 30px rgba(15,23,42,1);
    }

    .users-submit-btn:hover {
        filter: brightness(1.05);
        color: #020617;
    }

    .txp-users-edit-page .form-control:disabled,
    .txp-users-edit-page .form-control[readonly] {
        background-color: rgba(15,23,42,0.8);
        color: rgba(156,163,175,0.9);
    }
</style>
@endsection
