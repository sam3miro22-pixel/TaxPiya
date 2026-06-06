
@extends($layout)

@section('title', 'Iniciar sesión')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-card">
        @include('pages.index.loginview')
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-auth.css') }}">
@endsection
