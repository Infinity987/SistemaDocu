@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @can('docente.horario')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1><i class="fas fa-calendar-alt"></i> - VER HORARIO</h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('docente.horario') }}">Horarios</a></li>
                                <li class="breadcrumb-item active">Ver horario</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('docente.horario')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">{{ 'Periodo '.$año.' - '.$periodo }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">{{ $nombre_de_carrera }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Malla: {{ $año_de_inicio }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Ciclo: {{ $nombre_ciclo }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Turno: {{ $nombre_turno }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">{{ $codigo_aula }} - {{ $aula_nombre }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;"></a>Seccion: {{ $nom_seccion }}</li>

                    </ol>
                </div>
            </div>
        </div>
        {{-- @dump($queryHorari) --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    @if ($contMaña > 0)
                        <div class="card card-info shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO MAÑANA</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="thead" style="background: linear-gradient(-50deg, #024f9c, #6aadcf); color: #ffffff; text-align: center; font-weight: bold;">
                                            <tr>
                                                <th style="width: 15%;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 15%;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 1)
                                                    <tr>
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div class="d-flex align-items-center">
                                                                        @foreach ($queryHorari as $queryHora)
                                                                            @if ($queryHora->idhora === $hora->idhora && $queryHora->iddias === $dia->iddias)
                                                                                {{ $queryHora->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-2 p-2" style="background: linear-gradient(-50deg, #9c4702, #cf8d6a); color: #ffffff; text-align: center; font-weight: bold;">RECESO</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif

                    @if ($contTard > 0)
                        <div class="card card-warning shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO TARDE</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="thead" style="background: linear-gradient(-50deg, #9c5c02, #f07634); color: #ffffff; text-align: center; font-weight: bold;">
                                            <tr>
                                                <th style="width: 15%;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 15%;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 2)
                                                    <tr>
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div class="d-flex align-items-center">
                                                                        @foreach ($queryHorari as $queryHora)
                                                                            @if ($queryHora->idhora === $hora->idhora && $queryHora->iddias === $dia->iddias)
                                                                                {{ $queryHora->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-2 p-2" style="background: linear-gradient(-50deg, #9c5c02, #f07634); color: #ffffff; text-align: center; font-weight: bold;">RECESO</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />

    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    @livewireScripts
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
    <script>
        $(document).ready(function() {
            $(".mause").hover(
                function() {
                    $(this).css("color", "#ba9643");
                },
                function() {
                    $(this).css("color", "#4a3911");
                }
            );
        });
    </script>
@stop
