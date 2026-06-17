@php
$estadoLabel = [
    'pendiente' => 'En revisión',
    'aprobada'  => 'Aprobada',
    'completado'=> 'Acreditada',
    'rechazada' => 'Rechazada',
];
$estadoClass = [
    'pendiente' => 'warning',
    'aprobada'  => 'info',
    'completado'=> 'success',
    'rechazada' => 'danger',
];
@endphp
@if(!empty($solicitudes))
    <h2 class="txp-section-title">Solicitudes de recarga</h2>
    @foreach($solicitudes as $sol)
        <div class="txp-mobile-card txp-mov-card">
            <div class="txp-mov-top">
                <span class="txp-mov-badge txp-mov-badge--credito">
                    +${{ number_format((float)$sol->monto, 0, ',', '.') }}
                </span>
                <span class="badge bg-{{ $estadoClass[$sol->estado] ?? 'secondary' }}">
                    {{ $estadoLabel[$sol->estado] ?? $sol->estado }}
                </span>
            </div>
            <div class="txp-mov-desc">NEQUI · Ref. {{ $sol->referencia_pago ?? '—' }}</div>
            <div class="txp-mov-sub">{{ $sol->created_at ?? '' }}</div>
            @if($sol->estado === 'rechazada' && !empty($sol->notas))
                <div class="txp-mov-sub text-danger">{{ $sol->notas }}</div>
            @endif
            @if($sol->estado === 'pendiente')
                <div class="txp-mov-sub text-muted"><i class="fa-solid fa-clock me-1"></i> Esperando aprobación del administrador</div>
            @endif
        </div>
    @endforeach
@endif
