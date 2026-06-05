<!-- 
expose component model to current view
e.g $arrDataFromDb = $comp_model->fetchData(); //function name
-->
@inject('comp_model', 'App\Models\ComponentsData')
<?php
    //check if current user role is allowed access to the pages
    $can_add = $user->canAccess("roles/add");
    $can_edit = $user->canAccess("roles/edit");
    $can_view = $user->canAccess("roles/view");
    $can_delete = $user->canAccess("roles/delete");
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
                            $rec_id = ($data['role_id'] ? urlencode($data['role_id']) : null);
                        ?>
                        <div id="page-main-content" class=" mb-3">
                            <div class="row gutter-lg ">
                                <div class="col">
                                    <div class="page-data">
                                        <!--PageComponentStart-->
                                        <div class="mb-3 row row justify-content-start g-0 gutter-lg">
                                            <div class="col-12  col-md-4">
                                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <small class="text-muted">Role Id</small>
                                                            <div class="fw-bold">
                                                                <?php echo  $data['role_id'] ; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12  col-md-4">
                                                <div class="bg-light mb-1 card-1 p-2 border rounded">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <small class="text-muted">Role Name</small>
                                                            <div class="fw-bold">
                                                                <?php echo  $data['role_name'] ; ?>
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
                                            <a class="btn btn-success has-tooltip "   title="Editar" href="<?php print_link("roles/edit/$rec_id"); ?>" >
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <?php } ?>
                                        <?php if($can_delete){ ?>
                                        <a class="btn btn-danger has-tooltip record-delete-btn" data-prompt-msg="¿Seguro que quieres borrar este registro?" data-display-style="modal" title="Borrar" href="<?php print_link("roles/delete/$rec_id?redirect=roles"); ?>" >
                                        <i class="fa fa-times"></i> Delete
                                    </a>
                                    <?php } ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Detail Page Column -->
                        <?php if(!request()->has('subpage')){ ?>
                        <div class="col-12">
                            <div class="my-3 p-1 ">
                                @include("pages.roles.detail-pages", ["masterRecordId" => $rec_id])
                            </div>
                        </div>
                        <?php } ?>
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
