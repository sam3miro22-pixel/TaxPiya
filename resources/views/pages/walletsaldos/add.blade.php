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
                        <form id="walletsaldos-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="{{ route('walletsaldos.store') }}" method="post">
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
                                            <label class="control-label" for="saldo_actual">Saldo Actual <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-saldo_actual-holder" class=" ">
                                                <input id="ctrl-saldo_actual" data-field="saldo_actual"  value="<?php echo get_value('saldo_actual', "0.00") ?>" type="number" placeholder="Escribir Saldo Actual" step="0.1"  required="" name="saldo_actual"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="saldo_reservado">Saldo Reservado <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-saldo_reservado-holder" class=" ">
                                                <input id="ctrl-saldo_reservado" data-field="saldo_reservado"  value="<?php echo get_value('saldo_reservado', "0.00") ?>" type="number" placeholder="Escribir Saldo Reservado" step="0.1"  required="" name="saldo_reservado"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="min_operativo">Min Operativo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-min_operativo-holder" class=" ">
                                                <input id="ctrl-min_operativo" data-field="min_operativo"  value="<?php echo get_value('min_operativo', "0.00") ?>" type="number" placeholder="Escribir Min Operativo" step="0.1"  required="" name="min_operativo"  class="form-control " />
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
                                                <input id="ctrl-moneda" data-field="moneda"  value="<?php echo get_value('moneda', "'COP'") ?>" type="text" placeholder="Escribir Moneda"  required="" name="moneda"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="last_movimiento_id">Last Movimiento Id </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-last_movimiento_id-holder" class=" ">
                                                <select  id="ctrl-last_movimiento_id" data-field="last_movimiento_id" name="last_movimiento_id"  placeholder="Seleccione un valor"    class="form-select" >
                                                <option value="">Seleccione un valor</option>
                                                <?php 
                                                    $options = $comp_model->last_movimiento_id_option_list() ?? [];
                                                    foreach($options as $option){
                                                    $value = $option->value;
                                                    $label = $option->label ?? $value;
                                                    $selected = Html::get_field_selected('last_movimiento_id', $value, "");
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
                                            <label class="control-label" for="last_movimiento_at">Last Movimiento At </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-last_movimiento_at-holder" class="input-group ">
                                                <input id="ctrl-last_movimiento_at" data-field="last_movimiento_at" class="form-control datepicker  datepicker"  value="<?php echo get_value('last_movimiento_at', "NULL") ?>" type="datetime" name="last_movimiento_at" placeholder="Escribir Last Movimiento At" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="bloqueado">Bloqueado <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-bloqueado-holder" class=" ">
                                                <input id="ctrl-bloqueado" data-field="bloqueado"  value="<?php echo get_value('bloqueado', "0") ?>" type="number" placeholder="Escribir Bloqueado" step="any"  required="" name="bloqueado"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="motivo_bloqueo">Motivo Bloqueo </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="ctrl-motivo_bloqueo-holder" class=" ">
                                                <input id="ctrl-motivo_bloqueo" data-field="motivo_bloqueo"  value="<?php echo get_value('motivo_bloqueo', "NULL") ?>" type="text" placeholder="Escribir Motivo Bloqueo"  name="motivo_bloqueo"  class="form-control " />
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
