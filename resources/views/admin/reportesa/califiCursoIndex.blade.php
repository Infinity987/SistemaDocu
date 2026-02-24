@extends('adminlte::page')

@section('title', 'Docente curso')

@section('content_header')
    @can('asignarCurso.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-10">
                            <h1><i class="fa fa-users" aria-hidden="true"></i> - Reporte calificaciones de curso y Módulos</h1>
                        </div>
                        <div class="col-sm-2">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Inicio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('asignarCurso.index')
        @livewire('Admin.alumno-matricula-notas')
    @endcan
@stop

@section('css')
    @livewireStyles

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@stop


@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        document.addEventListener('livewire:init', () => {

            window.addEventListener('abrirPdf', event => {
                const url = event.detail.url
                window.open(url, '_blank');
            });
        });
    </script>
@stop
