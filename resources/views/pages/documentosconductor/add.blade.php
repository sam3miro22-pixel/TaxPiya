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
                        <form id="documentosconductor-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('documentosconductor.store') }}" method="post">
                            @csrf
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="conductor_id">Conductor Id <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-conductor_id-holder" class=" ">
                                                <select required=""  id="ctrl-conductor_id" data-field="conductor_id" name="conductor_id"  placeholder="Seleccione un valor"    class="form-select" >
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
                                            <label class="control-label" for="numero">Numero </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-numero-holder" class=" ">
                                                <input id="ctrl-numero" data-field="numero"  value="<?php echo get_value('numero', "NULL") ?>" type="text" placeholder="Escribir Numero"  name="numero"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="emisor">Emisor </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-emisor-holder" class=" ">
                                                <input id="ctrl-emisor" data-field="emisor"  value="<?php echo get_value('emisor', "NULL") ?>" type="text" placeholder="Escribir Emisor"  name="emisor"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="expedido_at">Expedido At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-expedido_at-holder" class="input-group ">
                                                <input id="ctrl-expedido_at" data-field="expedido_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('expedido_at', "NULL") ?>" type="datetime" name="expedido_at" placeholder="Escribir Expedido At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="expira_at">Expira At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-expira_at-holder" class="input-group ">
                                                <input id="ctrl-expira_at" data-field="expira_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('expira_at', "NULL") ?>" type="datetime" name="expira_at" placeholder="Escribir Expira At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="archivo_url">Archivo Url </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-archivo_url-holder" class=" ">
                                                <input id="ctrl-archivo_url" data-field="archivo_url"  value="<?php echo get_value('archivo_url', "NULL") ?>" type="text" placeholder="Escribir Archivo Url"  name="archivo_url"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="archivo_mime">Archivo Mime </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-archivo_mime-holder" class=" ">
                                                <input id="ctrl-archivo_mime" data-field="archivo_mime"  value="<?php echo get_value('archivo_mime', "NULL") ?>" type="text" placeholder="Escribir Archivo Mime"  name="archivo_mime"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="archivo_size_kb">Archivo Size Kb </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-archivo_size_kb-holder" class=" ">
                                                <input id="ctrl-archivo_size_kb" data-field="archivo_size_kb"  value="<?php echo get_value('archivo_size_kb', "NULL") ?>" type="number" placeholder="Escribir Archivo Size Kb" step="any"  name="archivo_size_kb"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="hash_sha256">Hash Sha256 </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-hash_sha256-holder" class=" ">
                                                <input id="ctrl-hash_sha256" data-field="hash_sha256"  value="<?php echo get_value('hash_sha256', "NULL") ?>" type="text" placeholder="Escribir Hash Sha256"  name="hash_sha256"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="estado_verificacion">Estado Verificacion <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-estado_verificacion-holder" class=" ">
                                                <select required=""  id="ctrl-estado_verificacion" data-field="estado_verificacion" name="estado_verificacion"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::verificacionEstado();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('estado_verificacion', $value, "");
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
                                            <label class="control-label" for="verificado_por">Verificado Por </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-verificado_por-holder" class=" ">
                                                <select  id="ctrl-verificado_por" data-field="verificado_por" name="verificado_por"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->actor_user_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('verificado_por', $value, "");
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
                                            <label class="control-label" for="verificado_at">Verificado At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-verificado_at-holder" class="input-group ">
                                                <input id="ctrl-verificado_at" data-field="verificado_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('verificado_at', "NULL") ?>" type="datetime" name="verificado_at" placeholder="Escribir Verificado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="rechazo_motivo">Rechazo Motivo </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-rechazo_motivo-holder" class=" ">
                                                <input id="ctrl-rechazo_motivo" data-field="rechazo_motivo"  value="<?php echo get_value('rechazo_motivo', "NULL") ?>" type="text" placeholder="Escribir Rechazo Motivo"  name="rechazo_motivo"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="notas">Notas </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-notas-holder" class=" ">
                                                <textarea placeholder="Escribir Notas" id="ctrl-notas" data-field="notas"  rows="5" name="notas" class=" form-control"><?php echo get_value('notas') ?></textarea>
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
