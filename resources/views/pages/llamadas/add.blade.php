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
                        <form id="llamadas-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('llamadas.store') }}" method="post">
                            @csrf
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="viaje_id">Viaje Id <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-viaje_id-holder" class=" ">
                                                <select required=""  id="ctrl-viaje_id" data-field="viaje_id" name="viaje_id"  placeholder="Seleccione un valor"    class="form-select" >
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
                                            <label class="control-label" for="llamador_user_id">Llamador User Id <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-llamador_user_id-holder" class=" ">
                                                <select required=""  id="ctrl-llamador_user_id" data-field="llamador_user_id" name="llamador_user_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('llamador_user_id', $value, "");
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
                                            <label class="control-label" for="llamador_rol">Llamador Rol <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-llamador_rol-holder" class=" ">
                                                <select required=""  id="ctrl-llamador_rol" data-field="llamador_rol" name="llamador_rol"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::remitenteRol();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('llamador_rol', $value, "");
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
                                            <label class="control-label" for="receptor_user_id">Receptor User Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-receptor_user_id-holder" class=" ">
                                                <select  id="ctrl-receptor_user_id" data-field="receptor_user_id" name="receptor_user_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('receptor_user_id', $value, "");
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
                                            <label class="control-label" for="receptor_rol">Receptor Rol </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-receptor_rol-holder" class=" ">
                                                <select  id="ctrl-receptor_rol" data-field="receptor_rol" name="receptor_rol"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::raterRol();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('receptor_rol', $value, "");
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
                                            <label class="control-label" for="tipo">Tipo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-tipo-holder" class=" ">
                                                <select required=""  id="ctrl-tipo" data-field="tipo" name="tipo"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::tipo2();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('tipo', $value, "");
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
                                            <label class="control-label" for="provider">Provider <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-provider-holder" class=" ">
                                                <select required=""  id="ctrl-provider" data-field="provider" name="provider"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::provider2();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('provider', $value, "");
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
                                            <label class="control-label" for="provider_call_id">Provider Call Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-provider_call_id-holder" class=" ">
                                                <input id="ctrl-provider_call_id" data-field="provider_call_id"  value="<?php echo get_value('provider_call_id', "NULL") ?>" type="text" placeholder="Escribir Provider Call Id"  name="provider_call_id"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="provider_room_id">Provider Room Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-provider_room_id-holder" class=" ">
                                                <input id="ctrl-provider_room_id" data-field="provider_room_id"  value="<?php echo get_value('provider_room_id', "NULL") ?>" type="text" placeholder="Escribir Provider Room Id"  name="provider_room_id"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="caller_phone_snapshot">Caller Phone Snapshot </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-caller_phone_snapshot-holder" class=" ">
                                                <input id="ctrl-caller_phone_snapshot" data-field="caller_phone_snapshot"  value="<?php echo get_value('caller_phone_snapshot', "NULL") ?>" type="text" placeholder="Escribir Caller Phone Snapshot"  name="caller_phone_snapshot"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="callee_phone_snapshot">Callee Phone Snapshot </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-callee_phone_snapshot-holder" class=" ">
                                                <input id="ctrl-callee_phone_snapshot" data-field="callee_phone_snapshot"  value="<?php echo get_value('callee_phone_snapshot', "NULL") ?>" type="text" placeholder="Escribir Callee Phone Snapshot"  name="callee_phone_snapshot"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="proxy_number">Proxy Number </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-proxy_number-holder" class=" ">
                                                <input id="ctrl-proxy_number" data-field="proxy_number"  value="<?php echo get_value('proxy_number', "NULL") ?>" type="text" placeholder="Escribir Proxy Number"  name="proxy_number"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="masked">Masked <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-masked-holder" class=" ">
                                                <input id="ctrl-masked" data-field="masked"  value="<?php echo get_value('masked', "0") ?>" type="number" placeholder="Escribir Masked" step="any"  required="" name="masked"  class="form-control " />
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
                                            <label class="control-label" for="ring_start_at">Ring Start At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-ring_start_at-holder" class="input-group ">
                                                <input id="ctrl-ring_start_at" data-field="ring_start_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('ring_start_at', "NULL") ?>" type="datetime" name="ring_start_at" placeholder="Escribir Ring Start At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="connected_at">Connected At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-connected_at-holder" class="input-group ">
                                                <input id="ctrl-connected_at" data-field="connected_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('connected_at', "NULL") ?>" type="datetime" name="connected_at" placeholder="Escribir Connected At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="ended_at">Ended At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-ended_at-holder" class="input-group ">
                                                <input id="ctrl-ended_at" data-field="ended_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('ended_at', "NULL") ?>" type="datetime" name="ended_at" placeholder="Escribir Ended At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="duracion_seg">Duracion Seg </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-duracion_seg-holder" class=" ">
                                                <input id="ctrl-duracion_seg" data-field="duracion_seg"  value="<?php echo get_value('duracion_seg', "NULL") ?>" type="number" placeholder="Escribir Duracion Seg" step="any"  name="duracion_seg"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="dispositivo_id">Dispositivo Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-dispositivo_id-holder" class=" ">
                                                <select  id="ctrl-dispositivo_id" data-field="dispositivo_id" name="dispositivo_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->dispositivo_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('dispositivo_id', $value, "");
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
                                            <label class="control-label" for="ip">Ip </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-ip-holder" class=" ">
                                                <input id="ctrl-ip" data-field="ip"  value="<?php echo get_value('ip', "NULL") ?>" type="text" placeholder="Escribir Ip"  name="ip"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="idempotencia">Idempotencia </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-idempotencia-holder" class=" ">
                                                <input id="ctrl-idempotencia" data-field="idempotencia"  value="<?php echo get_value('idempotencia', "NULL") ?>" type="text" placeholder="Escribir Idempotencia"  name="idempotencia"  class="form-control " />
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
