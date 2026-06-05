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
                        <form novalidate  id="" role="form" enctype="multipart/form-data"  class="form page-form form-horizontal needs-validation" action="<?php print_link("walletmovimientos/edit/$rec_id"); ?>" method="post">
                        <!--[form-content-start]-->
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
                                        <label class="control-label" for="admin_user_id">Admin User Id </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-admin_user_id-holder" class=" ">
                                            <select  id="ctrl-admin_user_id" data-field="admin_user_id" name="admin_user_id"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->actor_user_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['admin_user_id'] ? 'selected' : null );
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
                                        <label class="control-label" for="sentido">Sentido <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-sentido-holder" class=" ">
                                            <select required=""  id="ctrl-sentido" data-field="sentido" name="sentido"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::sentido();
                                                $field_value = $data['sentido'];
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
                                        <label class="control-label" for="motivo">Motivo <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-motivo-holder" class=" ">
                                            <select required=""  id="ctrl-motivo" data-field="motivo" name="motivo"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = Menu::motivo();
                                                $field_value = $data['motivo'];
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
                                        <label class="control-label" for="monto">Monto <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-monto-holder" class=" ">
                                            <input id="ctrl-monto" data-field="monto"  value="<?php  echo $data['monto']; ?>" type="number" placeholder="Escribir Monto" step="0.1"  required="" name="monto"  class="form-control " />
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
                                        <label class="control-label" for="saldo_antes">Saldo Antes </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-saldo_antes-holder" class=" ">
                                            <input id="ctrl-saldo_antes" data-field="saldo_antes"  value="<?php  echo $data['saldo_antes']; ?>" type="number" placeholder="Escribir Saldo Antes" step="0.1"  name="saldo_antes"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="saldo_despues">Saldo Despues </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-saldo_despues-holder" class=" ">
                                            <input id="ctrl-saldo_despues" data-field="saldo_despues"  value="<?php  echo $data['saldo_despues']; ?>" type="number" placeholder="Escribir Saldo Despues" step="0.1"  name="saldo_despues"  class="form-control " />
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
                                            <input id="ctrl-descripcion" data-field="descripcion"  value="<?php  echo $data['descripcion']; ?>" type="text" placeholder="Escribir Descripcion"  name="descripcion"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="referencia_externa">Referencia Externa </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-referencia_externa-holder" class=" ">
                                            <input id="ctrl-referencia_externa" data-field="referencia_externa"  value="<?php  echo $data['referencia_externa']; ?>" type="text" placeholder="Escribir Referencia Externa"  name="referencia_externa"  class="form-control " />
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
                                        <label class="control-label" for="anulado">Anulado <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-anulado-holder" class=" ">
                                            <input id="ctrl-anulado" data-field="anulado"  value="<?php  echo $data['anulado']; ?>" type="number" placeholder="Escribir Anulado" step="any"  required="" name="anulado"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="anulado_por">Anulado Por </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-anulado_por-holder" class=" ">
                                            <select  id="ctrl-anulado_por" data-field="anulado_por" name="anulado_por"  placeholder="Seleccione un valor"    class="form-select" >
                                            <option value="">Seleccione un valor</option>
                                            <?php
                                                $options = $comp_model->actor_user_id_option_list() ?? [];
                                                foreach($options as $option){
                                                $value = $option->value;
                                                $label = $option->label ?? $value;
                                                $selected = ( $value == $data['anulado_por'] ? 'selected' : null );
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
                                        <label class="control-label" for="anulado_motivo">Anulado Motivo </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-anulado_motivo-holder" class=" ">
                                            <input id="ctrl-anulado_motivo" data-field="anulado_motivo"  value="<?php  echo $data['anulado_motivo']; ?>" type="text" placeholder="Escribir Anulado Motivo"  name="anulado_motivo"  class="form-control " />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="anulado_at">Anulado At </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div id="ctrl-anulado_at-holder" class="input-group ">
                                            <input id="ctrl-anulado_at" data-field="anulado_at" class="form-control datepicker  datepicker"  value="<?php  echo $data['anulado_at']; ?>" type="datetime" name="anulado_at" placeholder="Escribir Anulado At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
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
