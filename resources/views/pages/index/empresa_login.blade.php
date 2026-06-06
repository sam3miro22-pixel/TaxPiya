<?php $pageTitle = 'Iniciar sesión - Empresa / Flota'; ?>
@extends('layouts.auth')

@section('title', $pageTitle)
@section('auth_body_class', 'txp-auth-body--empresa')

@section('content')
<div class="txp-auth-scene txp-auth-scene--empresa">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-orb txp-auth-orb--3"></div>
    <div class="txp-auth-card">
        @include('pages.index.loginview', ['app' => 'empresa'])
    </div>
</div>
@endsection
