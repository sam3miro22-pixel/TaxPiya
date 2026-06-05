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
                        <form id="chatmensajes-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('chatmensajes.store') }}" method="post">
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
                                            <label class="control-label" for="remitente_id">Remitente Id <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-remitente_id-holder" class=" ">
                                                <select required=""  id="ctrl-remitente_id" data-field="remitente_id" name="remitente_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('remitente_id', $value, "");
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
                                            <label class="control-label" for="remitente_rol">Remitente Rol <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-remitente_rol-holder" class=" ">
                                                <select required=""  id="ctrl-remitente_rol" data-field="remitente_rol" name="remitente_rol"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::remitenteRol();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('remitente_rol', $value, "");
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
                                                    $options = Menu::tipo();
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
                                            <label class="control-label" for="mensaje">Mensaje </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-mensaje-holder" class=" ">
                                                <textarea placeholder="Escribir Mensaje" id="ctrl-mensaje" data-field="mensaje"  rows="5" name="mensaje" class=" form-control"><?php echo get_value('mensaje') ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Por favor ingrese el texto</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="media_url">Media Url </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-media_url-holder" class=" ">
                                                <input id="ctrl-media_url" data-field="media_url"  value="<?php echo get_value('media_url', "NULL") ?>" type="text" placeholder="Escribir Media Url"  name="media_url"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="media_tipo">Media Tipo </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-media_tipo-holder" class=" ">
                                                <input id="ctrl-media_tipo" data-field="media_tipo"  value="<?php echo get_value('media_tipo', "NULL") ?>" type="text" placeholder="Escribir Media Tipo"  name="media_tipo"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="reply_to_id">Reply To Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-reply_to_id-holder" class=" ">
                                                <select  id="ctrl-reply_to_id" data-field="reply_to_id" name="reply_to_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->reply_to_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('reply_to_id', $value, "");
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
                                            <label class="control-label" for="leido_por_pasajero_at">Leido Por Pasajero At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-leido_por_pasajero_at-holder" class="input-group ">
                                                <input id="ctrl-leido_por_pasajero_at" data-field="leido_por_pasajero_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('leido_por_pasajero_at', "NULL") ?>" type="datetime" name="leido_por_pasajero_at" placeholder="Escribir Leido Por Pasajero At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="leido_por_conductor_at">Leido Por Conductor At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-leido_por_conductor_at-holder" class="input-group ">
                                                <input id="ctrl-leido_por_conductor_at" data-field="leido_por_conductor_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('leido_por_conductor_at', "NULL") ?>" type="datetime" name="leido_por_conductor_at" placeholder="Escribir Leido Por Conductor At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="moderado">Moderado <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-moderado-holder" class=" ">
                                                <input id="ctrl-moderado" data-field="moderado"  value="<?php echo get_value('moderado', "0") ?>" type="number" placeholder="Escribir Moderado" step="any"  required="" name="moderado"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="moderado_motivo">Moderado Motivo </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-moderado_motivo-holder" class=" ">
                                                <input id="ctrl-moderado_motivo" data-field="moderado_motivo"  value="<?php echo get_value('moderado_motivo', "NULL") ?>" type="text" placeholder="Escribir Moderado Motivo"  name="moderado_motivo"  class="form-control " />
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
