
<?php
    $rec_id = $masterRecordId ?? null;
    $page_id = "tab-".random_str(6);
?>
<div class="master-detail-page card">
    <div class="card-header text-bold h5 p-3 mb-3">Users Records</div>
    
    <div class="p-2">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a data-bs-toggle="tab" href="#auditoriaeventos_<?php echo $page_id ?>" class="nav-link active">
                User Auditoria Eventos
            </a>
        </li>
        
        <li class="nav-item">
            <a data-bs-toggle="tab" href="#calificaciones_<?php echo $page_id ?>" class="nav-link ">
            User Calificaciones
        </a>
    </li>
    
    <li class="nav-item">
        <a data-bs-toggle="tab" href="#calificaciones_<?php echo $page_id ?>" class="nav-link ">
        User Calificaciones
    </a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#chatmensajes_<?php echo $page_id ?>" class="nav-link ">
    User Chat Mensajes
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#conductores_<?php echo $page_id ?>" class="nav-link ">
    User Conductores
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#documentosconductor_<?php echo $page_id ?>" class="nav-link ">
    User Documentos Conductor
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#llamadas_<?php echo $page_id ?>" class="nav-link ">
    User Llamadas
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#llamadas_<?php echo $page_id ?>" class="nav-link ">
    User Llamadas
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#notasoperacion_<?php echo $page_id ?>" class="nav-link ">
    User Notas Operacion
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#sosincidentes_<?php echo $page_id ?>" class="nav-link ">
    User Sos Incidentes
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#viajeestadoslog_<?php echo $page_id ?>" class="nav-link ">
    User Viaje Estados Log
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#viajes_<?php echo $page_id ?>" class="nav-link ">
    User Viajes
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#walletmovimientos_<?php echo $page_id ?>" class="nav-link ">
    User Wallet Movimientos
</a>
</li>

<li class="nav-item">
    <a data-bs-toggle="tab" href="#walletmovimientos_<?php echo $page_id ?>" class="nav-link ">
    User Wallet Movimientos
</a>
</li>

</ul>
</div>
<div class="tab-content">
    <div class="tab-pane fade show active" id="auditoriaeventos_<?php echo $page_id ?>" role="tabpanel">
    <div class=" ">
        <?php
            $params = ['actor_user_id' => $rec_id,'show_header' => false]; //new query param
            $query = array_merge(request()->query(), $params);
            $queryParams = http_build_query($query);
            $url = url("auditoriaeventos/index/actor_user_id/$rec_id?$queryParams");
        ?>
        <div class="ajax-inline-page" data-url="{{ $url }}" >
            <div class="ajax-page-load-indicator">
                <div class="text-center d-flex justify-content-center load-indicator">
                    <span class="loader mr-3"></span>
                    <span class="fw-bold">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="tab-pane fade show " id="calificaciones_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['rater_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("calificaciones/index/rater_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="calificaciones_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['ratee_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("calificaciones/index/ratee_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="chatmensajes_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['remitente_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("chatmensajes/index/remitente_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="conductores_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['user_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("conductores/index/user_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="documentosconductor_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['verificado_por' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("documentosconductor/index/verificado_por/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="llamadas_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['llamador_user_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("llamadas/index/llamador_user_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="llamadas_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['receptor_user_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("llamadas/index/receptor_user_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="notasoperacion_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['created_by' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("notasoperacion/index/created_by/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="sosincidentes_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['operador_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("sosincidentes/index/operador_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="viajeestadoslog_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['actor_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("viajeestadoslog/index/actor_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="viajes_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['pasajero_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("viajes/index/pasajero_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="walletmovimientos_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['admin_user_id' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("walletmovimientos/index/admin_user_id/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

<div class="tab-pane fade show " id="walletmovimientos_<?php echo $page_id ?>" role="tabpanel">
<div class=" ">
    <?php
        $params = ['anulado_por' => $rec_id,'show_header' => false]; //new query param
        $query = array_merge(request()->query(), $params);
        $queryParams = http_build_query($query);
        $url = url("walletmovimientos/index/anulado_por/$rec_id?$queryParams");
    ?>
    <div class="ajax-inline-page" data-url="{{ $url }}" >
        <div class="ajax-page-load-indicator">
            <div class="text-center d-flex justify-content-center load-indicator">
                <span class="loader mr-3"></span>
                <span class="fw-bold">Cargando...</span>
            </div>
        </div>
    </div>
</div>

</div>

</div>
</div>
