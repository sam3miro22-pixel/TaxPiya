<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    $pageTitle = "Editar"; //set dynamic page title
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page" data-page-type="edit" data-page-url="{{ url()->full() }}">
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
                                <i class="fa fa-edit"></i>
                            </div>
                            <div class="col">
                                <div class="h5 font-weight-bold text-primary m-0">Editar</div>
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
                        <form novalidate  id="" role="form" enctype="multipart/form-data"  class="form page-form form-horizontal needs-validation" action="<?php print_link("viajes/edit/$rec_id"); ?>" method="post">
                        <!--[form-content-start]-->
                        @csrf
                        <div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="pasajero_id">Pasajero Id <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-pasajero_id-holder" class=" ">
                                            <select required=""  id="ctrl-pasajero_id" data-field="pasajero_id" name="pasajero_id"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->actor_user_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['pasajero_id'] ? 'selected' : null );
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
                                                $selected = ( $value == $data['conductor_id'] ? 'selected' : null );
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
                                        <label class="control-label" for="vehiculo_id">Vehiculo Id </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-vehiculo_id-holder" class=" ">
                                            <select  id="ctrl-vehiculo_id" data-field="vehiculo_id" name="vehiculo_id"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->vehiculo_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['vehiculo_id'] ? 'selected' : null );
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
                                        <label class="control-label" for="origen_lat">Origen Lat <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_lat-holder" class=" ">
                                            <input id="ctrl-origen_lat" data-field="origen_lat"  value="<?php  echo $data['origen_lat']; ?>" type="number" placeholder="Escribir Origen Lat" step="0.1"  required="" name="origen_lat"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="origen_lng">Origen Lng <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_lng-holder" class=" ">
                                            <input id="ctrl-origen_lng" data-field="origen_lng"  value="<?php  echo $data['origen_lng']; ?>" type="number" placeholder="Escribir Origen Lng" step="0.1"  required="" name="origen_lng"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="origen_ubicacion">Origen Ubicacion <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_ubicacion-holder" class=" ">
                                            <input id="ctrl-origen_ubicacion" data-field="origen_ubicacion"  value="<?php  echo $data['origen_ubicacion']; ?>" type="number" placeholder="Escribir Origen Ubicacion" step="any"  required="" name="origen_ubicacion"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="origen_texto">Origen Texto </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_texto-holder" class=" ">
                                            <input id="ctrl-origen_texto" data-field="origen_texto"  value="<?php  echo $data['origen_texto']; ?>" type="text" placeholder="Escribir Origen Texto"  name="origen_texto"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="destino_lat">Destino Lat </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-destino_lat-holder" class=" ">
                                            <input id="ctrl-destino_lat" data-field="destino_lat"  value="<?php  echo $data['destino_lat']; ?>" type="number" placeholder="Escribir Destino Lat" step="0.1"  name="destino_lat"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="destino_lng">Destino Lng </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-destino_lng-holder" class=" ">
                                            <input id="ctrl-destino_lng" data-field="destino_lng"  value="<?php  echo $data['destino_lng']; ?>" type="number" placeholder="Escribir Destino Lng" step="0.1"  name="destino_lng"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="destino_ubicacion">Destino Ubicacion </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-destino_ubicacion-holder" class=" ">
                                            <input id="ctrl-destino_ubicacion" data-field="destino_ubicacion"  value="<?php  echo $data['destino_ubicacion']; ?>" type="number" placeholder="Escribir Destino Ubicacion" step="any"  name="destino_ubicacion"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="destino_texto">Destino Texto </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-destino_texto-holder" class=" ">
                                            <input id="ctrl-destino_texto" data-field="destino_texto"  value="<?php  echo $data['destino_texto']; ?>" type="text" placeholder="Escribir Destino Texto"  name="destino_texto"  class="form-control " />
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
                                                $options = Menu::fromEstado();
                                                $field_value = $data['estado'];
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
                                            <input id="ctrl-asignado_at" data-field="asignado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['asignado_at']; ?>" type="datetime" name="asignado_at" placeholder="Escribir Asignado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="aceptar_hasta">Aceptar Hasta </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-aceptar_hasta-holder" class="input-group ">
                                            <input id="ctrl-aceptar_hasta" data-field="aceptar_hasta" class="form-control datepicker  datepicker"  value="<?php  echo $data['aceptar_hasta']; ?>" type="datetime" name="aceptar_hasta" placeholder="Escribir Aceptar Hasta" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="aceptado_at">Aceptado At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-aceptado_at-holder" class="input-group ">
                                            <input id="ctrl-aceptado_at" data-field="aceptado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['aceptado_at']; ?>" type="datetime" name="aceptado_at" placeholder="Escribir Aceptado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="en_camino_at">En Camino At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-en_camino_at-holder" class="input-group ">
                                            <input id="ctrl-en_camino_at" data-field="en_camino_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['en_camino_at']; ?>" type="datetime" name="en_camino_at" placeholder="Escribir En Camino At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="llego_at">Llego At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-llego_at-holder" class="input-group ">
                                            <input id="ctrl-llego_at" data-field="llego_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['llego_at']; ?>" type="datetime" name="llego_at" placeholder="Escribir Llego At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="iniciado_at">Iniciado At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-iniciado_at-holder" class="input-group ">
                                            <input id="ctrl-iniciado_at" data-field="iniciado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['iniciado_at']; ?>" type="datetime" name="iniciado_at" placeholder="Escribir Iniciado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="terminado_at">Terminado At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-terminado_at-holder" class="input-group ">
                                            <input id="ctrl-terminado_at" data-field="terminado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['terminado_at']; ?>" type="datetime" name="terminado_at" placeholder="Escribir Terminado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="cancelado_at">Cancelado At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-cancelado_at-holder" class="input-group ">
                                            <input id="ctrl-cancelado_at" data-field="cancelado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['cancelado_at']; ?>" type="datetime" name="cancelado_at" placeholder="Escribir Cancelado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="cancelado_por">Cancelado Por </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-cancelado_por-holder" class=" ">
                                            <select  id="ctrl-cancelado_por" data-field="cancelado_por" name="cancelado_por"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::canceladoPor();
                                                $field_value = $data['cancelado_por'];
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="cancelacion_motivo">Cancelacion Motivo </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-cancelacion_motivo-holder" class=" ">
                                            <input id="ctrl-cancelacion_motivo" data-field="cancelacion_motivo"  value="<?php  echo $data['cancelacion_motivo']; ?>" type="text" placeholder="Escribir Cancelacion Motivo"  name="cancelacion_motivo"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="metodo_asignacion">Metodo Asignacion <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-metodo_asignacion-holder" class=" ">
                                            <select required=""  id="ctrl-metodo_asignacion" data-field="metodo_asignacion" name="metodo_asignacion"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::metodo();
                                                $field_value = $data['metodo_asignacion'];
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="radio_busqueda_m">Radio Busqueda M </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-radio_busqueda_m-holder" class=" ">
                                            <input id="ctrl-radio_busqueda_m" data-field="radio_busqueda_m"  value="<?php  echo $data['radio_busqueda_m']; ?>" type="number" placeholder="Escribir Radio Busqueda M" step="any"  name="radio_busqueda_m"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="eta_min_estimada">Eta Min Estimada </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-eta_min_estimada-holder" class=" ">
                                            <input id="ctrl-eta_min_estimada" data-field="eta_min_estimada"  value="<?php  echo $data['eta_min_estimada']; ?>" type="number" placeholder="Escribir Eta Min Estimada" step="0.1"  name="eta_min_estimada"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="distancia_km_estimada">Distancia Km Estimada </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-distancia_km_estimada-holder" class=" ">
                                            <input id="ctrl-distancia_km_estimada" data-field="distancia_km_estimada"  value="<?php  echo $data['distancia_km_estimada']; ?>" type="number" placeholder="Escribir Distancia Km Estimada" step="0.1"  name="distancia_km_estimada"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="duracion_min_estimada">Duracion Min Estimada </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-duracion_min_estimada-holder" class=" ">
                                            <input id="ctrl-duracion_min_estimada" data-field="duracion_min_estimada"  value="<?php  echo $data['duracion_min_estimada']; ?>" type="number" placeholder="Escribir Duracion Min Estimada" step="0.1"  name="duracion_min_estimada"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="distancia_km_real">Distancia Km Real </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-distancia_km_real-holder" class=" ">
                                            <input id="ctrl-distancia_km_real" data-field="distancia_km_real"  value="<?php  echo $data['distancia_km_real']; ?>" type="number" placeholder="Escribir Distancia Km Real" step="0.1"  name="distancia_km_real"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="duracion_min_real">Duracion Min Real </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-duracion_min_real-holder" class=" ">
                                            <input id="ctrl-duracion_min_real" data-field="duracion_min_real"  value="<?php  echo $data['duracion_min_real']; ?>" type="number" placeholder="Escribir Duracion Min Real" step="0.1"  name="duracion_min_real"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="tarifa_id">Tarifa Id </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-tarifa_id-holder" class=" ">
                                            <select  id="ctrl-tarifa_id" data-field="tarifa_id" name="tarifa_id"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->tarifa_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['tarifa_id'] ? 'selected' : null );
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
                                        <label class="control-label" for="moneda">Moneda <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-moneda-holder" class=" ">
                                            <input id="ctrl-moneda" data-field="moneda"  value="<?php  echo $data['moneda']; ?>" type="text" placeholder="Escribir Moneda"  required="" name="moneda"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="tarifa_aplicada">Tarifa Aplicada </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-tarifa_aplicada-holder" class=" ">
                                            <input id="ctrl-tarifa_aplicada" data-field="tarifa_aplicada"  value="<?php  echo $data['tarifa_aplicada']; ?>" type="number" placeholder="Escribir Tarifa Aplicada" step="0.1"  name="tarifa_aplicada"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="valor_pagado">Valor Pagado </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-valor_pagado-holder" class=" ">
                                            <input id="ctrl-valor_pagado" data-field="valor_pagado"  value="<?php  echo $data['valor_pagado']; ?>" type="number" placeholder="Escribir Valor Pagado" step="0.1"  name="valor_pagado"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="pago_registrado">Pago Registrado <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-pago_registrado-holder" class=" ">
                                            <input id="ctrl-pago_registrado" data-field="pago_registrado"  value="<?php  echo $data['pago_registrado']; ?>" type="number" placeholder="Escribir Pago Registrado" step="any"  required="" name="pago_registrado"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-ajax-status"></div>
                        <!--[form-content-end]-->
                        <!--[form-button-start]-->
                        <div class="form-group text-center">
                            <button class="btn btn-primary" type="submit">
                            Actualizar
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
