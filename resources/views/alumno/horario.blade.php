@extends('adminlte::page')

@section('title', 'Ver Horario')

@section('content_header')
    @can('alumno.matriActual.index')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1><i class="fas fa-calendar-alt"></i> - Ver horario(s)</h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('alumno.matriActual.index') }}">Matricula actual</a></li>
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
    @can('alumno.matriActual.index')
        <div class="container-fluid">
            <div class="row">
                {{-- <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item"><a
                                style="color: #392903; text-decoration: none;">{{ 'Periodo ' . $año . ' - ' . $periodo }}</a></li>
                        <li class="breadcrumb-item"><a
                                style="color: #392903; text-decoration: none;">{{ $nombre_de_carrera }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Malla:
                                {{ $año_de_inicio }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Ciclo:
                                {{ $nombre_ciclo }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">Turno:
                                {{ $nombre_turno }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;">{{ $codigo_aula }} -
                                {{ $aula_nombre }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #392903; text-decoration: none;"></a>Seccion:
                            {{ $nom_seccion }}</li>

                    </ol>
                </div> --}}
            </div>
        </div>
        {{-- @dump($queryHorari) --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    @if ($contMañaRe > 0)
                        <div class="card card-info shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO MAÑANA - CICLO
                                    {{ $datosR[0]->nombre_ciclo }} - REGULAR</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="container-fluid mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sm"
                                            style="min-width: 750px;">
                                            <thead class="text-center"
                                                style="background: linear-gradient(135deg, #5d3106, #b16816); color: white; font-size: 0.85rem;">
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 50%;">Cursos</th>
                                                    <th style="width: 15%;">Cant. Hora</th>
                                                    <th style="width: 30%;">Docente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($queryRegu as $index => $cursoDoce)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $cursoDoce->nombre_curso }}</td>
                                                        <td class="text-center">{{ $cursoDoce->horas }}</td>
                                                        <td>
                                                            @if (!is_null($cursoDoce->nombre))
                                                                <span class="badge bg-success">{{ $cursoDoce->nombre }}</span>
                                                            @else
                                                                <span class="badge bg-danger">Curso sin docente asignado.</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-center table-sm" style="min-width: 900px;">
                                        <thead class="thead"
                                            style="background: linear-gradient(-50deg, #024f9c, #6aadcf); color: #ffffff; text-align: center; font-weight: bold; font-size: 0.85rem;">
                                            <tr>
                                                <th style="width: 120px;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 120px;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 1)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center text-wrap">
                                                                        @foreach ($queryHorariR as $queryHoraR)
                                                                            @if ($queryHoraR->idhora === $hora->idhora && $queryHoraR->iddias === $dia->iddias)
                                                                                {{ $queryHoraR->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-1 p-1 d-block"
                                                                        style="background: linear-gradient(-50deg, #9c4702, #cf8d6a); color: #ffffff; font-weight: bold;">RECESO</span>
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

                    @if ($contTardRe > 0)
                        <div class="card card-warning shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO TARDE - CICLO
                                    {{ $datosR[0]->nombre_ciclo }} - REGULAR</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="container-fluid mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sm"
                                            style="min-width: 750px;">
                                            <thead class="text-center"
                                                style="background: linear-gradient(135deg, #5d3106, #b16816); color: white; font-size: 0.85rem;">
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 50%;">Cursos</th>
                                                    <th style="width: 15%;">Cant. Hora</th>
                                                    <th style="width: 30%;">Docente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($queryRegu as $index => $cursoDoce)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $cursoDoce->nombre_curso }}</td>
                                                        <td class="text-center">{{ $cursoDoce->horas }}</td>
                                                        <td>
                                                            @if (!is_null($cursoDoce->nombre))
                                                                <span class="badge bg-success">{{ $cursoDoce->nombre }}</span>
                                                            @else
                                                                <span class="badge bg-danger">Curso sin docente asignado.</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-center table-sm" style="min-width: 900px;">
                                        <thead class="thead"
                                            style="background: linear-gradient(-50deg, #7a4903, #de7701); color: #ffffff; text-align: center; font-weight: bold; font-size: 0.85rem;">
                                            <tr>
                                                <th style="width: 120px;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 120px;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 2)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center text-wrap">
                                                                        @foreach ($queryHorariR as $queryHoraRR)
                                                                            @if ($queryHoraRR->idhora === $hora->idhora && $queryHoraRR->iddias === $dia->iddias)
                                                                                {{ $queryHoraRR->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-1 p-1 d-block"
                                                                        style="background: linear-gradient(-50deg, #9c5c02, #f07634); color: #ffffff; font-weight: bold;">RECESO</span>
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

                    @if ($contMañasub > 0)
                        <div class="card card-info shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO MAÑANA - CICLO
                                    {{ $datosS[0]->nombre_ciclo }} - SUBSANACION</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="container-fluid mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sm"
                                            style="min-width: 750px;">
                                            <thead class="text-center"
                                                style="background: linear-gradient(135deg, #5d3106, #b16816); color: white; font-size: 0.85rem;">
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 50%;">Cursos</th>
                                                    <th style="width: 15%;">Cant. Hora</th>
                                                    <th style="width: 30%;">Docente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($querySubsa as $index => $cursoDoce)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $cursoDoce->nombre_curso }}</td>
                                                        <td class="text-center">{{ $cursoDoce->horas }}</td>
                                                        <td>
                                                            @if (!is_null($cursoDoce->nombre))
                                                                <span class="badge bg-success">{{ $cursoDoce->nombre }}</span>
                                                            @else
                                                                <span class="badge bg-danger">Curso sin docente
                                                                    asignado.</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-center table-sm" style="min-width: 900px;">
                                        <thead class="thead"
                                            style="background: linear-gradient(-50deg, #024f9c, #6aadcf); color: #ffffff; text-align: center; font-weight: bold; font-size: 0.85rem;">
                                            <tr>
                                                <th style="width: 120px;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 120px;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 1)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center text-wrap">
                                                                        @foreach ($queryHorariS as $queryHoraR)
                                                                            @if ($queryHoraR->idhora === $hora->idhora && $queryHoraR->iddias === $dia->iddias)
                                                                                {{ $queryHoraR->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-1 p-1 d-block"
                                                                        style="background: linear-gradient(-50deg, #9c4702, #cf8d6a); color: #ffffff; font-weight: bold;">RECESO</span>
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

                    @if ($contTardsub > 0)
                        <div class="card card-warning shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-week"></i> HORARIO TURNO TARDE - CICLO
                                    {{ $datosS[0]->nombre_ciclo }} - SUBSANACION</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                                <div class="container-fluid mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sm"
                                            style="min-width: 750px;">
                                            <thead class="text-center"
                                                style="background: linear-gradient(135deg, #5d3106, #b16816); color: white; font-size: 0.85rem;">
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 50%;">Cursos</th>
                                                    <th style="width: 15%;">Cant. Hora</th>
                                                    <th style="width: 30%;">Docente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($querySubsa as $index => $cursoDoce)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $cursoDoce->nombre_curso }}</td>
                                                        <td class="text-center">{{ $cursoDoce->horas }}</td>
                                                        <td>
                                                            @if (!is_null($cursoDoce->nombre))
                                                                <span class="badge bg-success">{{ $cursoDoce->nombre }}</span>
                                                            @else
                                                                <span class="badge bg-danger">Curso sin docente
                                                                    asignado.</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-center table-sm" style="min-width: 900px;">
                                        <thead class="thead"
                                            style="background: linear-gradient(-50deg, #7a4903, #de7701); color: #ffffff; text-align: center; font-weight: bold; font-size: 0.85rem;">
                                            <tr>
                                                <th style="width: 120px;">Hora</th>
                                                @foreach ($dias as $dia)
                                                    <th style="width: 120px;">{{ $dia->nom_dia }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($horas as $hora)
                                                @if ($hora->idturno === 2)
                                                    <tr style="font-size: 0.8rem;">
                                                        <td class="font-weight-bold">{{ $hora->nom_hora }}</td>
                                                        @foreach ($dias as $dia)
                                                            <td>
                                                                @if ($hora->idhora != 5 && $hora->idhora != 14)
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center text-wrap">
                                                                        @foreach ($queryHorariS as $queryHoraRR)
                                                                            @if ($queryHoraRR->idhora === $hora->idhora && $queryHoraRR->iddias === $dia->iddias)
                                                                                {{ $queryHoraRR->nombre_curso }}
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="m-1 p-1 d-block"
                                                                        style="background: linear-gradient(-50deg, #9c5c02, #f07634); color: #ffffff; font-weight: bold;">RECESO</span>
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
