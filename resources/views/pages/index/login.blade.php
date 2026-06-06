
@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-orb txp-auth-orb--3"></div>
    <div class="txp-auth-card">
        @include('pages.index.loginview')
    </div>
</div>
@endsection
