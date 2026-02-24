@extends('adminlte::page')

@section('title', 'Matricula Actual')

@section('content_header')
    @can('alumno.matriActual.index')
        <div class="callout callout-danger mb-1">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-calendar-check" aria-hidden="true"></i> - Matrícula actual</h1>
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
    @can('alumno.matriActual.index')
        @if (!empty($cursosactu))
            @php
                $totCursos = count($cursosactu);
                $año = collect($cursosactu)->pluck('año')->unique();
                $peri = collect($cursosactu)->pluck('periodo')->unique();
                $cursos = collect($cursosactu)->groupBy('nombre_tipo_matricula');
            @endphp
            <div class="container-fluid mb-2">
                <div class="row d-flex justify-content-between flex-wrap">
                    <!-- Información de cursos y semestre -->
                    <div class="col-12 col-md-6 mb-2">
                        <ol class="breadcrumb m-0 d-flex flex-wrap flex-md-nowrap align-items-center small-responsive">
                            <li class="breadcrumb-item mb-1 mb-md-0">
                                <a style="color: #512b05; text-decoration: none;">
                                    <i class="fas fa-book"></i> Estas llevando {{ $totCursos }} cursos
                                </a>
                            </li>
                            <li class="breadcrumb-item mb-1 mb-md-0">
                                <a style="color: #604609; text-decoration: none;">
                                    <i class="fas fa-university"></i> Semestre académico: {{ $año[0] }} -
                                    {{ $peri[0] }}
                                </a>
                            </li>
                        </ol>
                    </div>
                    <style>
                        @media (max-width: 576px) {
                            .small-responsive {
                                font-size: 0.70rem;
                                white-space: nowrap;
                                overflow-x: auto;
                                -webkit-overflow-scrolling: touch;
                            }
                        }
                    </style>

                    <!-- Botón Ver horario -->
                    <div class="col-sm-2 col-md-2 text-md-right">
                        <a href="{{ route('alumno.horarioAlumno.index') }}" class="btn btn-success btn btn-block">
                            <i class="fas fa-calendar-alt"></i> Ver horario(s)
                        </a>
                    </div>
                </div>

            </div>

            @foreach ($cursos as $tipo_matricula => $curso)
                <div class="card">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #924900, #d49d5e);">
                        <h3 class="card-title"><i class="fas fa-folder-open" aria-hidden="true"></i> Matrícula
                            {{ $tipo_matricula }}
                        </h3>
                    </div>
                    <form class="form-horizontal">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped" style="min-width: 1600px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width: 10px;">#</th>
                                            <th class="text-center" style="width: 300px;">Nombre curso</th>
                                            <th class="text-center"
                                                style="width: 80px; background: linear-gradient(135deg, #924900, #d49d5e); color: white;">
                                                Nota 1</th>
                                            <th class="text-center"
                                                style="width: 100px; background: linear-gradient(135deg, #924900, #d49d5e); color: white;">
                                                Recomendación 1</th>
                                            <th class="text-center"
                                                style="width: 80px; background: linear-gradient(135deg, #00779c, #5ed0d4); color: white;">
                                                Nota 2</th>
                                            <th class="text-center"
                                                style="width: 100px; background: linear-gradient(135deg, #00779c, #5ed0d4); color: white;">
                                                Recomendación 2</th>
                                            <th class="text-center"
                                                style="width: 80px; background: linear-gradient(135deg, #168400, #5ed466); color: white;">
                                                Nota 3</th>
                                            <th class="text-center"
                                                style="width: 100px; background: linear-gradient(135deg, #168400, #5ed466); color: white;">
                                                Recomendación 3</th>
                                            <th class="text-center" style="width: 80px;">Nota Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($curso as $cur)
                                            <tr>
                                                <td class="text-center">#</td>
                                                <td><i class="fas fa-book"></i> - {{ $cur->nombre_curso }}</td>
                                                <td class="text-center" style="font-size: 12px;">
                                                    @foreach ($califi as $calificacion)
                                                        @if ($calificacion->idCalificaciones == $cur->idCalificaciones1)
                                                            @if ($calificacion->nom_califi === 'ninguno')
                                                                <span>--</span>
                                                            @else
                                                                {{ $calificacion->nom_califi }}
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="font-size: 12px;">{{ $cur->recomendacion_nota1 }}</td>
                                                <td class="text-center" style="font-size: 12px;">
                                                    @foreach ($califi as $calificacion)
                                                        @if ($calificacion->idCalificaciones == $cur->idCalificaciones2)
                                                            @if ($calificacion->nom_califi === 'ninguno')
                                                                <span>--</span>
                                                            @else
                                                                {{ $calificacion->nom_califi }}
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="font-size: 12px;">{{ $cur->recomendacion_nota2 }}</td>
                                                <td class="text-center" style="font-size: 12px;">
                                                    @foreach ($califi as $calificacion)
                                                        @if ($calificacion->idCalificaciones == $cur->idCalificaciones3)
                                                            @if ($calificacion->nom_califi === 'ninguno')
                                                                <span>--</span>
                                                            @else
                                                                {{ $calificacion->nom_califi }}
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="font-size: 12px;">{{ $cur->recomendacion_nota3 }}</td>
                                                @php
                                                    $nulls = [
                                                        $cur->idcompetencia1,
                                                        $cur->idcompetencia2,
                                                        $cur->idcompetencia3,
                                                    ];
                                                    $cantidadNulls = count(
                                                        array_filter($nulls, function ($item) {
                                                            return is_null($item);
                                                        }),
                                                    );
                                                @endphp
                                                @if ($cantidadNulls == 2)
                                                    @if ($cur->idCalificaciones1 !== 0)
                                                        @if ($cur->total > 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #1d6901, #88cd83);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @elseif ($cur->total <= 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #c71919, #dd1515);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td>
                                                            <span>{{ $cur->total }}</span>
                                                        </td>
                                                    @endif
                                                @endif

                                                @if ($cantidadNulls == 1)
                                                    @if ($cur->idCalificaciones1 !== 0 && $cur->idCalificaciones2 !== 0)
                                                        @if ($cur->total > 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #1d6901, #88cd83);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @elseif ($cur->total <= 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #c71919, #dd1515);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td>
                                                            <span>{{ $cur->total }}</span>
                                                        </td>
                                                    @endif
                                                @endif

                                                @if ($cantidadNulls == 0)
                                                    @if ($cur->idCalificaciones1 !== 0 && $cur->idCalificaciones2 !== 0 && $cur->idCalificaciones3 !== 0)
                                                        @if ($cur->total > 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #1d6901, #88cd83);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @elseif ($cur->total <= 10)
                                                            <td class="text-center text-white"
                                                                style="background: linear-gradient(135deg, #c71919, #dd1515);">
                                                                {{ $cur->total }}
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td>
                                                            <span>{{ $cur->total }}</span>
                                                        </td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        @else
            <div class="alert alert-info alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Alerta !!!</h5>
                Aun no tienes matriculas actuales.
            </div>
        @endif
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
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
