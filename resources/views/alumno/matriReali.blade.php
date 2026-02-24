@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @can('alumno.matriReali.index')
        <div class="callout callout-danger mb-1">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-check-circle" aria-hidden="true"></i> - Matrícula Realizadas</h1>
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
    @can('alumno.matriReali.index')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item"><a style="color: #097daa; text-decoration: none;"><i
                                    class="fas fa-info-circle"></i> Se vizualisan las matrículas mas recientes</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        @php
            $matri_reali = collect($matriRealiz)->groupBy('semestre_academico');
        @endphp
        @foreach ($matri_reali as $semestre_academic => $cursos_llevand)
            <div class="container mt-1">
                <div class="table-responsive">
                    <table class="dentro table custom-table table-sm" style="min-width: 700px; border-radius: 5px;">
                        <thead class="table">
                            <tr style="width: 80px; background: linear-gradient(135deg, #098101, #6ad45e); color: white;">
                                <th class="text-center" colspan="7">{{ $semestre_academic }}</th>
                            </tr>
                            <tr style="width: 80px; background: linear-gradient(135deg, #006192, #5eccd4); color: white;">
                                <th class="text-center" style="width: 50px;">Malla Curri.</th>
                                <th class="text-center" style="width: 50px;">Ciclo</th>
                                <th class="text-center" style="width: 50px;">Código</th>
                                <th style="width: 400px;">Curso</th>
                                <th class="text-center" style="width: 50px;">Créditos</th>
                                <th class="text-center" style="width: 50px;">Nota</th>
                                <th class="text-center" style="width: 50px;">Turno</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cursos_llevand as $cursos)
                                <tr>
                                    <td class="text-center" style="width: 50px;">
                                        {{ $cursos->malla_curricular_idmalla_curricular }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $cursos->nombre_ciclo }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $cursos->idcursos }}</td>
                                    <td style="width: 400px;">{{ $cursos->nombre_curso }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $cursos->credito }}</td>
                                    <td class="text-center" style="width: 50px;">{{ $cursos->nota_final }}</td>
                                    <td class="text-center" style="width: 50px;">
                                        @if ($cursos->id_turno == 1)
                                            <span>M</span>
                                        @elseif ($cursos->id_turno == 2)
                                            <span>T</span>
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
    @livewireStyles
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
    </style>
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
