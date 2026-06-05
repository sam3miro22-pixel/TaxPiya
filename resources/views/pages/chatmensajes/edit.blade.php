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
                        <form novalidate  id="" role="form" enctype="multipart/form-data"  class="form page-form form-horizontal needs-validation" action="<?php print_link("chatmensajes/edit/$rec_id"); ?>" method="post">
                        <!--[form-content-start]-->
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
                                                $selected = ( $value == $data['viaje_id'] ? 'selected' : null );
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
                                                $selected = ( $value == $data['remitente_id'] ? 'selected' : null );
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
                                                $field_value = $data['remitente_rol'];
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
                                        <label class="control-label" for="tipo">Tipo <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-tipo-holder" class=" ">
                                            <select required=""  id="ctrl-tipo" data-field="tipo" name="tipo"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::tipo();
                                                $field_value = $data['tipo'];
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
                                        <label class="control-label" for="mensaje">Mensaje </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-mensaje-holder" class=" ">
                                            <textarea placeholder="Escribir Mensaje" id="ctrl-mensaje" data-field="mensaje"  rows="5" name="mensaje" class=" form-control"><?php  echo $data['mensaje']; ?></textarea>
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
                                            <input id="ctrl-media_url" data-field="media_url"  value="<?php  echo $data['media_url']; ?>" type="text" placeholder="Escribir Media Url"  name="media_url"  class="form-control " />
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
                                            <input id="ctrl-media_tipo" data-field="media_tipo"  value="<?php  echo $data['media_tipo']; ?>" type="text" placeholder="Escribir Media Tipo"  name="media_tipo"  class="form-control " />
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
                                                $selected = ( $value == $data['reply_to_id'] ? 'selected' : null );
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
                                            <input id="ctrl-lat" data-field="lat"  value="<?php  echo $data['lat']; ?>" type="number" placeholder="Escribir Lat" step="0.1"  name="lat"  class="form-control " />
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
                                            <input id="ctrl-lng" data-field="lng"  value="<?php  echo $data['lng']; ?>" type="number" placeholder="Escribir Lng" step="0.1"  name="lng"  class="form-control " />
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
                                            <input id="ctrl-leido_por_pasajero_at" data-field="leido_por_pasajero_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['leido_por_pasajero_at']; ?>" type="datetime" name="leido_por_pasajero_at" placeholder="Escribir Leido Por Pasajero At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
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
                                            <input id="ctrl-leido_por_conductor_at" data-field="leido_por_conductor_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['leido_por_conductor_at']; ?>" type="datetime" name="leido_por_conductor_at" placeholder="Escribir Leido Por Conductor At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
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
                                            <input id="ctrl-moderado" data-field="moderado"  value="<?php  echo $data['moderado']; ?>" type="number" placeholder="Escribir Moderado" step="any"  required="" name="moderado"  class="form-control " />
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
                                            <input id="ctrl-moderado_motivo" data-field="moderado_motivo"  value="<?php  echo $data['moderado_motivo']; ?>" type="text" placeholder="Escribir Moderado Motivo"  name="moderado_motivo"  class="form-control " />
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
                                            <input id="ctrl-ip" data-field="ip"  value="<?php  echo $data['ip']; ?>" type="text" placeholder="Escribir Ip"  name="ip"  class="form-control " />
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
