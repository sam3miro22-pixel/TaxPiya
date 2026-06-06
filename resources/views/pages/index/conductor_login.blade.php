<?php $pageTitle = 'Iniciar sesión - Conductor'; ?>
@extends('layouts.auth')

@section('title', $pageTitle)
@section('auth_body_class', 'txp-auth-body--conductor')

@section('content')
<div class="txp-auth-scene txp-auth-scene--conductor">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-orb txp-auth-orb--3"></div>
    <div class="txp-auth-card">
        @include('pages.index.loginview', ['app' => 'conductor'])
    </div>
</div>
@endsection
