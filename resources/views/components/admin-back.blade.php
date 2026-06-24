@props(['url' => url('home'), 'label' => 'Panel'])
<a href="{{ $url }}" class="btn btn-sm btn-outline-light rounded-pill px-3 mb-3 d-inline-flex align-items-center gap-1">
    <i class="fa fa-arrow-left"></i> {{ $label }}
</a>
