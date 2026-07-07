@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @can('documentario.mesapar.index')
        <div class="callout callout-danger mb-0">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-search"></i> <i class="fas fa-file-alt"></i>
                                - BUSCAR PAGOS</h1>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">inicio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('documentario.mesapar.index')
        <div class="pt-3">
            
  
            @if($id_depen == 9)
                @livewire('gestionar-permisos-pagos')
            
         
            @else
                @livewire('buscar-pagos', ['id_dependencia' => $id_depen])
            @endif

        </div>
    @endcan
@stop
{{-- @vite(['resources/js/app.js']) --}}

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
    @livewireStyles
@stop

@section('js')
    <script>
        window.dependenciaId = {{ $id_depen }}; // Esto se usa dentro de app.js
    </script>
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
    @livewireScripts
    
@stop
