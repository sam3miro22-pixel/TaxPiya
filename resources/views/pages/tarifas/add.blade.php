@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Agregar nueva tarifa";
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')

<style>
    body{
        color:#e5e7eb !important;
    }
    .tx-card-dark{
        background:#020617;
        border-radius:18px;
        border:1px solid #1e293b;
        box-shadow:0 10px 30px rgba(15,23,42,0.8);
        padding:24px 22px;
    }
    .tx-section-title{
        font-size:15px;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.04em;
        color:#38bdf8;
        margin-bottom:14px;
    }
    .form-group{
        margin-bottom:12px;
    }
    .control-label{
        font-weight:600;
        color:#9ca3af;
    }
    .form-control,
    .form-select{
        background:#020617;
        border-color:#374151;
        color:#e5e7eb;
        border-radius:10px;
    }
    .form-control:focus,
    .form-select:focus{
        background:#020617;
        border-color:#38bdf8;
        color:#e5e7eb;
        box-shadow:0 0 0 1px rgba(56,189,248,0.35);
    }
    .text-help{
        font-size:11px;
        color:#6b7280;
        margin-top:3px;
    }
    .input-group-text{
        background:#020617;
        border-color:#374151;
        color:#9ca3af;
        border-radius:0 10px 10px 0;
    }
    .form-submit-btn-holder .btn{
        border-radius:999px;
        padding:10px 26px;
        font-weight:600;
    }
	.comp-grid{
        margin:0 auto;
        float:none;
        max-width:920px;
    }
</style>

<section class="page" data-page-type="add" data-page-url="{{ url()->full() }}">
    <?php if($show_header == true){ ?>
    <div class="py-3 mb-2">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fa fa-plus text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-primary m-0">
                                Agregar nueva tarifa
                            </div>
                            <div class="small text-muted">
                                Define las reglas que usará Taxpiya para calcular el valor del viaje.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="mb-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7 comp-grid">
                    <div class="card card-1 border rounded page-content tx-card-dark">
                        <form id="tarifas-add-form"
                              role="form"
                              novalidate
                              enctype="multipart/form-data"
                              class="form page-form form-horizontal needs-validation"
                              action="{{ route('tarifas.store') }}"
                              method="post">
                            @csrf

                            <div class="tx-section-title">
                                Datos generales de la tarifa
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="nombre">
                                            Nombre <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Ej: Tarifa urbana día Pitalito.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-nombre-holder">
                                            <input id="ctrl-nombre"
                                                   data-field="nombre"
                                                   value="<?php echo get_value('nombre') ?>"
                                                   type="text"
                                                   placeholder="Ingresa el nombre de la tarifa"
                                                   required
                                                   name="nombre"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="descripcion">
                                            Descripción
                                        </label>
                                        <div class="text-help">
                                            Breve descripción interna (no visible para el pasajero).
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-descripcion-holder">
                                            <input id="ctrl-descripcion"
                                                   data-field="descripcion"
                                                   value="<?php echo get_value('descripcion') ?>"
                                                   type="text"
                                                   placeholder="Ej: Aplica solo para zona urbana de Pitalito"
                                                   name="descripcion"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tx-section-title">
                                Alcance y zona
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="scope">
                                            Ámbito (scope) <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Define si aplica a ciudad completa, aeropuerto, zona específica, etc.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-scope-holder">
                                            <select required
                                                    id="ctrl-scope"
                                                    data-field="scope"
                                                    name="scope"
                                                    class="form-select">
                                                <option value="">Seleccione el ámbito de la tarifa</option>
                                                <?php
                                                    $options = Menu::scope2();
                                                    if(!empty($options)){
                                                        foreach($options as $option){
                                                            $value = $option['value'];
                                                            $label = $option['label'];
                                                            $selected = Html::get_field_selected('scope', $value, "");
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
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="ciudad">
                                            Ciudad
                                        </label>
                                        <div class="text-help">
                                            Ciudad donde aplica la tarifa. Ej: Pitalito.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-ciudad-holder">
                                            <input id="ctrl-ciudad"
                                                   data-field="ciudad"
                                                   value="<?php echo get_value('ciudad') ?>"
                                                   type="text"
                                                   placeholder="Nombre de la ciudad"
                                                   name="ciudad"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="categoria">
                                            Categoría de vehículo <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Selecciona el tipo de vehículo que usará esta tarifa.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-categoria-holder">
                                            <select required
                                                    id="ctrl-categoria"
                                                    data-field="categoria"
                                                    name="categoria"
                                                    class="form-select">
                                                <option value="">Seleccione la categoría</option>
                                                <?php
                                                    $options = Menu::categoria2();
                                                    if(!empty($options)){
                                                        foreach($options as $option){
                                                            $value = $option['value'];
                                                            $label = $option['label'];
                                                            $selected = Html::get_field_selected('categoria', $value, "");
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
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="horario">
                                            Horario <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Define si la tarifa es diurna, nocturna, fin de semana, etc.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-horario-holder">
                                            <select required
                                                    id="ctrl-horario"
                                                    data-field="horario"
                                                    name="horario"
                                                    class="form-select">
                                                <option value="">Seleccione el horario</option>
                                                <?php
                                                    $options = Menu::horario();
                                                    if(!empty($options)){
                                                        foreach($options as $option){
                                                            $value = $option['value'];
                                                            $label = $option['label'];
                                                            $selected = Html::get_field_selected('horario', $value, "");
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
                            </div>

                            <div class="tx-section-title">
                                Referencias de ruta (opcional)
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="origen_ref">
                                            Origen referencial
                                        </label>
                                        <div class="text-help">
                                            Ej: Aeropuerto JMC, Centro comercial, punto fijo, etc.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_ref-holder">
                                            <input id="ctrl-origen_ref"
                                                   data-field="origen_ref"
                                                   value="<?php echo get_value('origen_ref') ?>"
                                                   type="text"
                                                   placeholder="Texto de referencia para el origen"
                                                   name="origen_ref"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="destino_ref">
                                            Destino referencial
                                        </label>
                                        <div class="text-help">
                                            Ej: Centro, barrio, zona hotelera, etc.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-destino_ref-holder">
                                            <input id="ctrl-destino_ref"
                                                   data-field="destino_ref"
                                                   value="<?php echo get_value('destino_ref') ?>"
                                                   type="text"
                                                   placeholder="Texto de referencia para el destino"
                                                   name="destino_ref"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tx-section-title">
                                Valores y recargos
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="moneda">
                                            Moneda <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Ej: COP, MXN, CLP.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-moneda-holder">
                                            <input id="ctrl-moneda"
                                                   data-field="moneda"
                                                   value="<?php echo get_value('moneda', 'COP') ?>"
                                                   type="text"
                                                   placeholder="Código de moneda (Ej: COP)"
                                                   required
                                                   name="moneda"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="monto_fijo">
                                            Monto fijo <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Valor base del viaje para esta tarifa.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-monto_fijo-holder">
                                            <input id="ctrl-monto_fijo"
                                                   data-field="monto_fijo"
                                                   value="<?php echo get_value('monto_fijo') ?>"
                                                   type="number"
                                                   step="0.1"
                                                   placeholder="Ingresa el valor base de la tarifa"
                                                   required
                                                   name="monto_fijo"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="recargo_nocturno">
                                            Recargo nocturno
                                        </label>
                                        <div class="text-help">
                                            Valor adicional para horario nocturno (si aplica).
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-recargo_nocturno-holder">
                                            <input id="ctrl-recargo_nocturno"
                                                   data-field="recargo_nocturno"
                                                   value="<?php echo get_value('recargo_nocturno') ?>"
                                                   type="number"
                                                   step="0.1"
                                                   placeholder="Valor del recargo nocturno (opcional)"
                                                   name="recargo_nocturno"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="recargo_festivo">
                                            Recargo festivo
                                        </label>
                                        <div class="text-help">
                                            Valor adicional para domingos y festivos.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-recargo_festivo-holder">
                                            <input id="ctrl-recargo_festivo"
                                                   data-field="recargo_festivo"
                                                   value="<?php echo get_value('recargo_festivo') ?>"
                                                   type="number"
                                                   step="0.1"
                                                   placeholder="Valor del recargo festivo (opcional)"
                                                   name="recargo_festivo"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="recargo_aeropuerto">
                                            Recargo aeropuerto
                                        </label>
                                        <div class="text-help">
                                            Valor adicional cuando el viaje incluye aeropuerto.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-recargo_aeropuerto-holder">
                                            <input id="ctrl-recargo_aeropuerto"
                                                   data-field="recargo_aeropuerto"
                                                   value="<?php echo get_value('recargo_aeropuerto') ?>"
                                                   type="number"
                                                   step="0.1"
                                                   placeholder="Valor del recargo de aeropuerto (opcional)"
                                                   name="recargo_aeropuerto"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tx-section-title">
                                Tiempos de espera
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="incluye_peajes">
                                            ¿Incluye peajes? <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Usa 1 para Sí, 0 para No.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-incluye_peajes-holder">
                                            <input id="ctrl-incluye_peajes"
                                                   data-field="incluye_peajes"
                                                   value="<?php echo get_value('incluye_peajes', '0') ?>"
                                                   type="number"
                                                   step="1"
                                                   placeholder="1 = Sí, 0 = No"
                                                   required
                                                   name="incluye_peajes"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="minutos_espera_incluidos">
                                            Minutos de espera incluidos
                                        </label>
                                        <div class="text-help">
                                            Minutos de espera sin recargo (0 si no aplica).
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-minutos_espera_incluidos-holder">
                                            <input id="ctrl-minutos_espera_incluidos"
                                                   data-field="minutos_espera_incluidos"
                                                   value="<?php echo get_value('minutos_espera_incluidos', '0') ?>"
                                                   type="number"
                                                   step="1"
                                                   placeholder="Minutos incluidos sin costo (ej: 5)"
                                                   name="minutos_espera_incluidos"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="valor_minuto_espera">
                                            Valor por minuto extra
                                        </label>
                                        <div class="text-help">
                                            Valor que se cobrará por cada minuto adicional de espera.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-valor_minuto_espera-holder">
                                            <input id="ctrl-valor_minuto_espera"
                                                   data-field="valor_minuto_espera"
                                                   value="<?php echo get_value('valor_minuto_espera') ?>"
                                                   type="number"
                                                   step="0.1"
                                                   placeholder="Valor por minuto de espera (opcional)"
                                                   name="valor_minuto_espera"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tx-section-title">
                                Vigencia y estado
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="vigente_desde">
                                            Vigente desde <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Fecha a partir de la cual se aplica la tarifa.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-vigente_desde-holder" class="input-group">
                                            <input id="ctrl-vigente_desde"
                                                   data-field="vigente_desde"
                                                   class="form-control datepicker datepicker"
                                                   required
                                                   value="<?php echo get_value('vigente_desde') ?>"
                                                   type="datetime"
                                                   name="vigente_desde"
                                                   placeholder="Selecciona la fecha de inicio"
                                                   data-enable-time="false"
                                                   data-min-date=""
                                                   data-max-date=""
                                                   data-date-format="Y-m-d"
                                                   data-alt-format="F j, Y"
                                                   data-inline="false"
                                                   data-no-calendar="false"
                                                   data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="vigente_hasta">
                                            Vigente hasta
                                        </label>
                                        <div class="text-help">
                                            Deja vacío si la tarifa no tiene fecha de fin.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-vigente_hasta-holder" class="input-group">
                                            <input id="ctrl-vigente_hasta"
                                                   data-field="vigente_hasta"
                                                   class="form-control datepicker datepicker"
                                                   value="<?php echo get_value('vigente_hasta') ?>"
                                                   type="datetime"
                                                   name="vigente_hasta"
                                                   placeholder="Selecciona la fecha de fin (opcional)"
                                                   data-enable-time="false"
                                                   data-min-date=""
                                                   data-max-date=""
                                                   data-date-format="Y-m-d"
                                                   data-alt-format="F j, Y"
                                                   data-inline="false"
                                                   data-no-calendar="false"
                                                   data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="activa">
                                            Estado <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Usa 1 para Activa, 0 para Inactiva.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-activa-holder">
                                            <input id="ctrl-activa"
                                                   data-field="activa"
                                                   value="<?php echo get_value('activa', '1') ?>"
                                                   type="number"
                                                   step="1"
                                                   placeholder="1 = Activa, 0 = Inactiva"
                                                   required
                                                   name="activa"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tx-section-title">
                                Opciones avanzadas
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="prioridad">
                                            Prioridad <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Valor usado para resolver conflictos entre múltiples tarifas. Por defecto 1.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-prioridad-holder">
                                            <input id="ctrl-prioridad"
                                                   data-field="prioridad"
                                                   value="<?php echo get_value('prioridad', '1') ?>"
                                                   type="number"
                                                   step="1"
                                                   placeholder="Prioridad de la tarifa (por defecto 1)"
                                                   required
                                                   name="prioridad"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="version">
                                            Versión <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-help">
                                            Control interno de versión. Normalmente 1.
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-version-holder">
                                            <input id="ctrl-version"
                                                   data-field="version"
                                                   value="<?php echo get_value('version', '1') ?>"
                                                   type="number"
                                                   step="1"
                                                   placeholder="Versión de la tarifa (por defecto 1)"
                                                   required
                                                   name="version"
                                                   class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-ajax-status"></div>

                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <button class="btn btn-primary" type="submit">
                                    Guardar tarifa
                                    <i class="fa fa-send"></i>
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
