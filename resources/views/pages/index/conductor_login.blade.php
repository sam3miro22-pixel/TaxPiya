<?php $pageTitle = 'Iniciar sesión - Conductor'; ?>
@extends($layout)

@section('title', $pageTitle)

@section('content')
<div class="txp-auth-scene txp-auth-scene--conductor">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-card">
        @include('pages.index.loginview', ['app' => 'conductor'])
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-auth.css') }}">
@endsection
