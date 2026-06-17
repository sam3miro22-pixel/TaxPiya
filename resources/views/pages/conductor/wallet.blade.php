@php $pageTitle = 'Mi billetera'; @endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mi billetera</h1>
    </header>

    @if(session('wallet_ok'))<div class="txp-mobile-card txp-alert-success">{{ session('wallet_ok') }}</div>@endif
    @if(session('wallet_error'))<div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">{{ session('wallet_error') }}</div>@endif

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo disponible</div>
        <div class="txp-wallet-amount">${{ number_format((float)($cuenta->saldo_actual ?? $saldo->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">
            @if($isFlota)
                Flota · Solo consulta (pagos los hace tu empresa)
            @else
                Mínimo operativo: ${{ number_format((float)($saldo->min_operativo ?? 0), 0, ',', '.') }} · {{ $saldo->moneda ?? 'COP' }}
            @endif
        </div>
        @if((int)($cuenta->bloqueado ?? $saldo->bloqueado ?? 0) === 1)
            <div class="txp-wallet-alert"><i class="fa-solid fa-lock"></i> Billetera bloqueada</div>
        @endif
    </div>

    @if(!$isFlota)
    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>Ingresos viajes</span><strong>${{ number_format($resumen['ingresos_viajes'] ?? 0, 0, ',', '.') }}</strong></div>
        <div class="txp-info-row"><span>Comisiones</span><strong>${{ number_format($resumen['comisiones'] ?? 0, 0, ',', '.') }}</strong></div>
        <div class="txp-info-row"><span>Retiros</span><strong>${{ number_format($resumen['retiros'] ?? 0, 0, ',', '.') }}</strong></div>
    </div>
    @else
    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>Ingresos viajes</span><strong>${{ number_format($resumen['ingresos_viajes'] ?? 0, 0, ',', '.') }}</strong></div>
        <div class="txp-info-row"><span>Pagos de empresa</span><strong>${{ number_format($resumen['pagos_empresa'] ?? 0, 0, ',', '.') }}</strong></div>
        <p class="small text-muted mb-0 mt-2"><i class="fa-solid fa-circle-info me-1"></i> Los depósitos y retiros los gestiona tu empresa desde su panel.</p>
    </div>
    @endif

    @if($cuenta && !(int)$cuenta->solo_lectura)
        @if((int)$cuenta->puede_depositar)
            @include('components.wallet-nequi-deposit', ['action' => route('conductor.wallet.depositar')])
        @endif
        @if((int)$cuenta->puede_retirar)
        <div class="txp-mobile-card mb-3">
            <h3 class="h6">Solicitar retiro</h3>
            <form method="post" action="{{ route('conductor.wallet.retirar') }}">
                @csrf
                <input type="number" name="monto" class="form-control txp-input-dark mb-2" min="10000" step="1000" required placeholder="Monto mín. $10.000">
                <button class="txp-mobile-btn w-100 border-0" type="submit" style="background:#334155;color:#fff;"><i class="fa-solid fa-arrow-up"></i> Solicitar retiro</button>
            </form>
            <p class="small text-muted mt-2 mb-0">Los retiros también requieren aprobación del administrador.</p>
        </div>
        @endif
    @endif

    @include('components.wallet-solicitudes-list', ['solicitudes' => $solicitudes ?? []])

    <h2 class="txp-section-title">Movimientos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
@endsection
