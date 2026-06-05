<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("sosincidentes/add");
    $can_edit = $user->canAccess("sosincidentes/edit");
    $can_view = $user->canAccess("sosincidentes/view");
    $can_delete = $user->canAccess("sosincidentes/delete");
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
                                                <small class="text-muted">Actor Tipo</small>
                                                <div class="fw-bold">
                                                    <?php echo  $data['actor_tipo'] ; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12  col-md-4">
                                    <div class="bg-light mb-1 card-1 p-2 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <small class="text-muted">Actor User Id</small>
                                                <div class="fw-bold">
                                                    <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[actor_user_id]?subpage=1") ?>">
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
                                            <small class="text-muted">Conductor Id</small>
                                            <div class="fw-bold">
                                                <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("conductores/view/$data[conductor_id]?subpage=1") ?>">
                                                <i class="fa fa-eye"></i> <?php echo "Conductores Detail" ?>
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
                                        <small class="text-muted">Categoria</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['categoria'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Severidad</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['severidad'] ; ?>
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
                                        <small class="text-muted">Descripcion</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['descripcion'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Telefono Contacto</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['telefono_contacto'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Lat</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['lat'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Lng</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['lng'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Ubicacion</small>
                                        <div class="fw-bold">
                                            <?php echo  $data['ubicacion'] ; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  col-md-4">
                            <div class="bg-light mb-1 card-1 p-2 border rounded">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted">Operador Id</small>
                                        <div class="fw-bold">
                                            <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("users/view/$data[operador_id]?subpage=1") ?>">
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
                                    <small class="text-muted">Asignado At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['asignado_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Reconocido At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['reconocido_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Atendido At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['atendido_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Resuelto At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['resuelto_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Cerrado At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['cerrado_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Nivel Escalamiento</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['nivel_escalamiento'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Sla Minutos</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['sla_minutos'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Breach At</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['breach_at'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Contacto Inicial</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['contacto_inicial'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Contacto Resultado</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['contacto_resultado'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Evidencia Url</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['evidencia_url'] ; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  col-md-4">
                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Notas Operacion</small>
                                    <div class="fw-bold">
                                        <?php echo  $data['notas_operacion'] ; ?>
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
                    <a class="btn btn-success has-tooltip "   title="Editar" href="<?php print_link("sosincidentes/edit/$rec_id"); ?>" >
                    <i class="fa fa-edit"></i> Edit
                </a>
                <?php } ?>
                <?php if($can_delete){ ?>
                <a class="btn btn-danger has-tooltip record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" title="Borrar" href="<?php print_link("sosincidentes/delete/$rec_id?redirect=sosincidentes"); ?>" >
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
