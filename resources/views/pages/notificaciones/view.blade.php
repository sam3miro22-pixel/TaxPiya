<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("notificaciones/add");
    $can_edit = $user->canAccess("notificaciones/edit");
    $can_view = $user->canAccess("notificaciones/view");
    $can_delete = $user->canAccess("notificaciones/delete");
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
                                                    <small class="text-muted">User Id</small>
                                                    <div class="fw-bold">
                                                        <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[user_id]?subpage=1") ?>">
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
                                            <small class="text-muted">Canal</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['canal'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Proveedor</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['proveedor'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Titulo</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['titulo'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Cuerpo</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['cuerpo'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Data Json</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['data_json'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Device Token Snapshot</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['device_token_snapshot'] ; ?>
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
                                            <small class="text-muted">Programada At</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['programada_at'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Enviada At</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['enviada_at'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Entregada At</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['entregada_at'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Abierta At</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['abierta_at'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Fallida At</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['fallida_at'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Provider Message Id</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['provider_message_id'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Error Code</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['error_code'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Error Message</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['error_message'] ; ?>
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
                                            <small class="text-muted">Prioridad</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['prioridad'] ; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12  col-md-4">
                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">Origen Evento</small>
                                            <div class="fw-bold">
                                                <?php echo  $data['origen_evento'] ; ?>
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
                            <a class="btn btn-success has-tooltip "   title="Editar" href="<?php print_link("notificaciones/edit/$rec_id"); ?>" >
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <?php } ?>
                        <?php if($can_delete){ ?>
                        <a class="btn btn-danger has-tooltip record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" title="Borrar" href="<?php print_link("notificaciones/delete/$rec_id?redirect=notificaciones"); ?>" >
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
