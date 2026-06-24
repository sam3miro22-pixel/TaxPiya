<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("conductorposicionactual/add");
    $can_edit = $user->canAccess("conductorposicionactual/edit");
    $can_view = $user->canAccess("conductorposicionactual/view");
    $can_delete = $user->canAccess("conductorposicionactual/delete");
    $field_name = request()->segment(3);
    $field_value = request()->segment(4);
    $total_records = $records->total();
    $limit = $records->perPage();
    $record_count = count($records);
    $pageTitle = "Conductor Posicion Actual"; //set dynamic page title
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
                    <a class="back-btn btn btn-secondary" href="{{ url('home') }}" >
                        <i class="fa fa-angle-left"></i>                                
                    </a>
                </div>
                <div class="col  " >
                    <div class="">
                        <div class="h5 font-weight-bold text-primary m-0">Conductor Posicion Actual</div>
                    </div>
                </div>
                <div class="col-auto  " >
                    <?php if($can_add){ ?>
                    <a  class="btn btn-primary btn-block" href="<?php print_link("conductorposicionactual/add", true) ?>" >
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
                    <div id="conductorposicionactual-list-records">
                        <div id="page-main-content" class="table-responsive">
                            <?php Html::page_bread_crumb("/conductorposicionactual/", $field_name, $field_value); ?>
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
                                        <th class="td-conductor_id" > Conductor Id</th>
                                        <th class="td-viaje_id" > Viaje Id</th>
                                        <th class="td-lat" > Lat</th>
                                        <th class="td-lng" > Lng</th>
                                        <th class="td-ubicacion" > Ubicacion</th>
                                        <th class="td-precision_m" > Precision M</th>
                                        <th class="td-velocidad_kmh" > Velocidad Kmh</th>
                                        <th class="td-heading" > Heading</th>
                                        <th class="td-origen" > Origen</th>
                                        <th class="td-provider" > Provider</th>
                                        <th class="td-bateria" > Bateria</th>
                                        <th class="td-app_estado" > App Estado</th>
                                        <th class="td-created_at" > Created At</th>
                                        <th class="td-actualizada_at" > Actualizada At</th>
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
                                        $rec_id = ($data['conductor_id'] ? urlencode($data['conductor_id']) : null);
                                        $counter++;
                                    ?>
                                    <tr>
                                        <?php if($can_delete){ ?>
                                        <td class=" td-checkbox">
                                            <label class="form-check-label">
                                            <input class="optioncheck form-check-input" name="optioncheck[]" value="<?php echo $data['conductor_id'] ?>" type="checkbox" />
                                            </label>
                                        </td>
                                        <?php } ?>
                                        <!--PageComponentStart-->
                                        <td class="td-conductor_id">
                                            <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("conductores/view/$data[conductor_id]?subpage=1") ?>">
                                            <i class="fa fa-eye"></i> <?php echo "Conductores" ?>
                                        </a>
                                    </td>
                                    <td class="td-viaje_id">
                                        <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("viajes/view/$data[viaje_id]?subpage=1") ?>">
                                        <i class="fa fa-eye"></i> <?php echo "Viajes" ?>
                                    </a>
                                </td>
                                <td class="td-lat">
                                    <?php echo  $data['lat'] ; ?>
                                </td>
                                <td class="td-lng">
                                    <?php echo  $data['lng'] ; ?>
                                </td>
                                <td class="td-ubicacion">
                                    <?php echo  $data['ubicacion'] ; ?>
                                </td>
                                <td class="td-precision_m">
                                    <?php echo  $data['precision_m'] ; ?>
                                </td>
                                <td class="td-velocidad_kmh">
                                    <?php echo  $data['velocidad_kmh'] ; ?>
                                </td>
                                <td class="td-heading">
                                    <?php echo  $data['heading'] ; ?>
                                </td>
                                <td class="td-origen">
                                    <?php echo  $data['origen'] ; ?>
                                </td>
                                <td class="td-provider">
                                    <?php echo  $data['provider'] ; ?>
                                </td>
                                <td class="td-bateria">
                                    <?php echo  $data['bateria'] ; ?>
                                </td>
                                <td class="td-app_estado">
                                    <?php echo  $data['app_estado'] ; ?>
                                </td>
                                <td class="td-created_at">
                                    <?php echo  $data['created_at'] ; ?>
                                </td>
                                <td class="td-actualizada_at">
                                    <?php echo  $data['actualizada_at'] ; ?>
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
                                            <a class="dropdown-item "   href="<?php print_link("conductorposicionactual/view/$rec_id"); ?>" >
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <?php } ?>
                                        <?php if($can_edit){ ?>
                                        <a class="dropdown-item "   href="<?php print_link("conductorposicionactual/edit/$rec_id"); ?>" >
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <?php } ?>
                                    <?php if($can_delete){ ?>
                                    <a class="dropdown-item record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" href="<?php print_link("conductorposicionactual/delete/$rec_id"); ?>" >
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
                <button data-prompt-msg="¿Está seguro de que desea eliminar estos registros?" data-display-style="modal" data-url="<?php print_link("conductorposicionactual/delete/{sel_ids}"); ?>" class="btn btn-danger btn-delete-selected d-none">
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
                <?php Html :: import_form('conductorposicionactual/importdata' , "Datos de importacion"); ?>
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
