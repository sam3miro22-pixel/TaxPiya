@if(Auth::check())
<div id="topbar" class="navbar navbar-expand-md fixed-top navbar-dark topbar-txp">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="sidebar-toggler btn btn-dark mx-2">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand" href="/home">
            <img class="img-responsive topbar-logo" src="{{ asset(config('app.logo')) }}" />
        </a>

        <button type="button" class="navbar-toggler dropdown-toggle" data-bs-toggle="collapse" data-bs-target=".navbar-responsive-collapse"></button>

        <div class="navbar-collapse collapse navbar-responsive-collapse">
            <div class="me-auto"></div>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"> <?php $user_photo = $user->UserPhoto(); if($user_photo){ Html::img($user_photo, 30, 30); } else { ?> <span class="avatar-icon"><i class="fa fa-user"></i></span> <?php } ?> <span>Hola, <?php echo $user->UserName(); ?>!</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <a class="dropdown-item" href="<?php print_link('auth/logout') ?>">
                            <i class="fa fa-sign-out"></i> Cerrar sesión
                        </a>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</div>

<style>

.modal-content{
    background-color: #000000 !important;
}

.topbar-txp{
    background:#0B0F19 !important;
    border-bottom:1px solid #1f2937;
    padding:8px 0;
    box-shadow:0 2px 10px rgba(0,0,0,0.45);
}
.topbar-logo{
    height:42px;
    width:auto;
    filter:brightness(1.1);
}
.topbar-user{
    display:flex;
    align-items:center;
    font-weight:600;
    color:#e5e7eb !important;
}
.topbar-user:hover{
    color:#00E5FF !important;
}
.topbar-avatar{
    border-radius:50%;
}
.topbar-avatar-fallback{
    width:32px;
    height:32px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#1e293b;
    color:#e5e7eb;
    font-size:14px;
}
</style>

<nav id="sidebar" class="txp-sidebar">
    <ul class="nav navbar-nav w-100 flex-column align-self-start">

        <li class="menu-profile p-3 nav-item txp-profile-card">
            <div class="avatar mb-2 text-center">
                <a href="#">
                    <?php 
                        $user_photo = $user->UserPhoto();
                        if($user_photo){
                            Html::page_img($user_photo, 60, 60, "small");
                        } else {
                    ?>
                    <span class="avatar-icon txp-avatar-default"><i class="fa fa-user"></i></span>
                    <?php } ?>
                </a>
            </div>

            <div class="user-name h6 fw-bold text-white text-center">
                Hola {{ $user->UserName() }}
                <br>
                <small class="text-white-50 text-capitalize">
                    {{ implode(", ", $user->getRoleNames()) }}
                </small>
            </div>

            <div class="dropdown menu-dropdown mt-3 text-center">
                <button class="btn txp-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-user"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <a class="dropdown-item" href="<?php print_link('auth/logout') ?>">
                        <i class="fa fa-sign-out"></i> Cerrar sesión
                    </a>
                </ul>
            </div>
        </li>

    </ul>

    {{ Html::render_menu(Menu::navbarsideleft(), "nav navbar-nav w-100 flex-column align-self-start txp-menu", "accordion") }}
</nav>

<style>
.txp-sidebar{
    width:260px;
    background:linear-gradient(180deg,#0B0F19 0%, #0C1224 100%) !important;
    border-right:1px solid #1f2937 !important;
    padding-top:70px;
    box-shadow:4px 0 14px rgba(0,0,0,0.5);
}

.txp-profile-card{
    background:rgba(255,255,255,0.03);
    border-radius:20px;
    margin:10px;
    border:1px solid rgba(255,255,255,0.08);
}

.txp-avatar{
    border-radius:50%;
    box-shadow:0 0 12px rgba(0,229,255,0.3);
}

.txp-avatar-default{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#1e293b;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    color:#e5e7eb;
}

.txp-profile-btn{
    background:linear-gradient(135deg,#00E5FF,#0284c7);
    border:none;
    width:46px;
    height:46px;
    border-radius:50%;
    color:#0B0F19;
    font-weight:700;
    box-shadow:0 0 16px rgba(0,229,255,0.45);
}

.txp-menu li a{
    color:#cbd5e1 !important;
    padding:14px 20px;
    font-size:16px;
    transition:0.25s ease;
    border-left:4px solid transparent;
}

.txp-menu li a:hover{
    background:#111827;
    color:#00E5FF !important;
    border-left:4px solid #00E5FF;
}

.txp-menu .active > a{
    background:#1e293b !important;
    border-left:4px solid #00E5FF;
    color:#00E5FF !important;
}

#sidebar::-webkit-scrollbar{
    width:6px;
}
#sidebar::-webkit-scrollbar-thumb{
    background:#1e293b;
    border-radius:3px;
}
</style>

@else
<div id="topbar" class="navbar navbar-expand-md fixed-top navbar-dark topbar-txp">
    <div class="container-fluid">
        <a class="navbar-brand" href="/home">
            <img class="img-responsive topbar-logo" src="{{ asset(config('app.logo')) }}" />
        </a>
    </div>
</div>
@endif
