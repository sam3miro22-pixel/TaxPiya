<nav class="txp-empresa-nav">
    <a href="{{ route('empresa.dashboard') }}" class="txp-empresa-nav__item {{ request()->routeIs('empresa.dashboard') ? 'is-active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i><span>Inicio</span>
    </a>
    <a href="{{ route('empresa.flota') }}" class="txp-empresa-nav__item {{ request()->routeIs('empresa.flota*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-taxi"></i><span>Flota</span>
    </a>
    <a href="{{ route('empresa.viajes') }}" class="txp-empresa-nav__item {{ request()->routeIs('empresa.viajes') ? 'is-active' : '' }}">
        <i class="fa-solid fa-route"></i><span>Viajes</span>
    </a>
    <a href="{{ route('empresa.wallet') }}" class="txp-empresa-nav__item {{ request()->routeIs('empresa.wallet') ? 'is-active' : '' }}">
        <i class="fa-solid fa-wallet"></i><span>Billetera</span>
    </a>
    <a href="{{ route('empresa.cuenta') }}" class="txp-empresa-nav__item {{ request()->routeIs('empresa.cuenta') ? 'is-active' : '' }}">
        <i class="fa-solid fa-building"></i><span>Empresa</span>
    </a>
</nav>
