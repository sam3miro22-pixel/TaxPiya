<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("pushtokens/add");
    $can_edit = $user->canAccess("pushtokens/edit");
    $can_view = $user->canAccess("pushtokens/view");
    $can_delete = $user->canAccess("pushtokens/delete");
    $field_name = request()->segment(3);
    $field_value = request()->segment(4);
    $total_records = $records->total();
    $limit = $records->perPage();
    $record_count = count($records);
    $pageTitle = "Push Tokens"; //set dynamic page title
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
                        <div class="h5 font-weight-bold text-primary m-0">Push Tokens</div>
                    </div>
                </div>
                <div class="col-auto  " >
                    <?php if($can_add){ ?>
                    <a  class="btn btn-primary btn-block" href="<?php print_link("pushtokens/add", true) ?>" >
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
                    <div id="pushtokens-list-records">
                        <div id="page-main-content" class="table-responsive">
                            <?php Html::page_bread_crumb("/pushtokens/", $field_name, $field_value); ?>
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
                                        <th class="td-dispositivo_id" > Dispositivo Id</th>
                                        <th class="td-provider" > Provider</th>
                                        <th class="td-token" > Token</th>
                                        <th class="td-token_hash" > Token Hash</th>
                                        <th class="td-estado" > Estado</th>
                                        <th class="td-scope" > Scope</th>
                                        <th class="td-ultimo_uso_at" > Ultimo Uso At</th>
                                        <th class="td-invalidado_at" > Invalidado At</th>
                                        <th class="td-motivo_invalidez" > Motivo Invalidez</th>
                                        <th class="td-idempotencia" > Idempotencia</th>
                                        <th class="td-created_at" > Created At</th>
                                        <th class="td-updated_at" > Updated At</th>
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
                                            <a href="<?php print_link("/pushtokens/view/$data[id]") ?>"><?php echo $data['id']; ?></a>
                                        </td>
                                        <td class="td-dispositivo_id">
                                            <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("usuariodispositivos/view/$data[dispositivo_id]?subpage=1") ?>">
                                            <i class="fa fa-eye"></i> <?php echo "Usuario Dispositivos" ?>
                                        </a>
                                    </td>
                                    <td class="td-provider">
                                        <?php echo  $data['provider'] ; ?>
                                    </td>
                                    <td class="td-token">
                                        <?php echo  $data['token'] ; ?>
                                    </td>
                                    <td class="td-token_hash">
                                        <?php echo  $data['token_hash'] ; ?>
                                    </td>
                                    <td class="td-estado">
                                        <?php echo  $data['estado'] ; ?>
                                    </td>
                                    <td class="td-scope">
                                        <?php echo  $data['scope'] ; ?>
                                    </td>
                                    <td class="td-ultimo_uso_at">
                                        <?php echo  $data['ultimo_uso_at'] ; ?>
                                    </td>
                                    <td class="td-invalidado_at">
                                        <?php echo  $data['invalidado_at'] ; ?>
                                    </td>
                                    <td class="td-motivo_invalidez">
                                        <?php echo  $data['motivo_invalidez'] ; ?>
                                    </td>
                                    <td class="td-idempotencia">
                                        <?php echo  $data['idempotencia'] ; ?>
                                    </td>
                                    <td class="td-created_at">
                                        <?php echo  $data['created_at'] ; ?>
                                    </td>
                                    <td class="td-updated_at">
                                        <?php echo  $data['updated_at'] ; ?>
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
                                                <a class="dropdown-item "   href="<?php print_link("pushtokens/view/$rec_id"); ?>" >
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <?php } ?>
                                            <?php if($can_edit){ ?>
                                            <a class="dropdown-item "   href="<?php print_link("pushtokens/edit/$rec_id"); ?>" >
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <?php } ?>
                                        <?php if($can_delete){ ?>
                                        <a class="dropdown-item record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" href="<?php print_link("pushtokens/delete/$rec_id"); ?>" >
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
                    <button data-prompt-msg="¿Está seguro de que desea eliminar estos registros?" data-display-style="modal" data-url="<?php print_link("pushtokens/delete/{sel_ids}"); ?>" class="btn btn-danger btn-delete-selected d-none">
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
                    <?php Html :: import_form('pushtokens/importdata' , "Datos de importacion"); ?>
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
