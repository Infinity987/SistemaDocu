@extends('adminlte::page')

@section('title', 'Tomar asistencia')

@section('content_header')
    @can('docente.calificaciones')
        <div class="callout callout-danger mb-0 estiTitulo">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1 style="font-size: 28px;" class="titulo-asistencia">
                                <i class="fas fa-pen"></i> <i class="fas fa-chalkboard-teacher"></i> - ASISTENCIA DE ESTUDIANTES
                            </h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <form action="{{ route('docente.verAlumnos') }}" method="get">
                                        @csrf
                                        <input type="hidden" name="iddocente_curso" value="{{ $iddocente_curso }}">
                                        <input type="hidden" name="nombre_de_carrera" value="{{ $nombre_de_carrera }}">
                                        <input type="hidden" name="nombre_curso" value="{{ $nombre_curso }}">
                                        <input type="hidden" name="idciclos" value="{{ $idciclos }}">
                                        <input type="hidden" name="nombre_ciclo" value="{{ $nombre_ciclo }}">
                                        <input type="hidden" name="año" value="{{ $año }}">
                                        <input type="hidden" name="periodo" value="{{ $periodo }}">

                                        <input type="hidden" name="año_de_inicio" value="{{ $año_de_inicio }}">
                                        <input type="hidden" name="nom_seccion" value="{{ $nom_seccion }}">
                                        <input type="hidden" name="idcursos" value="{{ $idcursos }}">
                                        <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">

                                        <input type="hidden" name="idturno" value="{{ $idturno }}">

                                        <button type="submit" class="btn btn-link p-0"
                                            style="color: #4a3911; text-decoration: none;">
                                            <i class="fas fa-arrow-left"></i> Volver atrás
                                        </button>
                                    </form>
                                </li>
                                <li class="breadcrumb-item active">Estudiantes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('docente.calificaciones')
        {{-- @dump($listAlumnos) --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 lineas">
                    <ol class="breadcrumb float-sm-left shadow">
                        <li class="breadcrumb-item"><a
                                style="color: #604609; text-decoration: none;">{{ $año . ' - ' . $periodo }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                style="color: #604609; text-decoration: none;">{{ $nombre_de_carrera }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #604609; text-decoration: none;">Malla:
                                {{ $año_de_inicio }}</a></li>
                        <li class="breadcrumb-item"><a style="color: #604609; text-decoration: none;">Ciclo:
                                {{ $nombre_ciclo }}</a>
                        </li>
                        <li class="breadcrumb-item"><a style="color: #604609; text-decoration: none;">Sección:
                                {{ $nom_seccion }}</a></li>

                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-10 col-md-10">
                    <div class="mb-3">
                        <span class="curso-nombre titulo-asistencia" class="">
                            <i class="fas fa-book-open"></i> {{ $nombre_curso }}
                        </span>
                    </div>
                </div>

                @php
                    $datos = [
                        'fecha' => date('Y-m-d'),
                        'idcurso' => $idcursos,
                        'iddocente_curso' => $iddocente_curso,
                        'nombre_de_carrera' => $nombre_de_carrera,
                        'nombre_curso' => $nombre_curso,
                        'idciclos' => $idciclos,
                        'nombre_ciclo' => $nombre_ciclo,
                        'año' => $año,
                        'periodo' => $periodo,
                        'año_de_inicio' => $año_de_inicio,
                        'nom_seccion' => $nom_seccion,
                        'tipodocente_curso' => $tipodocente_curso,
                        'idturno' => $idturno
                    ];
                @endphp

                <div class="col-xs-12 col-sm-2 col-md-2">
                    {{-- <a href="{{ route('docente.totalAsist', $datos) }}" class="btn btn-warning btn-block shadow">
                        <i class="fas fa-user-check"></i> Asistencia general
                    </a> --}}
                    <button type="button" class="btn btn-warning btn-block shadow" data-toggle="modal"
                        data-target="#modalAsistencia">
                        <i class="fas fa-user-check"></i> Reporte Asistencia
                    </button>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="modalAsistencia" tabindex="-1" role="dialog"
                    aria-labelledby="modalAsistenciaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content border-0 shadow-lg">
                            <form action="{{ route('docente.totalAsist') }}" method="GET">
                                <div class="modal-header bg-gradient-info text-white">
                                    <div>
                                        <h5 class="modal-title font-weight-bold" id="modalAsistenciaLabel">
                                            <i class="fas fa-calendar-check mr-2"></i> Generar reporte de asistencia
                                        </h5>
                                        <p class="mb-0 text-light">Seleccione un rango dentro del semestre para generar su
                                            reporte personalizado.</p>
                                    </div>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        {{-- Campos ocultos --}}
                                        @foreach ($datos as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <div class="col-12 mb-3">
                                            <div class="callout callout-info text-center">
                                                <strong><i class="fas fa-info-circle mr-1"></i> Intervalo de fechas del
                                                    SEMESTRE ACADEMICO:</strong>
                                                del <span
                                                    class="text-primary font-weight-bold">{{ \Carbon\Carbon::parse($inicio)->format('d-m-Y') }}</span>
                                                al <span
                                                    class="text-primary font-weight-bold">{{ \Carbon\Carbon::parse($fin)->format('d-m-Y') }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_inicio_inter">
                                                    <i class="fas fa-calendar-day mr-1 text-info"></i> Fecha inicio
                                                </label>
                                                <input type="date" name="fecha_inicio_inter" id="fecha_inicio_inter"
                                                    class="form-control border-info" min="{{ $inicio }}"
                                                    max="{{ $fin }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_fin_inter">
                                                    <i class="fas fa-calendar-day mr-1 text-info"></i> Fecha fin
                                                </label>
                                                <input type="date" name="fecha_fin_inter" id="fecha_fin_inter"
                                                    class="form-control border-info" min="{{ $inicio }}"
                                                    max="{{ $fin }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-alt mr-1"></i> ver reporte
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @dump($listAlumnos) --}}
        <div class="card-body table-responsive p-2 custom-scroll" style="overflow-x: auto;">
            <div class="box box-primary">

                <div class="col-12 col-sm-12">
                    <div class="card card-success card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                                        href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                                        aria-selected="false">Asistencia</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                                        href="#custom-tabs-four-profile" role="tab"
                                        aria-controls="custom-tabs-four-profile" aria-selected="true">Porcentaje de
                                        inasistencia</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            {{-- @dump($fecha) --}}
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade active show" id="custom-tabs-four-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-home-tab">
                                    {{-- leyenda, marcar todo P y btn eliminar --}}
                                    <div class="container mb-2">
                                        <div class="row">

                                            <div class="col-xs-6 col-sm-6 col-md-8">
                                                <div id="contadorAsistencia" style="text-align: center;">
                                                    <span style="display: inline-block; margin-right: 50px;">
                                                        <strong>Leyenda:</strong>
                                                    </span>
                                                    <span style="display: inline-block; margin-right: 50px;">
                                                        <i class="fas fa-user-check" style="color: #28a745;"></i>
                                                        <strong>1 – Presente</strong>
                                                    </span>
                                                    <span style="display: inline-block; margin-right: 50px;">
                                                        <i class="fas fa-clock" style="color: #ffc107;"></i>
                                                        <strong>2 – Tarde</strong>
                                                    </span>
                                                    <span style="display: inline-block; margin-right: 50px;">
                                                        <i class="fas fa-user-times" style="color: #dc3545;"></i>
                                                        <strong>3 – Falto</strong>
                                                    </span>
                                                    <span style="display: inline-block;">
                                                        <i class="fas fa-book" style="color: #007bff;"></i>
                                                        <strong>4 – Justificado</strong>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-xs-6 col-sm-6 col-md-2">
                                                <button type="button" class="btn btn-success btn-block btn-sm mb-2 shadow"
                                                    id="btnMarcarTodoPresente">
                                                    <i class="fas fa-check-circle mr-1"></i> Marcar todo P
                                                </button>
                                            </div>

                                            <div class="col-xs-6 col-sm-6 col-md-2">
                                                <button type="button" class="btn btn-danger btn-block btn-sm mb-2 shadow"
                                                    data-toggle="modal" data-target="#modalEliminarHorario">
                                                    <i class="fas fa-trash-alt"></i> Eliminar asistencia
                                                </button>
                                                <div class="modal fade" id="modalEliminarHorario" tabindex="-1"
                                                    role="dialog" aria-labelledby="modalEliminarHorarioLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-md" role="document">
                                                        <div class="modal-content border-0 shadow">
                                                            <form id="eliminarAsis">
                                                                @csrf
                                                                <div class="modal-header bg-danger text-white">
                                                                    <div>
                                                                        <h5 class="modal-title"
                                                                            id="modalEliminarHorarioLabel">
                                                                            <i class="fas fa-calendar-times mr-2"></i>
                                                                            Confirmar
                                                                            eliminación de
                                                                            ASISTENCIA:
                                                                        </h5>
                                                                    </div>
                                                                    <button type="button" class="close text-white"
                                                                        data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <p class="text-muted">Se eliminará la asistencia del día
                                                                        <strong>{{ $fecha }}</strong> para todos los
                                                                        alumnos
                                                                        listados. ¿Desea continuar?
                                                                    </p>
                                                                    <input type="hidden" name="fecha"
                                                                        value="{{ $fecha }}">
                                                                    <input type="hidden" name="iddocente_curso"
                                                                        value="{{ $iddocente_curso }}">
                                                                    <input type="hidden" name="idciclos"
                                                                        value="{{ $idciclos }}">
                                                                    <input type="hidden" name="tipodocente_curso"
                                                                        value="{{ $tipodocente_curso }}">
                                                                </div>

                                                                <div class="modal-footer bg-light justify-content-center">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">
                                                                        <i class="fas fa-times mr-1"></i> Cancelar
                                                                    </button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash-alt mr-1"></i> Eliminar horario
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- asistencia --}}
                                    <form id="asistencia">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="row">
                                                {{-- fecha --}}
                                                <div class="col-sm-6">
                                                    <div class="callout callout-success shadow"
                                                        style="text-align: center; padding: 10px 15px;">

                                                        <div id="fechaEditable" style="display: inline-block;">
                                                            <!-- Fecha -->
                                                            <span style="display: inline-block; margin-right: 15px;">
                                                                <label for="fecha" class="control-label"
                                                                    style="margin-right: 5px;">
                                                                    <i class="fa fa-calendar"></i> Fecha:
                                                                </label>
                                                                <input type="date" name="fecha" id="fecha"
                                                                    class="form-control" value="{{ $fecha }}" readonly
                                                                    style="display: inline-block; width: auto; vertical-align: middle;">
                                                            </span>

                                                            <!-- Checkbox -->
                                                            <span style="display: inline-block;" class="mt-2">
                                                                <label class="btn btn-sm btn-default"
                                                                    style="margin-bottom: 0; width: 100%;">
                                                                    <input type="checkbox" id="editarFecha"
                                                                        autocomplete="off">
                                                                    <i class="fa fa-pencil"></i> Editar fecha
                                                                </label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- conteo --}}
                                                <div class="col-sm-6">
                                                    <div class="callout callout-info shadow" style="text-align: center;">
                                                        <div id="contadorAsistencia">
                                                            <span style="display: inline-block; margin-right: 15px;">
                                                                <i class="fas fa-user-check" style="color: #28a745;"></i>
                                                                Presentes: <span id="countP">0</span>
                                                            </span>
                                                            <span style="display: inline-block; margin-right: 15px;">
                                                                <i class="fas fa-clock" style="color: #ffc107;"></i>
                                                                Tardanzas: <span id="countT">0</span>
                                                            </span>
                                                            <span style="display: inline-block; margin-right: 15px;">
                                                                <i class="fas fa-user-times" style="color: #dc3545;"></i>
                                                                Faltas: <span id="countF">0</span>
                                                            </span>
                                                            <span style="display: inline-block;">
                                                                <i class="fas fa-book" style="color: #007bff;"></i>
                                                                Justificados: <span id="countJ">0</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- tabla asisten --}}
                                        <div class="container mb-2">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <!-- Tabla de alumnos -->
                                                    <div style="max-height: 500px; overflow-y: auto;">
                                                        <table id="tablaAsistencia"
                                                            class="table table-bordered table-striped table-hover"
                                                            style="margin-bottom: 0;">
                                                            <thead
                                                                style="background: linear-gradient(360deg, #de9857, #854a02); color: white; position: sticky; top: 0; z-index: 2;">
                                                                <tr>
                                                                    <th style="width: 20px; text-align:center;">#</th>
                                                                    <th style="width: 300px;">
                                                                        Alumno &nbsp;
                                                                    </th>

                                                                    <th style="width: 200px; text-align:center;">Asistencia
                                                                    </th>
                                                                    <th style="width: 200px; text-align:center;">Observación
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($listAlumnos as $index => $alumno)
                                                                    <tr>
                                                                        <td
                                                                            style="background-color: #d4ddea; text-align:center;">
                                                                            {{ $index + 1 }}
                                                                        </td>
                                                                        <td style="">
                                                                            {{ $alumno->apellidos_pater_postulante }}
                                                                            {{ $alumno->apellidos_mater_postulante }},
                                                                            {{ $alumno->nombres_postulante }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                class="form-control asistencia-input"
                                                                                maxlength="1"
                                                                                data-id="{{ $alumno->idincripcion_curso }}"
                                                                                style="width: 100%; font-size: 0.85rem; padding: 2px; text-align: center;" />

                                                                            <select
                                                                                name="asistencia[{{ $alumno->idincripcion_curso }}]"
                                                                                class="d-none asistencia-select">
                                                                                <option value="">Seleccione</option>
                                                                                <option value="P"
                                                                                    {{ ($alumno->estado_raw ?? '') == 'P' ? 'selected' : '' }}>
                                                                                    P
                                                                                </option>
                                                                                <option value="T"
                                                                                    {{ ($alumno->estado_raw ?? '') == 'T' ? 'selected' : '' }}>
                                                                                    T
                                                                                </option>
                                                                                <option value="F"
                                                                                    {{ ($alumno->estado_raw ?? '') == 'F' ? 'selected' : '' }}>
                                                                                    F
                                                                                </option>
                                                                                <option value="J"
                                                                                    {{ ($alumno->estado_raw ?? '') == 'J' ? 'selected' : '' }}>
                                                                                    J
                                                                                </option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <textarea class="form-control" name="observacion[{{ $alumno->idincripcion_curso }}]" id="observacion"
                                                                                rows="1" placeholder="Escribe aquí...">{{ $alumno->observacion ?? '' }}</textarea>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Botón guardar -->
                                        <div class="box-footer text-center mb-4">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-save"></i> Guardar Asistencia
                                            </button>
                                            {{-- @foreach ($listAlumnos as $listAlumn)
                                                @dump($listAlumn)
                                            @endforeach --}}

                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-profile-tab">

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm text-center align-middle">
                                            <thead class="bg-gradient-info text-white">
                                                <tr>
                                                    <th style="min-width: 150px;">Alumno</th>
                                                    <th style="min-width: 100px;">Asistencias</th>
                                                    <th style="min-width: 100px;">Faltas</th>
                                                    <th style="min-width: 130px;">Sesiones Totales</th>
                                                    <th style="min-width: 120px;">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($alumnos as $a)
                                                    <tr>
                                                        <td class="text-left">{{ $a['nombre'] }}</td>
                                                        <td>{{ $a['asistencias'] }}</td>
                                                        <td>{{ $a['faltas'] }}</td>
                                                        <td>{{ count($a['sesiones_registradas']) }}</td>
                                                        <td>
                                                            <span class="badge px-3 py-2 text-white"
                                                                style="background-color: {{ $a['color'] }};">
                                                                {{ $a['mensaje'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

            </div>
        </div>
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/nombreCurso.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnPresente = document.getElementById('btnMarcarTodoPresente');

            btnPresente.addEventListener('click', function() {
                // Usando jQuery para compatibilidad con tu función
                $('.asistencia-input').each(function() {
                    aplicarColorYLetra($(this), 1); // 1 = Presente
                });
                actualizarContador();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#modalAsistencia form');
            const fechaInicio = document.getElementById('fecha_inicio_inter');
            const fechaFin = document.getElementById('fecha_fin_inter');

            // Límites del semestre (extraídos del backend)
            const minFecha = new Date("{{ \Carbon\Carbon::parse($inicio)->format('Y-m-d') }}");
            const maxFecha = new Date("{{ \Carbon\Carbon::parse($fin)->format('Y-m-d') }}");

            form.addEventListener('submit', function(e) {
                const inicioSeleccionado = new Date(fechaInicio.value);
                const finSeleccionado = new Date(fechaFin.value);

                // Validaciones
                if (
                    inicioSeleccionado < minFecha ||
                    finSeleccionado > maxFecha ||
                    inicioSeleccionado > finSeleccionado
                ) {
                    e.preventDefault(); // Bloquea el envío
                    Swal.fire({
                        icon: 'error',
                        title: 'Fechas inválidas',
                        text: 'Por favor selecciona un rango válido dentro del semestre académico.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    </script>

    <script>
        function estadoDesdeNumero(num) {
            return {
                1: {
                    valor: 'P',
                    clase: 'bg-success text-white'
                },
                2: {
                    valor: 'T',
                    clase: 'bg-warning text-dark'
                },
                3: {
                    valor: 'F',
                    clase: 'bg-danger text-white'
                },
                4: {
                    valor: 'J',
                    clase: 'bg-info text-white'
                }
            } [num] || {
                valor: '',
                clase: ''
            };
        }

        function aplicarColorYLetra(input, num) {
            const {
                valor,
                clase
            } = estadoDesdeNumero(num);

            // Limpia clases anteriores
            input.removeClass('bg-success bg-warning bg-danger bg-info text-white text-dark');

            if (valor) {
                input.val(valor); // muestra la letra en el input
                input.addClass(clase);

                // Actualiza el select oculto
                const select = input.closest('td').find('.asistencia-select');
                select.val(valor);

                // Avanza automáticamente al siguiente input
                const siguiente = $('.asistencia-input').eq($('.asistencia-input').index(input) + 1);
                if (siguiente.length) siguiente.focus();
            } else {
                input.val(''); // limpia si el número no es válido
            }
        }

        function actualizarContador() {
            let countP = 0,
                countT = 0,
                countF = 0,
                countJ = 0;

            $('.asistencia-select').each(function() {
                const val = $(this).val();
                if (val === 'P') countP++;
                else if (val === 'T') countT++;
                else if (val === 'F') countF++;
                else if (val === 'J') countJ++;
            });

            $('#countP').text(countP);
            $('#countT').text(countT);
            $('#countF').text(countF);
            $('#countJ').text(countJ);
        }

        $(document).ready(function() {
            $('#tablaAsistencia').DataTable({
                paging: false, // Desactiva paginación si quieres ver todos los alumnos
                searching: true,
                ordering: false, // Puedes poner true si quieres ordenar por columnas
                info: false,
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna de manera ascendente",
                        sortDescending: ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
            ////////////////////////////////////////////
            // Inicializa los inputs con valor y color
            $('.asistencia-input').each(function() {
                const input = $(this);
                const select = input.closest('td').find('.asistencia-select');
                const estado = select.val();
                const num = {
                    'P': 1,
                    'T': 2,
                    'F': 3,
                    'J': 4
                } [estado] || '';
                if (num) aplicarColorYLetra(input, num);
            });

            actualizarContador();

            // Detecta número y transforma
            $('.asistencia-input').on('keydown', function(e) {
                const input = $(this);

                // Solo si es número del 1 al 4
                if (['1', '2', '3', '4'].includes(e.key)) {
                    e.preventDefault(); // evita que se escriba el número
                    const num = parseInt(e.key);
                    aplicarColorYLetra(input, num);

                    actualizarContador(); // ✅ actualiza el resumen
                } else {
                    // Si no es válido, limpia
                    setTimeout(() => input.val(''), 10);
                }
            });

            // actualizarContador();

            ////////////////////////////////////////////

            $('#asistencia').on('submit', function(e) {
                e.preventDefault();

                let todosSeleccionados = true;

                $(".asistencia-select").each(function() {
                    if ($(this).val() === "") {
                        todosSeleccionados = false;
                        return false; // corta el bucle apenas encuentre uno vacío
                    }
                });

                if (!todosSeleccionados) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debes Asignar todas las asistencias'
                    });
                    return;
                }

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('docente.guardarAsistencia') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message
                            }).then(() => {
                                location.reload();
                                // $('#tablaAsistencia').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                                // $('#tablaAsistencia').DataTable().ajax.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message
                        });
                    }
                });
            });

            $('#eliminarAsis').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('docente.eliminarAsis') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message
                            }).then(() => {
                                location.reload();
                                // $('#tablaAsistencia').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                                // $('#tablaAsistencia').DataTable().ajax.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message
                        });
                    }
                });
            });

            //fecha
            // Por defecto, el checkbox NO está marcado
            $('#editarFecha').prop('checked', false);

            // Evento cuando se hace click en el checkbox
            $('#editarFecha').change(function() {
                if ($(this).is(':checked')) {
                    // Permitir editar
                    $('#fecha').prop('readonly', false);
                } else {
                    // Volver a bloquear la fecha
                    $('#fecha').prop('readonly', true);
                }
            });
        });

        const routeAsistencia = {!! json_encode(
            route('docente.asistencia.index', [
                'fecha' => '__FECHA__',
                'idcursos' => $idcursos,
                'iddocente_curso' => $iddocente_curso,
                'nombre_de_carrera' => $nombre_de_carrera,
                'nombre_curso' => $nombre_curso,
                'idciclos' => $idciclos,
                'nombre_ciclo' => $nombre_ciclo,
                'año' => $año,
                'periodo' => $periodo,
                'año_de_inicio' => $año_de_inicio,
                'nom_seccion' => $nom_seccion,
                'tipodocente_curso' => $tipodocente_curso,
            ]),
        ) !!};
        $('#fecha').on('change', function() {
            const fecha = $(this).val();
            if (!fecha) return;
            const url = routeAsistencia.replace('__FECHA__', encodeURIComponent(fecha));
            window.location.href = url;
        });
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
