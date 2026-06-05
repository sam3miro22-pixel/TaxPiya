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
                        <form novalidate  id="" role="form" enctype="multipart/form-data"  class="form page-form form-horizontal needs-validation" action="<?php print_link("notificaciones/edit/$rec_id"); ?>" method="post">
                        <!--[form-content-start]-->
                        @csrf
                        <div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="user_id">User Id <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-user_id-holder" class=" ">
                                            <select required=""  id="ctrl-user_id" data-field="user_id" name="user_id"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->actor_user_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['user_id'] ? 'selected' : null );
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
                                        <label class="control-label" for="canal">Canal <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-canal-holder" class=" ">
                                            <select required=""  id="ctrl-canal" data-field="canal" name="canal"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::canal();
                                                $field_value = $data['canal'];
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
                                        <label class="control-label" for="proveedor">Proveedor </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-proveedor-holder" class=" ">
                                            <select  id="ctrl-proveedor" data-field="proveedor" name="proveedor"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::proveedor();
                                                $field_value = $data['proveedor'];
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
                                        <label class="control-label" for="titulo">Titulo </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-titulo-holder" class=" ">
                                            <input id="ctrl-titulo" data-field="titulo"  value="<?php  echo $data['titulo']; ?>" type="text" placeholder="Escribir Titulo"  name="titulo"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="cuerpo">Cuerpo </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-cuerpo-holder" class=" ">
                                            <input id="ctrl-cuerpo" data-field="cuerpo"  value="<?php  echo $data['cuerpo']; ?>" type="text" placeholder="Escribir Cuerpo"  name="cuerpo"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="data_json">Data Json </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-data_json-holder" class=" ">
                                            <textarea placeholder="Escribir Data Json" id="ctrl-data_json" data-field="data_json"  rows="5" name="data_json" class=" form-control"><?php  echo $data['data_json']; ?></textarea>
                                            <!--<div class="invalid-feedback animated bounceIn text-center">Por favor ingrese el texto</div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="device_token_snapshot">Device Token Snapshot </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-device_token_snapshot-holder" class=" ">
                                            <input id="ctrl-device_token_snapshot" data-field="device_token_snapshot"  value="<?php  echo $data['device_token_snapshot']; ?>" type="text" placeholder="Escribir Device Token Snapshot"  name="device_token_snapshot"  class="form-control " />
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
                                        <label class="control-label" for="programada_at">Programada At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-programada_at-holder" class="input-group ">
                                            <input id="ctrl-programada_at" data-field="programada_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['programada_at']; ?>" type="datetime" name="programada_at" placeholder="Escribir Programada At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="enviada_at">Enviada At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-enviada_at-holder" class="input-group ">
                                            <input id="ctrl-enviada_at" data-field="enviada_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['enviada_at']; ?>" type="datetime" name="enviada_at" placeholder="Escribir Enviada At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="entregada_at">Entregada At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-entregada_at-holder" class="input-group ">
                                            <input id="ctrl-entregada_at" data-field="entregada_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['entregada_at']; ?>" type="datetime" name="entregada_at" placeholder="Escribir Entregada At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="abierta_at">Abierta At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-abierta_at-holder" class="input-group ">
                                            <input id="ctrl-abierta_at" data-field="abierta_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['abierta_at']; ?>" type="datetime" name="abierta_at" placeholder="Escribir Abierta At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="fallida_at">Fallida At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-fallida_at-holder" class="input-group ">
                                            <input id="ctrl-fallida_at" data-field="fallida_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['fallida_at']; ?>" type="datetime" name="fallida_at" placeholder="Escribir Fallida At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="provider_message_id">Provider Message Id </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-provider_message_id-holder" class=" ">
                                            <input id="ctrl-provider_message_id" data-field="provider_message_id"  value="<?php  echo $data['provider_message_id']; ?>" type="text" placeholder="Escribir Provider Message Id"  name="provider_message_id"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="error_code">Error Code </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-error_code-holder" class=" ">
                                            <input id="ctrl-error_code" data-field="error_code"  value="<?php  echo $data['error_code']; ?>" type="text" placeholder="Escribir Error Code"  name="error_code"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="error_message">Error Message </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-error_message-holder" class=" ">
                                            <input id="ctrl-error_message" data-field="error_message"  value="<?php  echo $data['error_message']; ?>" type="text" placeholder="Escribir Error Message"  name="error_message"  class="form-control " />
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
                                            <input id="ctrl-idempotencia" data-field="idempotencia"  value="<?php  echo $data['idempotencia']; ?>" type="text" placeholder="Escribir Idempotencia"  name="idempotencia"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="prioridad">Prioridad <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-prioridad-holder" class=" ">
                                            <select required=""  id="ctrl-prioridad" data-field="prioridad" name="prioridad"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::prioridad();
                                                $field_value = $data['prioridad'];
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
                                        <label class="control-label" for="origen_evento">Origen Evento </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-origen_evento-holder" class=" ">
                                            <input id="ctrl-origen_evento" data-field="origen_evento"  value="<?php  echo $data['origen_evento']; ?>" type="text" placeholder="Escribir Origen Evento"  name="origen_evento"  class="form-control " />
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
