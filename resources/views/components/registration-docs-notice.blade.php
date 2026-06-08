@php
    $docsEmail = config('taxpiya.registration.docs_email', 'taxpiya20@gmail.com');
    $roleLabel = $roleLabel ?? 'tu solicitud';
@endphp
<div class="txp-docs-notice">
    <h3 class="txp-docs-notice__title"><i class="fa-solid fa-envelope-open-text me-2"></i>Documentación requerida</h3>
    <p class="txp-docs-notice__text mb-2">
        Después de enviar {{ $roleLabel }}, envía los documentos solicitados a:
        <strong><a href="mailto:{{ $docsEmail }}">{{ $docsEmail }}</a></strong>
    </p>
    <ul class="txp-docs-notice__list mb-0">
        @foreach(($documents ?? []) as $doc)
            <li>{{ $doc }}</li>
        @endforeach
    </ul>
    <p class="txp-docs-notice__hint mb-0 mt-2">Tu cuenta quedará en <strong>revisión</strong> hasta que el administrador apruebe la documentación.</p>
</div>
