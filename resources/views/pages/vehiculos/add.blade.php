@inject('comp_model', 'App\Models\ComponentsData')
<?php
use Illuminate\Support\Facades\DB;

$pageTitle      = "Agregar nuevo vehículo";
$conductorPref  = request('conductor_id');

$conductoresNombres = DB::table('conductores as c')
    ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
    ->pluck('u.name', 'c.id')   // [conductor_id => user_name]
    ->toArray();
?>

@extends($layout)
@section('title', $pageTitle)

@section('content')

<style>
body{
    color:#e5e7eb!important;
}
#main #main-content{
    width:100%!important;
    max-width:900px!important;
    margin:0 auto!important;
    position:relative!important;
    right:auto!important;
    left:0!important;
    padding:16px 16px 40px!important;
}
.tax-veh-add-page{
    min-height:100vh;
    padding-top:96px;
    display:flex;
    align-items:flex-start;
    justify-content:center;
}
.tax-veh-add-wrap{
    width:100%;
    max-width:640px;
    margin:0 auto;
}
.tax-veh-card{
    background:radial-gradient(circle at top left,rgba(148,163,184,0.14),rgba(15,23,42,1));
    border-radius:18px;
    border:1px solid rgba(31,41,55,0.8);
    box-shadow:0 18px 40px rgba(15,23,42,0.95);
}
.tax-veh-card-header{
    padding:14px 18px 10px;
    border-bottom:1px solid rgba(31,41,55,0.9);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}
.tax-veh-card-header-left{
    display:flex;
    align-items:center;
    gap:10px;
}
.tax-veh-chip{
    width:36px;
    height:36px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(circle at top,rgba(56,189,248,0.35),rgba(15,23,42,1));
    box-shadow:0 0 28px rgba(56,189,248,0.55);
}
.tax-veh-chip i{
    color:#e5e7eb;
    font-size:18px;
}
.tax-veh-title-main{
    font-size:18px;
    font-weight:700;
    letter-spacing:.03em;
}
.tax-veh-title-sub{
    font-size:12px;
    color:#9ca3af;
}
.tax-veh-card-body{
    padding:16px 18px 18px;
}
.tax-veh-form-group{
    margin-bottom:12px;
}
.tax-veh-label{
    font-size:13px;
    font-weight:600;
    margin-bottom:3px;
    color:#ffffff!important;
}
.tax-veh-help{
    font-size:11px;
    color:#9ca3af;
    margin-top:3px;
}
.tax-veh-form-control,
.tax-veh-select{
    background:#020617!important;
    border:1px solid #1f2937!important;
    color:#e5e7eb!important;
    border-radius:999px!important;
    font-size:13px!important;
    padding:.45rem .85rem!important;
}
.tax-veh-form-control:focus,
.tax-veh-select:focus{
    border-color:#22c55e!important;
    box-shadow:0 0 0 1px rgba(34,197,94,0.4)!important;
}
.tax-veh-date-group .input-group-text{
    background:#020617!important;
    border-radius:0 999px 999px 0!important;
    border-color:#1f2937!important;
    color:#e5e7eb!important;
}
.tax-veh-footer{
    padding:10px 18px 16px;
    border-top:1px solid rgba(31,41,55,0.9);
    text-align:center;
}
.tax-veh-submit-btn{
    border-radius:999px;
    padding:.5rem 1.6rem;
    font-weight:600;
    background:linear-gradient(90deg,#22c55e,#4ade80);
    border:none;
}
.tax-veh-submit-btn i{
    margin-left:6px;
}
.tax-veh-back-btn{
    width:38px;
    height:38px;
    border-radius:999px;
    border:1px solid rgba(148,163,184,0.5);
    background:radial-gradient(circle at top left,rgba(34,197,94,0.2),rgba(15,23,42,1));
    display:flex;
    align-items:center;
    justify-content:center;
    color:#e5e7eb;
    margin-right:6px;
}
.tax-veh-back-btn i{
    font-size:18px;
}
.tax-veh-header-row{
    display:flex;
    align-items:center;
}
</style>

<section class="page tax-veh-add-page" data-page-type="add" data-page-url="{{ url()->full() }}">
    <div class="tax-veh-add-wrap">

        <div class="mb-3">
            <div class="tax-veh-header-row">
                <a class="tax-veh-back-btn" href="{{ url()->previous() }}">
                    <i class="fa fa-angle-left"></i>
                </a>
                <div>
                    <div class="tax-veh-title-main">Registrar vehículo</div>
                    <div class="tax-veh-title-sub">
                        Vincula un vehículo al conductor y mantén al día sus documentos obligatorios.
                    </div>
                </div>
            </div>
        </div>

        <div class="card tax-veh-card">
            <div class="tax-veh-card-header">
                <div class="tax-veh-card-header-left">
                    <div class="tax-veh-chip">
                        <i class="fa fa-taxi"></i>
                    </div>
                    <div>
                        <div class="tax-veh-title-main">Datos del vehículo</div>
                        <div class="tax-veh-title-sub">Completa la información principal del taxi y sus pólizas.</div>
                    </div>
                </div>
            </div>

            <form id="vehiculos-add-form"
                  role="form"
                  novalidate
                  enctype="multipart/form-data"
                  class="tax-veh-card-body form page-form form-horizontal needs-validation"
                  action="{{ route('vehiculos.store') }}"
                  method="post">
                @csrf

              <div class="tax-veh-form-group">
    <label class="tax-veh-label" for="ctrl-conductor_id">Conductor asignado</label>
    <div id="ctrl-conductor_id-holder">
        <select id="ctrl-conductor_id"
                data-field="conductor_id"
                name="conductor_id"
                class="form-select tax-veh-select">
            <option value="">
                Selecciona el conductor al que pertenece este vehículo
            </option>
            <?php
                $options = $comp_model->conductor_id_option_list() ?? [];
                foreach($options as $option){
                    $value   = $option->value;                         // ID del conductor
                    $nombre  = $conductoresNombres[$value] ?? 'Sin usuario';
                    $label   = "ID {$value} · {$nombre}";
                    $selected = Html::get_field_selected('conductor_id', $value, $conductorPref);
            ?>
            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                <?php echo $label; ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <div class="tax-veh-help">
        Selecciona el conductor dueño o responsable operativo de este vehículo.
    </div>
</div>



                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-placa">Placa <span class="text-danger">*</span></label>
                    <input id="ctrl-placa"
                           data-field="placa"
                           value="<?php echo get_value('placa'); ?>"
                           type="text"
                           placeholder="Ej: ABC123"
                           required
                           name="placa"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-vin">VIN / Número de chasis</label>
                    <input id="ctrl-vin"
                           data-field="vin"
                           value="<?php echo get_value('vin'); ?>"
                           type="text"
                           placeholder="Código VIN o chasis"
                           name="vin"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-marca">Marca</label>
                    <input id="ctrl-marca"
                           data-field="marca"
                           value="<?php echo get_value('marca'); ?>"
                           type="text"
                           placeholder="Ej: Chevrolet"
                           name="marca"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-linea">Línea / Referencia</label>
                    <input id="ctrl-linea"
                           data-field="linea"
                           value="<?php echo get_value('linea'); ?>"
                           type="text"
                           placeholder="Ej: Spark, Logan"
                           name="linea"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-modelo_anio">Modelo / Año</label>
                    <input id="ctrl-modelo_anio"
                           data-field="modelo_anio"
                           value="<?php echo get_value('modelo_anio'); ?>"
                           type="number"
                           placeholder="2022"
                           step="1"
                           name="modelo_anio"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-color">Color</label>
                    <input id="ctrl-color"
                           data-field="color"
                           value="<?php echo get_value('color'); ?>"
                           type="text"
                           placeholder="Color"
                           name="color"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-categoria">Categoría <span class="text-danger">*</span></label>
                    <select required
                            id="ctrl-categoria"
                            data-field="categoria"
                            name="categoria"
                            class="form-select tax-veh-select">
                        <option value="">Selecciona la categoría</option>
                        <?php
                            $options = Menu::categoria2();
                            if(!empty($options)){
                                foreach($options as $option){
                                    $value = $option['value'];
                                    $label = $option['label'];
                                    $selected = Html::get_field_selected('categoria', $value, "");
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php } } ?>
                    </select>
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-asientos">Número de asientos</label>
                    <input id="ctrl-asientos"
                           data-field="asientos"
                           value="<?php echo get_value('asientos', '4'); ?>"
                           type="number"
                           placeholder="4"
                           step="1"
                           name="asientos"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-soat_numero">Número de SOAT</label>
                    <input id="ctrl-soat_numero"
                           data-field="soat_numero"
                           value="<?php echo get_value('soat_numero'); ?>"
                           type="text"
                           placeholder="Número SOAT"
                           name="soat_numero"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group tax-veh-date-group">
                    <label class="tax-veh-label" for="ctrl-soat_expira">SOAT vence</label>
                    <div class="input-group">
                        <input id="ctrl-soat_expira"
                               data-field="soat_expira"
                               class="form-control datepicker tax-veh-form-control"
                               value="<?php echo get_value('soat_expira'); ?>"
                               type="datetime"
                               name="soat_expira"
                               placeholder="Fecha de vencimiento" />
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="tax-veh-form-group tax-veh-date-group">
                    <label class="tax-veh-label" for="ctrl-tecnomecanica_expira">Tecnomecánica vence</label>
                    <div class="input-group">
                        <input id="ctrl-tecnomecanica_expira"
                               data-field="tecnomecanica_expira"
                               class="form-control datepicker tax-veh-form-control"
                               value="<?php echo get_value('tecnomecanica_expira'); ?>"
                               type="datetime"
                               name="tecnomecanica_expira"
                               placeholder="Fecha de vencimiento" />
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-seguro_extracontractual_numero">Seguro extracontractual</label>
                    <input id="ctrl-seguro_extracontractual_numero"
                           data-field="seguro_extracontractual_numero"
                           value="<?php echo get_value('seguro_extracontractual_numero'); ?>"
                           type="text"
                           placeholder="Número póliza extracontractual"
                           name="seguro_extracontractual_numero"
                           class="form-control tax-veh-form-control" />
                </div>

                <div class="tax-veh-form-group tax-veh-date-group">
                    <label class="tax-veh-label" for="ctrl-seguro_extracontractual_expira">Extracontractual vence</label>
                    <div class="input-group">
                        <input id="ctrl-seguro_extracontractual_expira"
                               data-field="seguro_extracontractual_expira"
                               class="form-control datepicker tax-veh-form-control"
                               value="<?php echo get_value('seguro_extracontractual_expira'); ?>"
                               type="datetime"
                               name="seguro_extracontractual_expira"
                               placeholder="Fecha de vencimiento" />
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-estado_vehiculo">
                        Estado del vehículo <span class="text-danger">*</span>
                    </label>
                    <select required
                            id="ctrl-estado_vehiculo"
                            data-field="estado_vehiculo"
                            name="estado_vehiculo"
                            class="form-select tax-veh-select">
                        <option value="">Selecciona el estado</option>
                        <?php
                            $options = Menu::estadoVehiculo();
                            if(!empty($options)){
                                foreach($options as $option){
                                    $value = $option['value'];
                                    $label = $option['label'];
                                    $selected = Html::get_field_selected('estado_vehiculo', $value, "");
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php } } ?>
                    </select>
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-verificacion_estado">
                        Estado de verificación <span class="text-danger">*</span>
                    </label>
                    <select required
                            id="ctrl-verificacion_estado"
                            data-field="verificacion_estado"
                            name="verificacion_estado"
                            class="form-select tax-veh-select">
                        <option value="">Selecciona el estado</option>
                        <?php
                            $options = Menu::verificacionEstado();
                            if(!empty($options)){
                                foreach($options as $option){
                                    $value = $option['value'];
                                    $label = $option['label'];
                                    $selected = Html::get_field_selected('verificacion_estado', $value, "");
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php } } ?>
                    </select>
                </div>

                <div class="tax-veh-form-group">
                    <label class="tax-veh-label" for="ctrl-verificacion_notas">Notas de verificación</label>
                    <textarea id="ctrl-verificacion_notas"
                              data-field="verificacion_notas"
                              rows="4"
                              name="verificacion_notas"
                              placeholder="Observaciones del vehículo"
                              class="form-control tax-veh-form-control"><?php echo get_value('verificacion_notas'); ?></textarea>
                </div>

                <input type="hidden" id="ctrl-foto_principal" name="foto_principal" value="">

                <div class="form-ajax-status"></div>

                <div class="tax-veh-footer">
                    <button class="btn btn-primary tax-veh-submit-btn" type="submit">
                        Guardar vehículo
                        <i class="fa fa-send"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
