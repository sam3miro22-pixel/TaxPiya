@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Editar";
?>
@extends($layout)
@section('title', $pageTitle)

@section('content')
<style>
#main #main-content {
    width: 100% !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
    position: relative !important;
    right: auto !important;
    left: 0 !important;
    padding: 24px 16px 40px !important;
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
.tax-edit-wrap {
    max-width: 760px;
    margin: 96px auto 0;
}
.tax-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}
.tax-edit-header-left {
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
.tax-edit-title-block h1 {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.02em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.tax-edit-chip {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, rgba(56, 189, 248, 0.3), rgba(37, 99, 235, 0.08));
    box-shadow: 0 0 24px rgba(56, 189, 248, 0.4);
}
.tax-edit-chip i {
    font-size: 16px;
    color: #e5e7eb;
}
.tax-edit-sub {
    font-size: 13px;
    color: var(--tax-text-muted);
    margin-top: 2px;
}
.tax-edit-card {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(15, 23, 42, 1));
    border-radius: 22px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.9);
    padding: 20px 20px 22px;
}
.tax-fieldset-title {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #9ca3af;
    margin-bottom: 8px;
}
.tax-form-group {
    margin-bottom: 14px;
}
.tax-form-label {
    font-size: 13px;
    font-weight: 500;
    color: #e5e7eb;
    margin-bottom: 4px;
}
.tax-form-help {
    font-size: 11px;
    color: var(--tax-text-muted);
    margin-top: 2px;
}
.form-control,
.form-select {
    background-color: #020617;
    border-radius: 999px;
    border: 1px solid rgba(30, 64, 175, 0.7);
    color: #e5e7eb;
    padding-inline: 14px;
}
.form-control:focus,
.form-select:focus {
    background-color: #020617;
    border-color: rgba(56, 189, 248, 0.9);
    box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.3);
    color: #f9fafb;
}
textarea.form-control {
    border-radius: 16px;
    min-height: 110px;
}
.tax-switch-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.tax-switch-label-block {
    flex: 1;
}
.tax-switch-container {
    padding-right: 4px;
}
.tax-switch-container .form-check.form-switch {
    margin: 0;
}
.tax-switch-container .form-check-input {
    cursor: pointer;
}
.tax-switch-container .form-check-input:checked {
    background-color: #22c55e;
    border-color: #22c55e;
}
.tax-switch-container .form-check-input:not(:checked) {
    background-color: #0f172a;
    border-color: #4b5563;
}
.tax-submit-holder {
    margin-top: 18px;
}
.tax-submit-holder .btn {
    border-radius: 999px;
    padding-inline: 26px;
    font-weight: 600;
    letter-spacing: 0.04em;
}
.tax-submit-holder .btn i {
    margin-left: 6px;
}
@media (max-width: 767.98px) {
    .tax-edit-card {
        padding: 16px 14px 18px;
        border-radius: 18px;
    }
}
</style>

<section class="page" data-page-type="edit" data-page-url="{{ url()->full() }}">
    <div class="tax-edit-wrap">
        <?php if($show_header == true){ ?>
        <div class="tax-edit-header">
            <div class="tax-edit-header-left">
                <a class="tax-back-btn" href="{{ url()->previous() }}">
                    <i class="fa fa-angle-left"></i>
                </a>
                <div class="tax-edit-title-block">
                    <h1>
                        <span class="tax-edit-chip">
                            <i class="fa fa-id-card"></i>
                        </span>
                        Editar perfil de conductor
                    </h1>
                    <div class="tax-edit-sub">
                        Actualiza los datos del conductor y su estado operativo en la plataforma.
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="tax-edit-card">
            <form novalidate id="conductores-edit-form" role="form" enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("conductores/edit/$rec_id"); ?>" method="post">
                @csrf

                <div class="tax-fieldset-title">Datos de vinculación</div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-user_id">Usuario Conductor <span class="text-danger">*</span></label>
                    <select required id="ctrl-user_id" data-field="user_id" name="user_id" class="form-select">
                        <option value="">Seleccione un usuario conductor</option>
                        <?php
                            $options = $comp_model->actor_user_id_option_list() ?? [];
                            foreach($options as $option){
                                $value = $option->value;
                                $label = $option->label ?? $value;
                                if(property_exists($option, 'user_role_id') && (int)$option->user_role_id !== 3){
                                    continue;
                                }
                                $selected = ($value == $data['user_id'] ? 'selected' : null);
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                            <?php echo $label; ?>
                        </option>
                        <?php
                            }
                        ?>
                    </select>
                    <div class="tax-form-help">
                        Solo se listan usuarios con rol Conductor (user_role_id = 3).
                    </div>
                </div>

                <div class="tax-fieldset-title mt-3">Disponibilidad y operación</div>

                <div class="tax-form-group">
                    <div class="tax-switch-row">
                        <div class="tax-switch-label-block">
                            <label class="tax-form-label" for="ctrl-estado_operitivo">Estado operativo <span class="text-danger">*</span></label>
                            <div class="tax-form-help">
                                Define si el perfil está activo para recibir y realizar viajes.
                            </div>
                        </div>
                        <div class="tax-switch-container">
                            <input type="hidden" name="estado_operitivo" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ctrl-estado_operitivo" data-field="estado_operitivo" name="estado_operitivo" value="1" {{ $data['estado_operitivo'] == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tax-form-group">
                    <div class="tax-switch-row">
                        <div class="tax-switch-label-block">
                            <label class="tax-form-label" for="ctrl-disponible">Disponible en plataforma <span class="text-danger">*</span></label>
                            <div class="tax-form-help">
                                Indica si el conductor está disponible para recibir solicitudes de viaje.
                            </div>
                        </div>
                        <div class="tax-switch-container">
                            <input type="hidden" name="disponible" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ctrl-disponible" data-field="disponible" name="disponible" value="1" {{ $data['disponible'] == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-comision_plataforma_percent">Comisión plataforma (%)</label>
                    <input id="ctrl-comision_plataforma_percent" data-field="comision_plataforma_percent"
                           value="<?php echo $data['comision_plataforma_percent'] ?? ''; ?>" type="number" step="0.1" min="0" max="100"
                           name="comision_plataforma_percent" class="form-control" placeholder="Ej. 2 o 3 (vacío = global)">
                    <div class="tax-form-help">
                        Porcentaje que retiene TaxPiya por viaje para este conductor. Ej: 2 = 2%, 3 = 3%. Dejar vacío para usar el valor global.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-total_viajes">Total de viajes <span class="text-danger">*</span></label>
                    <input id="ctrl-total_viajes" data-field="total_viajes" value="<?php echo $data['total_viajes']; ?>" type="number" step="any" name="total_viajes" class="form-control" readonly>
                    <div class="tax-form-help">
                        Contador de viajes realizados por el conductor dentro de Taxpiya. Se actualiza automáticamente.
                    </div>
                </div>

                <div class="tax-fieldset-title mt-3">Documentos del vehículo y conductor</div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-licencia_numero">Número de licencia</label>
                    <input id="ctrl-licencia_numero" data-field="licencia_numero" value="<?php echo $data['licencia_numero']; ?>" type="text" placeholder="Ej: MDL-123456" name="licencia_numero" class="form-control">
                    <div class="tax-form-help">
                        Número completo de la licencia de conducción del conductor.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-licencia_categoria">Categoría de licencia</label>
                    <input id="ctrl-licencia_categoria" data-field="licencia_categoria" value="<?php echo $data['licencia_categoria']; ?>" type="text" placeholder="Ej: B1, C1, C2" name="licencia_categoria" class="form-control">
                    <div class="tax-form-help">
                        Categoría asociada a la licencia, según el tipo de vehículo que conduce.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-licencia_expira">Fecha de vencimiento licencia</label>
                    <div class="input-group">
                        <input id="ctrl-licencia_expira" data-field="licencia_expira" class="form-control datepicker" value="<?php echo $data['licencia_expira']; ?>" type="datetime" name="licencia_expira" placeholder="Seleccionar fecha de vencimiento" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <div class="tax-form-help">
                        Asegúrate de que la licencia esté vigente para aprobar al conductor.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-soat_numero">Número de SOAT</label>
                    <input id="ctrl-soat_numero" data-field="soat_numero" value="<?php echo $data['soat_numero']; ?>" type="text" placeholder="Número del SOAT vigente" name="soat_numero" class="form-control">
                    <div class="tax-form-help">
                        Identificador del seguro obligatorio de accidentes de tránsito del vehículo.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-soat_expira">Fecha de vencimiento SOAT</label>
                    <div class="input-group">
                        <input id="ctrl-soat_expira" data-field="soat_expira" class="form-control datepicker" value="<?php echo $data['soat_expira']; ?>" type="datetime" name="soat_expira" placeholder="Seleccionar fecha de vencimiento del SOAT" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <div class="tax-form-help">
                        Fecha límite de vigencia del SOAT del vehículo utilizado para los viajes.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-seguro_numero">Número de seguro adicional</label>
                    <input id="ctrl-seguro_numero" data-field="seguro_numero" value="<?php echo $data['seguro_numero']; ?>" type="text" placeholder="Póliza de seguro adicional (si aplica)" name="seguro_numero" class="form-control">
                    <div class="tax-form-help">
                        Póliza de seguro complementario al SOAT (seguro todo riesgo u otro).
                    </div>
                </div>

                <div class="tax-fieldset-title mt-3">Verificación y seguridad</div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-verificacion_estado">Estado de verificación <span class="text-danger">*</span></label>
                    <select required id="ctrl-verificacion_estado" data-field="verificacion_estado" name="verificacion_estado" class="form-select">
                        <option value="">Seleccione un estado de verificación</option>
                        <?php
                            $options = Menu::verificacionEstado();
                            $field_value = $data['verificacion_estado'];
                            if(!empty($options)){
                                foreach($options as $option){
                                    $value = $option['value'];
                                    $label = $option['label'];
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
                    <div class="tax-form-help">
                        Define el estado general de revisión de documentos y antecedentes del conductor.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-verificacion_notas">Notas de verificación</label>
                    <textarea placeholder="Observaciones internas sobre la validación de documentos o antecedentes" id="ctrl-verificacion_notas" data-field="verificacion_notas" rows="5" name="verificacion_notas" class="form-control"><?php echo $data['verificacion_notas']; ?></textarea>
                    <div class="tax-form-help">
                        Usa este espacio para registrar comentarios sobre revisiones, pendientes o alertas del perfil.
                    </div>
                </div>

                <div class="tax-fieldset-title mt-3">Contacto de emergencia</div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-contacto_emergencia_nombre">Nombre de contacto de emergencia</label>
                    <input id="ctrl-contacto_emergencia_nombre" data-field="contacto_emergencia_nombre" value="<?php echo $data['contacto_emergencia_nombre']; ?>" type="text" placeholder="Nombre completo del contacto de emergencia" name="contacto_emergencia_nombre" class="form-control">
                    <div class="tax-form-help">
                        Persona a la que se debe contactar en caso de incidente con el conductor.
                    </div>
                </div>

                <div class="tax-form-group">
                    <label class="tax-form-label" for="ctrl-contacto_emergencia_telefono">Teléfono de contacto de emergencia</label>
                    <input id="ctrl-contacto_emergencia_telefono" data-field="contacto_emergencia_telefono" value="<?php echo $data['contacto_emergencia_telefono']; ?>" type="text" placeholder="Número de teléfono del contacto de emergencia" name="contacto_emergencia_telefono" class="form-control">
                    <div class="tax-form-help">
                        Número móvil o fijo donde se pueda localizar rápidamente al contacto de emergencia.
                    </div>
                </div>

                <div class="form-ajax-status"></div>

                <div class="form-group text-center mt-3 tax-submit-holder">
                    <button class="btn btn-primary" type="submit">
                        Actualizar conductor
                        <i class="fa fa-send"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
