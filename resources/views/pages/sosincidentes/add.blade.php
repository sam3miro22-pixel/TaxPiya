<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Agregar nuevo"; //set dynamic page title
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page" data-page-type="add" data-page-url="{{ url()->full() }}">
    <?php
        if( $show_header == true ){
    ?>
    <div  class="py-3 mb-2" >
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center g-3">
                <div class="col-auto  back-btn-col" >
                    <a class="back-btn btn btn-secondary" href="{{ url()->previous() }}" >
                        <i class="fa fa-angle-left"></i>                                
                    </a>
                </div>
                <div class="col  " >
                    <div class="">
                        <div class="row">
                            <div class="col-auto">
                                <i class="fa fa-plus"></i>
                            </div>
                            <div class="col">
                                <div class="h5 font-weight-bold text-primary m-0">Agregar nuevo</div>
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
    <div  class="mb-3" >
        <div class="container-fluid">
            <div class="row ">
                <div class="col-md-7 comp-grid " >
                    <div  class="card card-1 border rounded page-content" >
                        <!--[form-start]-->
                        <form id="sosincidentes-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('sosincidentes.store') }}" method="post">
                            @csrf
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="viaje_id">Viaje Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-viaje_id-holder" class=" ">
                                                <select  id="ctrl-viaje_id" data-field="viaje_id" name="viaje_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->viaje_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('viaje_id', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="actor_tipo">Actor Tipo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-actor_tipo-holder" class=" ">
                                                <select required=""  id="ctrl-actor_tipo" data-field="actor_tipo" name="actor_tipo"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::remitenteRol();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('actor_tipo', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="actor_user_id">Actor User Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-actor_user_id-holder" class=" ">
                                                <select  id="ctrl-actor_user_id" data-field="actor_user_id" name="actor_user_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('actor_user_id', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="conductor_id">Conductor Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-conductor_id-holder" class=" ">
                                                <select  id="ctrl-conductor_id" data-field="conductor_id" name="conductor_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->conductor_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('conductor_id', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="categoria">Categoria </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-categoria-holder" class=" ">
                                                <select  id="ctrl-categoria" data-field="categoria" name="categoria"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::categoria();
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="severidad">Severidad <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-severidad-holder" class=" ">
                                                <select required=""  id="ctrl-severidad" data-field="severidad" name="severidad"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::prioridad();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('severidad', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="estado">Estado <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-estado-holder" class=" ">
                                                <select required=""  id="ctrl-estado" data-field="estado" name="estado"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::estado2();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('estado', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="descripcion">Descripcion </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-descripcion-holder" class=" ">
                                                <textarea placeholder="Escribir Descripcion" id="ctrl-descripcion" data-field="descripcion"  rows="5" name="descripcion" class=" form-control"><?php echo get_value('descripcion') ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Por favor ingrese el texto</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="telefono_contacto">Telefono Contacto </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-telefono_contacto-holder" class=" ">
                                                <input id="ctrl-telefono_contacto" data-field="telefono_contacto"  value="<?php echo get_value('telefono_contacto', "NULL") ?>" type="text" placeholder="Escribir Telefono Contacto"  name="telefono_contacto"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="lat">Lat </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-lat-holder" class=" ">
                                                <input id="ctrl-lat" data-field="lat"  value="<?php echo get_value('lat', "NULL") ?>" type="number" placeholder="Escribir Lat" step="0.1"  name="lat"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="lng">Lng </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-lng-holder" class=" ">
                                                <input id="ctrl-lng" data-field="lng"  value="<?php echo get_value('lng', "NULL") ?>" type="number" placeholder="Escribir Lng" step="0.1"  name="lng"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="ubicacion">Ubicacion </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-ubicacion-holder" class=" ">
                                                <input id="ctrl-ubicacion" data-field="ubicacion"  value="<?php echo get_value('ubicacion', "NULL") ?>" type="number" placeholder="Escribir Ubicacion" step="any"  name="ubicacion"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="operador_id">Operador Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-operador_id-holder" class=" ">
                                                <select  id="ctrl-operador_id" data-field="operador_id" name="operador_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('operador_id', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="asignado_at">Asignado At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-asignado_at-holder" class="input-group ">
                                                <input id="ctrl-asignado_at" data-field="asignado_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('asignado_at', "NULL") ?>" type="datetime" name="asignado_at" placeholder="Escribir Asignado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="reconocido_at">Reconocido At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-reconocido_at-holder" class="input-group ">
                                                <input id="ctrl-reconocido_at" data-field="reconocido_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('reconocido_at', "NULL") ?>" type="datetime" name="reconocido_at" placeholder="Escribir Reconocido At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="atendido_at">Atendido At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-atendido_at-holder" class="input-group ">
                                                <input id="ctrl-atendido_at" data-field="atendido_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('atendido_at', "NULL") ?>" type="datetime" name="atendido_at" placeholder="Escribir Atendido At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="resuelto_at">Resuelto At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-resuelto_at-holder" class="input-group ">
                                                <input id="ctrl-resuelto_at" data-field="resuelto_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('resuelto_at', "NULL") ?>" type="datetime" name="resuelto_at" placeholder="Escribir Resuelto At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="cerrado_at">Cerrado At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-cerrado_at-holder" class="input-group ">
                                                <input id="ctrl-cerrado_at" data-field="cerrado_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('cerrado_at', "NULL") ?>" type="datetime" name="cerrado_at" placeholder="Escribir Cerrado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="nivel_escalamiento">Nivel Escalamiento </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-nivel_escalamiento-holder" class=" ">
                                                <input id="ctrl-nivel_escalamiento" data-field="nivel_escalamiento"  value="<?php echo get_value('nivel_escalamiento', "0") ?>" type="number" placeholder="Escribir Nivel Escalamiento" step="any"  name="nivel_escalamiento"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="sla_minutos">Sla Minutos </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-sla_minutos-holder" class=" ">
                                                <input id="ctrl-sla_minutos" data-field="sla_minutos"  value="<?php echo get_value('sla_minutos', "0") ?>" type="number" placeholder="Escribir Sla Minutos" step="any"  name="sla_minutos"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="breach_at">Breach At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-breach_at-holder" class="input-group ">
                                                <input id="ctrl-breach_at" data-field="breach_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('breach_at', "NULL") ?>" type="datetime" name="breach_at" placeholder="Escribir Breach At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="contacto_inicial">Contacto Inicial </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-contacto_inicial-holder" class=" ">
                                                <select  id="ctrl-contacto_inicial" data-field="contacto_inicial" name="contacto_inicial"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::contactoInicial();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('contacto_inicial', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="contacto_resultado">Contacto Resultado </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-contacto_resultado-holder" class=" ">
                                                <select  id="ctrl-contacto_resultado" data-field="contacto_resultado" name="contacto_resultado"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::contactoResultado();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('contacto_resultado', $value, "");
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
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="evidencia_url">Evidencia Url </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-evidencia_url-holder" class=" ">
                                                <input id="ctrl-evidencia_url" data-field="evidencia_url"  value="<?php echo get_value('evidencia_url', "NULL") ?>" type="text" placeholder="Escribir Evidencia Url"  name="evidencia_url"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="notas_operacion">Notas Operacion </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-notas_operacion-holder" class=" ">
                                                <textarea placeholder="Escribir Notas Operacion" id="ctrl-notas_operacion" data-field="notas_operacion"  rows="5" name="notas_operacion" class=" form-control"><?php echo get_value('notas_operacion') ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Por favor ingrese el texto</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-ajax-status"></div>
                            <!--[form-button-start]-->
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <button class="btn btn-primary" type="submit">
                                Entregar
                                <i class="fa fa-send"></i>
                                </button>
                            </div>
                            <!--[form-button-end]-->
                        </form>
                        <!--[form-end]-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
