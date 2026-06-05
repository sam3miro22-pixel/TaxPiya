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
    $pageTitle = "Ver"; //set dynamic page title
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page" data-page-type="view" data-page-url="{{ url()->full() }}">
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
                                <i class="fa fa-eye"></i>
                            </div>
                            <div class="col">
                                <div class="h5 font-weight-bold text-primary m-0">Ver</div>
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
                <div class="col comp-grid " >
                    <div  class=" page-content" >
                        <?php
                            if($data){
                            $rec_id = ($data['id'] ? urlencode($data['id']) : null);
                        ?>
                        <div id="page-main-content" class=" mb-3">
                            <div class="page-data">
                                <!--PageComponentStart-->
                                <div class="mb-3 row row justify-content-start g-0 gutter-lg">
                                    <div class="col-12  col-md-4">
                                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="text-muted">Id</small>
                                                    <div class="fw-bold">
                                                        <?php echo  $data['id'] ; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12  col-md-4">
                                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="text-muted">Viaje Id</small>
                                                    <div class="fw-bold">
                                                        <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("viajes/view/$data[viaje_id]?subpage=1") ?>">
                                                        <i class="fa fa-eye"></i> <?php echo "Viajes Detail" ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12  col-md-4">
                                    <div class="bg-light mb-1 card-1 p-2 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <small class="text-muted">Llamador User Id</small>
                                                <div class="fw-bold">
                                                    <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[llamador_user_id]?subpage=1") ?>">
                                                    <i class="fa fa-eye"></i> <?php echo "Users Detail" ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Llamador Rol</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['llamador_rol'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Receptor User Id</small>
                                            <div class="fw-bold">
                                                <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[receptor_user_id]?subpage=1") ?>">
                                                <i class="fa fa-eye"></i> <?php echo "Users Detail" ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Receptor Rol</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['receptor_rol'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Tipo</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['tipo'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Provider</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['provider'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Provider Call Id</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['provider_call_id'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Provider Room Id</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['provider_room_id'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Caller Phone Snapshot</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['caller_phone_snapshot'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Callee Phone Snapshot</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['callee_phone_snapshot'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Proxy Number</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['proxy_number'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Masked</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['masked'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Estado</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['estado'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Call Start At</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['call_start_at'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Ring Start At</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['ring_start_at'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Connected At</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['connected_at'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Ended At</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['ended_at'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Duracion Seg</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['duracion_seg'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Dispositivo Id</small>
                                        <div class="fw-bold">
                                            <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("usuariodispositivos/view/$data[dispositivo_id]?subpage=1") ?>">
                                            <i class="fa fa-eye"></i> <?php echo "Usuario Dispositivos Detail" ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Ip</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['ip'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Idempotencia</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['idempotencia'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Created At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['created_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--PageComponentEnd-->
                <div class="d-flex align-items-center gap-2">
                    <div class="dropup export-btn-holder">
                        <button  class="btn btn-outline-primary dropdown-toggle" title="Export" type="button" data-bs-toggle="dropdown">
                        <i class="fa fa-save"></i> Exportar
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php Html :: export_menus(['pdf', 'print']); ?>
                        </div>
                    </div>
                    <span id="record-{{ $rec_id }}">
                    <?php if($can_edit){ ?>
                    <a class="btn btn-success has-tooltip "   title="Editar" href="<?php print_link("llamadas/edit/$rec_id"); ?>" >
                    <i class="fa fa-edit"></i> Edit
                </a>
                <?php } ?>
                <?php if($can_delete){ ?>
                <a class="btn btn-danger has-tooltip record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" title="Borrar" href="<?php print_link("llamadas/delete/$rec_id?redirect=llamadas"); ?>" >
                <i class="fa fa-times"></i> Delete
            </a>
            <?php } ?>
            </span>
        </div>
    </div>
</div>
<?php
    }
    else{
?>
<!-- Empty Record Message -->
<div class="text-muted p-3">
    <i class="fa fa-ban"></i> ningún record fue encontrado
</div>
<?php
    }
?>
</div>
</div>
</div>
</div>
</div>
</section>


@endsection
