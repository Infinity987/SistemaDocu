@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="callout callout-danger estiTitulo">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fa fa-users" aria-hidden="true"></i> - VISTA DOCENTE</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">inicio</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>
@stop

@section('content')
    <H1>
        DOCENTE
    </H1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    @livewireScripts
    <script></script>

    <script>
        @if (session('success'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error"
            });
        @endif
    </script>
@stop
