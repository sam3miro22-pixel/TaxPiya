@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Editar"; //set dynamic page title
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

<section class="page" data-page-type="edit" data-page-url="{{ url()->full() }}">
    <?php
        if( $show_header == true ){
    ?>
    <div class="py-3 mb-2">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto back-btn-col">
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </div>
                <div class="col">
                    <div class="">
                        <div class="row">
                            <div class="col-auto">
                                <i class="fa fa-edit"></i>
                            </div>
                            <div class="col">
                                <div class="h5 font-weight-bold text-primary m-0">Editar tarifa</div>
                                <div class="small text-muted">Actualiza los valores de la tarifa seleccionada.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        }
    ?>

    <div class="mb-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7 comp-grid">
                    <div class="tx-card-dark page-content">

                        <div class="tx-section-title">
                            Configuración de la tarifa
                        </div>

                        <form novalidate id="tarifas-edit-form" role="form" enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("tarifas/edit/$rec_id"); ?>" method="post">
                            @csrf

                            <div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="nombre">Nombre de la tarifa <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-nombre-holder">
                                                <input id="ctrl-nombre" data-field="nombre" value="<?php echo $data['nombre']; ?>" type="text" placeholder="Ej: Tarifa urbana diurna" required name="nombre" class="form-control" />
                                                <div class="text-help">Nombre corto para identificar la tarifa en la configuración y reportes.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="descripcion">Descripción</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-descripcion-holder">
                                                <input id="ctrl-descripcion" data-field="descripcion" value="<?php echo $data['descripcion']; ?>" type="text" placeholder="Describe brevemente la tarifa" name="descripcion" class="form-control" />
                                                <div class="text-help">Puedes indicar condiciones especiales o notas internas.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="scope">Ámbito (scope) <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-scope-holder">
                                                <select required id="ctrl-scope" data-field="scope" name="scope" placeholder="Seleccione un valor" class="form-select">
                                                    <option value="">Seleccione un valor</option>
                                                    <?php
                                                        $options = Menu::scope2();
                                                        $field_value = $data['scope'];
                                                        if(!empty($options)){
                                                            foreach($options as $option){
                                                                $value = $option['value'];
                                                                $label = $option['label'];
                                                                $selected = Html::get_record_selected($field_value, $value);
                                                    ?>
                                                    <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                        <?php echo $label; ?>
                                                    </option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                <div class="text-help">Define si aplica a toda la plataforma, ciudad específica u otro alcance configurado.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="ciudad">Ciudad</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-ciudad-holder">
                                                <input id="ctrl-ciudad" data-field="ciudad" value="<?php echo $data['ciudad']; ?>" type="text" placeholder="Ej: Medellín, Bogotá" name="ciudad" class="form-control" />
                                                <div class="text-help">Si la tarifa es local, indica la ciudad donde aplica.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="categoria">Categoría <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-categoria-holder">
                                                <select required id="ctrl-categoria" data-field="categoria" name="categoria" placeholder="Seleccione un valor" class="form-select">
                                                    <option value="">Seleccione un valor</option>
                                                    <?php
                                                        $options = Menu::categoria2();
                                                        $field_value = $data['categoria'];
                                                        if(!empty($options)){
                                                            foreach($options as $option){
                                                                $value = $option['value'];
                                                                $label = $option['label'];
                                                                $selected = Html::get_record_selected($field_value, $value);
                                                    ?>
                                                    <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                        <?php echo $label; ?>
                                                    </option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                <div class="text-help">Tipo de servicio, por ejemplo: básico, premium, XL, etc.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="horario">Horario <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-horario-holder">
                                                <select required id="ctrl-horario" data-field="horario" name="horario" placeholder="Seleccione un valor" class="form-select">
                                                    <option value="">Seleccione un valor</option>
                                                    <?php
                                                        $options = Menu::horario();
                                                        $field_value = $data['horario'];
                                                        if(!empty($options)){
                                                            foreach($options as $option){
                                                                $value = $option['value'];
                                                                $label = $option['label'];
                                                                $selected = Html::get_record_selected($field_value, $value);
                                                    ?>
                                                    <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                        <?php echo $label; ?>
                                                    </option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                <div class="text-help">Franja horaria en la que aplica la tarifa (diurna, nocturna, etc.).</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="origen_ref">Origen referencia</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-origen_ref-holder">
                                                <input id="ctrl-origen_ref" data-field="origen_ref" value="<?php echo $data['origen_ref']; ?>" type="text" placeholder="Ej: Aeropuerto, Centro, Terminal" name="origen_ref" class="form-control" />
                                                <div class="text-help">Punto de origen de referencia, si aplica (ej: Aeropuerto JMC).</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="destino_ref">Destino referencia</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-destino_ref-holder">
                                                <input id="ctrl-destino_ref" data-field="destino_ref" value="<?php echo $data['destino_ref']; ?>" type="text" placeholder="Ej: Centro, Terminal, Zona Norte" name="destino_ref" class="form-control" />
                                                <div class="text-help">Punto de destino de referencia, si aplica.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-3">

                                <div class="tx-section-title">
                                    Valores económicos
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="moneda">Moneda <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-moneda-holder">
                                                <input id="ctrl-moneda" data-field="moneda" value="<?php echo $data['moneda']; ?>" type="text" placeholder="Ej: COP" required name="moneda" class="form-control" />
                                                <div class="text-help">Código de moneda, normalmente COP.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="monto_fijo">Monto fijo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-monto_fijo-holder">
                                                <input id="ctrl-monto_fijo" data-field="monto_fijo" value="<?php echo $data['monto_fijo']; ?>" type="number" placeholder="Valor base del viaje" step="0.1" required name="monto_fijo" class="form-control" />
                                                <div class="text-help">Valor fijo que se cobra por el viaje (sin incluir recargos ni peajes).</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="recargo_nocturno">Recargo nocturno</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-recargo_nocturno-holder">
                                                <input id="ctrl-recargo_nocturno" data-field="recargo_nocturno" value="<?php echo $data['recargo_nocturno']; ?>" type="number" placeholder="Valor recargo nocturno (opcional)" step="0.1" name="recargo_nocturno" class="form-control" />
                                                <div class="text-help">Monto adicional cuando el viaje ocurre en horario nocturno.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="recargo_festivo">Recargo festivo</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-recargo_festivo-holder">
                                                <input id="ctrl-recargo_festivo" data-field="recargo_festivo" value="<?php echo $data['recargo_festivo']; ?>" type="number" placeholder="Valor recargo festivo (opcional)" step="0.1" name="recargo_festivo" class="form-control" />
                                                <div class="text-help">Monto adicional cuando el viaje ocurre en domingos o festivos.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="recargo_aeropuerto">Recargo aeropuerto</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-recargo_aeropuerto-holder">
                                                <input id="ctrl-recargo_aeropuerto" data-field="recargo_aeropuerto" value="<?php echo $data['recargo_aeropuerto']; ?>" type="number" placeholder="Valor recargo aeropuerto (opcional)" step="0.1" name="recargo_aeropuerto" class="form-control" />
                                                <div class="text-help">Monto adicional cuando el viaje inicia o termina en aeropuerto.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="incluye_peajes">Incluye peajes <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-incluye_peajes-holder">
                                                <select id="ctrl-incluye_peajes" data-field="incluye_peajes" name="incluye_peajes" required class="form-select">
                                                    <?php $field_value = $data['incluye_peajes']; ?>
                                                    <option value="0" <?php echo Html::get_record_selected($field_value, "0"); ?>>No, no incluye peajes</option>
                                                    <option value="1" <?php echo Html::get_record_selected($field_value, "1"); ?>>Sí, incluye peajes</option>
                                                </select>
                                                <div class="text-help">Indica si el valor fijo de la tarifa ya contempla peajes.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="minutos_espera_incluidos">Minutos de espera incluidos</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-minutos_espera_incluidos-holder">
                                                <input id="ctrl-minutos_espera_incluidos" data-field="minutos_espera_incluidos" value="<?php echo $data['minutos_espera_incluidos']; ?>" type="number" placeholder="Ej: 5, 10, 15" step="any" name="minutos_espera_incluidos" class="form-control" />
                                                <div class="text-help">Minutos de espera que están incluidos sin costo adicional.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="valor_minuto_espera">Valor por minuto de espera</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-valor_minuto_espera-holder">
                                                <input id="ctrl-valor_minuto_espera" data-field="valor_minuto_espera" value="<?php echo $data['valor_minuto_espera']; ?>" type="number" placeholder="Valor adicional por minuto extra" step="0.1" name="valor_minuto_espera" class="form-control" />
                                                <div class="text-help">Se aplica cuando se superan los minutos de espera incluidos.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-3">

                                <div class="tx-section-title">
                                    Vigencia y estado
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="vigente_desde">Vigente desde <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-vigente_desde-holder" class="input-group">
                                                <input id="ctrl-vigente_desde" data-field="vigente_desde" class="form-control datepicker datepicker" required value="<?php echo $data['vigente_desde']; ?>" type="datetime" name="vigente_desde" placeholder="Selecciona la fecha de inicio" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="text-help">Fecha desde la cual la tarifa empieza a aplicarse.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="vigente_hasta">Vigente hasta</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-vigente_hasta-holder" class="input-group">
                                                <input id="ctrl-vigente_hasta" data-field="vigente_hasta" class="form-control datepicker datepicker" value="<?php echo $data['vigente_hasta']; ?>" type="datetime" name="vigente_hasta" placeholder="Opcional: fecha de finalización" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="text-help">Si se deja vacío, la tarifa se considera vigente indefinidamente.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="activa">Estado <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-activa-holder">
                                                <select id="ctrl-activa" data-field="activa" name="activa" required class="form-select">
                                                    <?php $field_value = $data['activa']; ?>
                                                    <option value="1" <?php echo Html::get_record_selected($field_value, "1"); ?>>Activa</option>
                                                    <option value="0" <?php echo Html::get_record_selected($field_value, "0"); ?>>Inactiva</option>
                                                </select>
                                                <div class="text-help">Solo las tarifas activas se tendrán en cuenta al calcular viajes.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="prioridad">Prioridad <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-prioridad-holder">
                                                <input id="ctrl-prioridad" data-field="prioridad" value="<?php echo $data['prioridad']; ?>" type="number" placeholder="1 = mayor prioridad" step="any" required name="prioridad" class="form-control" />
                                                <div class="text-help">Se usa cuando varias tarifas coinciden; los números más bajos tienen mayor prioridad.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="version">Versión <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-version-holder">
                                                <input id="ctrl-version" data-field="version" value="<?php echo $data['version']; ?>" type="number" placeholder="Versión de esta tarifa" step="any" required name="version" class="form-control" />
                                                <div class="text-help">Útil para llevar control de cambios en la configuración de tarifas.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-ajax-status"></div>

                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <button class="btn btn-primary" type="submit">
                                    Actualizar
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
