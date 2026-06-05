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
                        <form id="usuariodispositivos-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('usuariodispositivos.store') }}" method="post">
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
                                                    $selected = Html::get_field_selected('user_id', $value, "");
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
                                            <label class="control-label" for="device_uuid">Device Uuid <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-device_uuid-holder" class=" ">
                                                <input id="ctrl-device_uuid" data-field="device_uuid"  value="<?php echo get_value('device_uuid') ?>" type="text" placeholder="Escribir Device Uuid"  required="" name="device_uuid"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="plataforma">Plataforma <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-plataforma-holder" class=" ">
                                                <select required=""  id="ctrl-plataforma" data-field="plataforma" name="plataforma"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php
                                                    $options = Menu::plataforma();
                                                    if(!empty($options)){
                                                    foreach($options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $selected = Html::get_field_selected('plataforma', $value, "");
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
                                            <label class="control-label" for="app_version">App Version </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-app_version-holder" class=" ">
                                                <input id="ctrl-app_version" data-field="app_version"  value="<?php echo get_value('app_version', "NULL") ?>" type="text" placeholder="Escribir App Version"  name="app_version"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="os_version">Os Version </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-os_version-holder" class=" ">
                                                <input id="ctrl-os_version" data-field="os_version"  value="<?php echo get_value('os_version', "NULL") ?>" type="text" placeholder="Escribir Os Version"  name="os_version"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="idioma">Idioma </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-idioma-holder" class=" ">
                                                <input id="ctrl-idioma" data-field="idioma"  value="<?php echo get_value('idioma', "'es-CO'") ?>" type="text" placeholder="Escribir Idioma"  name="idioma"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="zona_horaria">Zona Horaria </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-zona_horaria-holder" class=" ">
                                                <input id="ctrl-zona_horaria" data-field="zona_horaria"  value="<?php echo get_value('zona_horaria', "NULL") ?>" type="text" placeholder="Escribir Zona Horaria"  name="zona_horaria"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="fabricante">Fabricante </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-fabricante-holder" class=" ">
                                                <input id="ctrl-fabricante" data-field="fabricante"  value="<?php echo get_value('fabricante', "NULL") ?>" type="text" placeholder="Escribir Fabricante"  name="fabricante"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="modelo">Modelo </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-modelo-holder" class=" ">
                                                <input id="ctrl-modelo" data-field="modelo"  value="<?php echo get_value('modelo', "NULL") ?>" type="text" placeholder="Escribir Modelo"  name="modelo"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="notificaciones_activas">Notificaciones Activas <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-notificaciones_activas-holder" class=" ">
                                                <input id="ctrl-notificaciones_activas" data-field="notificaciones_activas"  value="<?php echo get_value('notificaciones_activas', "1") ?>" type="number" placeholder="Escribir Notificaciones Activas" step="any"  required="" name="notificaciones_activas"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="activo">Activo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-activo-holder" class=" ">
                                                <input id="ctrl-activo" data-field="activo"  value="<?php echo get_value('activo', "1") ?>" type="number" placeholder="Escribir Activo" step="any"  required="" name="activo"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="is_emulador">Is Emulador <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-is_emulador-holder" class=" ">
                                                <input id="ctrl-is_emulador" data-field="is_emulador"  value="<?php echo get_value('is_emulador', "0") ?>" type="number" placeholder="Escribir Is Emulador" step="any"  required="" name="is_emulador"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="root_jailbreak">Root Jailbreak <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-root_jailbreak-holder" class=" ">
                                                <input id="ctrl-root_jailbreak" data-field="root_jailbreak"  value="<?php echo get_value('root_jailbreak', "0") ?>" type="number" placeholder="Escribir Root Jailbreak" step="any"  required="" name="root_jailbreak"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="installed_at">Installed At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-installed_at-holder" class="input-group ">
                                                <input id="ctrl-installed_at" data-field="installed_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('installed_at', "NULL") ?>" type="datetime" name="installed_at" placeholder="Escribir Installed At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="last_seen_at">Last Seen At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-last_seen_at-holder" class="input-group ">
                                                <input id="ctrl-last_seen_at" data-field="last_seen_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('last_seen_at', "NULL") ?>" type="datetime" name="last_seen_at" placeholder="Escribir Last Seen At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="last_ip">Last Ip </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-last_ip-holder" class=" ">
                                                <input id="ctrl-last_ip" data-field="last_ip"  value="<?php echo get_value('last_ip', "NULL") ?>" type="text" placeholder="Escribir Last Ip"  name="last_ip"  class="form-control " />
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
