<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("permissions/add");
    $can_edit = $user->canAccess("permissions/edit");
    $can_view = $user->canAccess("permissions/view");
    $can_delete = $user->canAccess("permissions/delete");
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
                            $rec_id = ($data['permission_id'] ? urlencode($data['permission_id']) : null);
                        ?>
                        <div id="page-main-content" class=" mb-3">
                            <div class="page-data">
                                <!--PageComponentStart-->
                                <div class="mb-3 row row justify-content-start g-0 gutter-lg">
                                    <div class="col-12  col-md-4">
                                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="text-muted">Permission Id</small>
                                                    <div class="fw-bold">
                                                        <?php echo  $data['permission_id'] ; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12  col-md-4">
                                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="text-muted">Permission</small>
                                                    <div class="fw-bold">
                                                        <?php echo  $data['permission'] ; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12  col-md-4">
                                        <div class="bg-light mb-1 card-1 p-2 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="text-muted">Role Id</small>
                                                    <div class="fw-bold">
                                                        <a size="sm" class="btn btn btn-secondary page-modal" href="<?php print_link("roles/view/$data[role_id]?subpage=1") ?>">
                                                        <i class="fa fa-eye"></i> <?php echo "Roles Detail" ?>
                                                    </a>
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
                                <a class="btn btn-success has-tooltip "   title="Editar" href="<?php print_link("permissions/edit/$rec_id"); ?>" >
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <?php } ?>
                            <?php if($can_delete){ ?>
                            <a class="btn btn-danger has-tooltip record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" title="Borrar" href="<?php print_link("permissions/delete/$rec_id?redirect=permissions"); ?>" >
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
