<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("llamadas/add");
    $can_edit = $user->canAccess("llamadas/edit");
    $can_view = $user->canAccess("llamadas/view");
    $can_delete = $user->canAccess("llamadas/delete");
    $field_name = request()->segment(3);
    $field_value = request()->segment(4);
    $total_records = $records->total();
    $limit = $records->perPage();
    $record_count = count($records);
    $pageTitle = "Llamadas"; //set dynamic page title
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page" data-page-type="list" data-page-url="{{ url()->full() }}">
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
                        <div class="h5 font-weight-bold text-primary m-0">Llamadas</div>
                    </div>
                </div>
                <div class="col-auto  " >
                    <?php if($can_add){ ?>
                    <a  class="btn btn-primary btn-block" href="<?php print_link("llamadas/add", true) ?>" >
                    <i class="fa fa-plus"></i>                              
                    Agregar nuevo 
                </a>
                <?php } ?>
            </div>
            <div class="col-md-3  " >
                <!-- Page drop down search component -->
                <form  class="search" action="{{ url()->current() }}" method="get">
                    <input type="hidden" name="page" value="1" />
                    <div class="input-group">
                        <input value="<?php echo get_value('search'); ?>" class="form-control page-search" type="text" name="search"  placeholder="Buscar" />
                        <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </form>
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
            <div class="col comp-grid " >
                <div  class=" page-content" >
                    <div id="llamadas-list-records">
                        <div id="page-main-content" class="table-responsive">
                            <?php Html::page_bread_crumb("/llamadas/", $field_name, $field_value); ?>
                            <?php Html::display_page_errors($errors); ?>
                            <div class="filter-tags mb-2">
                                <?php Html::filter_tag('search', __('Search')); ?>
                            </div>
                            <table class="table table-hover table-striped table-sm text-left">
                                <thead class="table-header ">
                                    <tr>
                                        <?php if($can_delete){ ?>
                                        <th class="td-checkbox">
                                        <label class="form-check-label">
                                        <input class="toggle-check-all form-check-input" type="checkbox" />
                                        </label>
                                        </th>
                                        <?php } ?>
                                        <th class="td-id" > Id</th>
                                        <th class="td-viaje_id" > Viaje Id</th>
                                        <th class="td-llamador_user_id" > Llamador User Id</th>
                                        <th class="td-llamador_rol" > Llamador Rol</th>
                                        <th class="td-receptor_user_id" > Receptor User Id</th>
                                        <th class="td-receptor_rol" > Receptor Rol</th>
                                        <th class="td-tipo" > Tipo</th>
                                        <th class="td-provider" > Provider</th>
                                        <th class="td-provider_call_id" > Provider Call Id</th>
                                        <th class="td-provider_room_id" > Provider Room Id</th>
                                        <th class="td-caller_phone_snapshot" > Caller Phone Snapshot</th>
                                        <th class="td-callee_phone_snapshot" > Callee Phone Snapshot</th>
                                        <th class="td-proxy_number" > Proxy Number</th>
                                        <th class="td-masked" > Masked</th>
                                        <th class="td-estado" > Estado</th>
                                        <th class="td-call_start_at" > Call Start At</th>
                                        <th class="td-ring_start_at" > Ring Start At</th>
                                        <th class="td-connected_at" > Connected At</th>
                                        <th class="td-ended_at" > Ended At</th>
                                        <th class="td-duracion_seg" > Duracion Seg</th>
                                        <th class="td-dispositivo_id" > Dispositivo Id</th>
                                        <th class="td-ip" > Ip</th>
                                        <th class="td-idempotencia" > Idempotencia</th>
                                        <th class="td-created_at" > Created At</th>
                                        <th class="td-btn"></th>
                                    </tr>
                                </thead>
                                <?php
                                    if($total_records){
                                ?>
                                <tbody class="page-data">
                                    <!--record-->
                                    <?php
                                        $counter = 0;
                                        foreach($records as $data){
                                        $rec_id = ($data['id'] ? urlencode($data['id']) : null);
                                        $counter++;
                                    ?>
                                    <tr>
                                        <?php if($can_delete){ ?>
                                        <td class=" td-checkbox">
                                            <label class="form-check-label">
                                            <input class="optioncheck form-check-input" name="optioncheck[]" value="<?php echo $data['id'] ?>" type="checkbox" />
                                            </label>
                                        </td>
                                        <?php } ?>
                                        <!--PageComponentStart-->
                                        <td class="td-id">
                                            <a href="<?php print_link("/llamadas/view/$data[id]") ?>"><?php echo $data['id']; ?></a>
                                        </td>
                                        <td class="td-viaje_id">
                                            <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("viajes/view/$data[viaje_id]?subpage=1") ?>">
                                            <i class="fa fa-eye"></i> <?php echo "Viajes" ?>
                                        </a>
                                    </td>
                                    <td class="td-llamador_user_id">
                                        <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[llamador_user_id]?subpage=1") ?>">
                                        <i class="fa fa-eye"></i> <?php echo "Users" ?>
                                    </a>
                                </td>
                                <td class="td-llamador_rol">
                                    <?php echo  $data['llamador_rol'] ; ?>
                                </td>
                                <td class="td-receptor_user_id">
                                    <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[receptor_user_id]?subpage=1") ?>">
                                    <i class="fa fa-eye"></i> <?php echo "Users" ?>
                                </a>
                            </td>
                            <td class="td-receptor_rol">
                                <?php echo  $data['receptor_rol'] ; ?>
                            </td>
                            <td class="td-tipo">
                                <?php echo  $data['tipo'] ; ?>
                            </td>
                            <td class="td-provider">
                                <?php echo  $data['provider'] ; ?>
                            </td>
                            <td class="td-provider_call_id">
                                <?php echo  $data['provider_call_id'] ; ?>
                            </td>
                            <td class="td-provider_room_id">
                                <?php echo  $data['provider_room_id'] ; ?>
                            </td>
                            <td class="td-caller_phone_snapshot">
                                <a href="<?php print_link("tel:$data[caller_phone_snapshot]") ?>"><?php echo $data['caller_phone_snapshot']; ?></a>
                            </td>
                            <td class="td-callee_phone_snapshot">
                                <a href="<?php print_link("tel:$data[callee_phone_snapshot]") ?>"><?php echo $data['callee_phone_snapshot']; ?></a>
                            </td>
                            <td class="td-proxy_number">
                                <?php echo  $data['proxy_number'] ; ?>
                            </td>
                            <td class="td-masked">
                                <?php echo  $data['masked'] ; ?>
                            </td>
                            <td class="td-estado">
                                <?php echo  $data['estado'] ; ?>
                            </td>
                            <td class="td-call_start_at">
                                <?php echo  $data['call_start_at'] ; ?>
                            </td>
                            <td class="td-ring_start_at">
                                <?php echo  $data['ring_start_at'] ; ?>
                            </td>
                            <td class="td-connected_at">
                                <?php echo  $data['connected_at'] ; ?>
                            </td>
                            <td class="td-ended_at">
                                <?php echo  $data['ended_at'] ; ?>
                            </td>
                            <td class="td-duracion_seg">
                                <?php echo  $data['duracion_seg'] ; ?>
                            </td>
                            <td class="td-dispositivo_id">
                                <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("usuariodispositivos/view/$data[dispositivo_id]?subpage=1") ?>">
                                <i class="fa fa-eye"></i> <?php echo "Usuario Dispositivos" ?>
                            </a>
                        </td>
                        <td class="td-ip">
                            <?php echo  $data['ip'] ; ?>
                        </td>
                        <td class="td-idempotencia">
                            <?php echo  $data['idempotencia'] ; ?>
                        </td>
                        <td class="td-created_at">
                            <?php echo  $data['created_at'] ; ?>
                        </td>
                        <!--PageComponentEnd-->
                        <td class="td-btn">
                            <div class="dropdown" >
                                <button data-bs-toggle="dropdown" class="dropdown-toggle btn text-primary btn-flat">
                                <i class="fa fa-bars"></i> 
                                </button>
                                <ul class="dropdown-menu">
                                    <span id="record-{{ $rec_id }}">
                                    <?php if($can_view){ ?>
                                    <a class="dropdown-item "   href="<?php print_link("llamadas/view/$rec_id"); ?>" >
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <?php } ?>
                                <?php if($can_edit){ ?>
                                <a class="dropdown-item "   href="<?php print_link("llamadas/edit/$rec_id"); ?>" >
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <?php } ?>
                            <?php if($can_delete){ ?>
                            <a class="dropdown-item record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" href="<?php print_link("llamadas/delete/$rec_id"); ?>" >
                            <i class="fa fa-times"></i> Delete
                        </a>
                        <?php } ?>
                        </span>
                    </ul>
                </div>
            </td>
        </tr>
        <?php 
            }
        ?>
        <!--endrecord-->
    </tbody>
    <tbody class="search-data"></tbody>
    <?php
        }
        else{
    ?>
    <tbody class="page-data">
        <tr>
            <td class="bg-light text-center text-muted animated bounce p-3" colspan="1000">
                <i class="fa fa-ban"></i> ningún record fue encontrado
            </td>
        </tr>
    </tbody>
    <?php
        }
    ?>
</table>
</div>
<?php
    if($show_footer){
?>
<div class=" mt-3">
    <div class="row align-items-center justify-content-between">    
        <div class="col-md-auto d-flex gap-2">
            <?php if($can_delete){ ?>
            <button data-prompt-msg="¿Está seguro de que desea eliminar estos registros?" data-display-style="modal" data-url="<?php print_link("llamadas/delete/{sel_ids}"); ?>" class="btn btn-danger btn-delete-selected d-none">
            <i class="fa fa-times"></i> Eliminar seleccionado
            </button>
            <?php } ?>
            <div class="dropup export-btn-holder">
                <button  class="btn btn-outline-primary dropdown-toggle" title="Export" type="button" data-bs-toggle="dropdown">
                <i class="fa fa-save"></i> Exportar
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php Html :: export_menus(['pdf', 'print', 'excel', 'csv']); ?>
                </div>
            </div>
            <?php Html :: import_form('llamadas/importdata' , "Datos de importacion"); ?>
        </div>
        <div class="col">   
            <?php
                if($show_pagination == true){
                $pager = new Pagination($total_records, $record_count);
                $pager->show_page_count = false;
                $pager->show_record_count = true;
                $pager->show_page_limit =false;
                $pager->limit = $limit;
                $pager->show_page_number_list = true;
                $pager->pager_link_range=5;
                $pager->render();
                }
            ?>
        </div>
    </div>
</div>
<?php
    }
?>
</div>
</div>
</div>
</div>
</div>
</div>
</section>


@endsection
