@extends('adminlte::auth.register')

@section('title', ' <span style="color: red;">SIA GBM</span> | Dashboard')

@section('auth_header')
    <strong style="color: rgb(146, 89, 5);">Crear nueva cuenta</strong>
@endsection

@section('content')

@endsection

@section('adminlte_css')
    <style>
        body {
            background: url('{{ asset('foto/gb.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Crear una capa oscura encima */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(193, 83, 4, 0.212); /* Color negro con opacidad 50% */
            z-index: -1; /* Para que no cubra el contenido */
        }

    </style>
@endsection
