@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @can('alumno.matriPorCurri.index')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-book" aria-hidden="true"></i> - Matrícula por Currícula</h1>
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
    @endcan
@stop

@section('content')
    @can('alumno.matriPorCurri.index')
        @php
            $malla_cursos_notas = collect($malla_notas)->groupBy('nombre_ciclo');
        @endphp
        @foreach ($malla_cursos_notas as $ciclo => $malla_cursos_nota)
            <div class="container mt-1">
                <div class="table-responsive">
                    <table class="dentro table custom-table table-sm" style="min-width: 650px; border-radius: 5px;">
                        <thead class="table">
                            <tr style="width: 80px; background: linear-gradient(135deg, #006e92, #5ed4d4); color: white;">
                                <th class="text-center" colspan="6">Ciclo: {{ $ciclo }}</th>
                            </tr>
                            <tr style="width: 80px; background: linear-gradient(135deg, #920081, #d45ec6); color: white;">
                                <th class="text-center" style="width: 50px;">Código</th>
                                <th style="width: 400px;">Cursos</th>
                                <th class="text-center" style="width: 50px;">Créditos</th>
                                <th class="text-center" style="width: 50px;">Tipo</th>
                                <th class="text-center" style="width: 50px;">Nota</th>
                                <th class="text-center" style="width: 50px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($malla_cursos_nota as $malla_cn)
                                <tr>
                                    <td class="text-center" style="width: 50px;">{{ $malla_cn->idcursos }}</td>
                                    <td style="width: 400px;">{{ $malla_cn->nombre_curso }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $malla_cn->credito }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $malla_cn->nombre_tipo_curso }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $malla_cn->total }}</td>
                                    <td class="text-center" style="width: 50px;">
                                        @if ($malla_cn->estado_nota == 1)
                                            <div class="m-0 p-0  circle green"></div>
                                        @elseif ($malla_cn->estado_nota == 2)
                                            <div class="m-0 p-0 circle yellow"></div>
                                        @elseif ($malla_cn->estado_nota == 0)
                                            <div class="m-0 p-0 circle red"></div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <style>
        .custom-table thead th {
            border: none !important;
        }

        .custom-table thead th {
            border-bottom: 2px solid #343a40;
        }

        .custom-table tbody td {
            border: none;
            border-bottom: 1px solid #dee2e6;
        }

        .custom-table tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.2s ease-in-out;
        }

        .custom-table {
            border-collapse: collapse;
        }

        .dentro {
            border: 1px solid #0883ac;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
        }

        .circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            /* Hace que el div sea un círculo */
            display: inline-block;
            /* Para que se alineen horizontalmente */
            margin: 10px;
        }

        .red {
            background: linear-gradient(135deg, #b30303, #e98787); color: white;
        }

        .yellow {
            background: linear-gradient(135deg, #c5b90b, #ded588); color: white;
        }

        .green {
            background: linear-gradient(135deg, #00840f, #baffad); color: white;
        }
    </style>
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
