@extends('adminlte::page')

@section('title', 'Lista de estudiantes')

@section('content_header')
    @can('docente.calificaciones')
        <div class="callout callout-danger mb-0 estiTitulo">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1><i class="fas fa-users"></i> <i class="fas fa-chalkboard-teacher"></i> - LISTA DE ESTUDIANTES
                            </h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('docente.calificaciones') }}">Ciclos cursos</a></li>
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
        @dump($listAlumnos)
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
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

            <div class="row m-2">
                <div class="col-xs-12 col-sm-10 col-md-10">
                    <div class="mb-3">
                        <span class="curso-nombre">
                            <i class="fas fa-book-open"></i> {{ $nombre_curso }}
                        </span>
                    </div>
                </div>
                @php
                    $datos = [
                        'fecha' => date('Y-m-d'),
                        'idcursos' => $idcursos,
                        'iddocente_curso' => $iddocente_curso,
                        'nombre_de_carrera' => rawurlencode($nombre_de_carrera),
                        'nombre_curso' => rawurlencode($nombre_curso),
                        'idciclos' => $idciclos,
                        'nombre_ciclo' => rawurlencode($nombre_ciclo),
                        'año' => $año,
                        'periodo' => $periodo,
                        'año_de_inicio' => $año_de_inicio,
                        'nom_seccion' => $nom_seccion,
                        'tipodocente_curso' => $tipodocente_curso,
                        'idturno' => $idturno
                    ];
                @endphp
                {{-- @dump($idturno) --}}
                <div class="col-xs-12 col-sm-2 col-md-1 mb-1">
                    <a href="{{ route('docente.reporteNotas', ['iddocente_curso' => $iddocente_curso, 'idturno' => $idturno]) }}" class="btn btn-danger btn-block shadow" target="_blank">
                        <i class="fas fa-file-pdf"></i> Repo._Notas
                    </a>
                </div>
                <div class="col-xs-12 col-sm-2 col-md-1">
                    <a href="{{ route('docente.asistencia.index', $datos) }}" class="btn btn-warning btn-block shadow">
                        <i class="fas fa-user-check"></i> Asistencia
                    </a>
                </div>
            </div>
        </div>

        @php
            $cantcompe = $cantCompetenciaPorCurso[0] ? $cantCompetenciaPorCurso[0] : 0;
            $lise = $competencias;
        @endphp

        @if ($cantCompetenciaPorCurso[0] === 0)
            <div class="alert alert-warning alert-dismissible">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Alerta!</h5>
                Deberá solicitar al administrador del sistema que asigne las COMPETENCIAS del curso ...
            </div>
        @else
            @if ($ContCompeNullNota === 3)
                @php
                    $agrupado = collect($competencias)->groupBy('Nombre_dominio');
                @endphp
                <div class="card collapsed-card shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-layer-group"></i> COMPETENCIAS
                        </h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    @foreach ($agrupado as $dominio => $competencias)
                        @php
                            $dominioId = 'dominio_' . Str::slug($dominio, '_');
                        @endphp
                        <div class="card-body m-1 pt-2 pb-1">
                            <span class="bg-warning text-dark px-2 py-1 rounded"><i class="fas fa-sign-out-alt"></i>
                                {{ $dominio }}</span>
                            @foreach ($competencias as $item)
                                <div class="mb-2 mt-2">
                                    <h6 class="text-dark mb-1">
                                        <i class="fas fa-check-circle text-success"></i> {{ $item->competencia }}
                                    </h6>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-angle-right"></i> {{ $item->descripcion }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="alert alert-warning p-2 mb-3 rounded">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>ASIGNE LAS {{ $ContCompeNullNota }} COMPETENCIAS A ESTE CURSO</strong>
                </div>

                <div class="container">
                    <form id="competenciasAsignar">
                        @csrf
                        <input type="hidden" name="iddocente_curso" value="{{ $iddocente_curso }}">
                        <div class="row">
                            @for ($i = 0; $i < $cantcompe; $i++)
                                <div class="form-group col-md-4 col-sm-6 mb-3">
                                    <label class="fw-bold text-secondary">
                                        Seleccione: {{ $i + 1 }}
                                    </label>
                                    <select class="form-control form-control-sm competencia-select"
                                        name="compe{{ $i + 1 }}">
                                        <option value="">Seleccione...</option>
                                        @foreach ($lise as $competen)
                                            <option value="{{ $competen->idcompetencias }}">
                                                {{ $competen->competencia }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-success col-sm-3" type="submit">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                    </form>

                    <!-- Aquí irá el mensaje de error -->
                    <div id="error-message" class="text-danger fw-bold text-center mt-2" style="display:none;">
                        Debes seleccionar al menos una competencia
                    </div>
                </div>
            @else
                @php
                    $nom_competen = $verLasCompetenciasT[0];
                    $list = [$nom_competen->name1, $nom_competen->name2, $nom_competen->name3];
                    $idcompetencia1 = $nom_competen->idcompetencia1;
                    $idcompetencia2 = $nom_competen->idcompetencia2;
                    $idcompetencia3 = $nom_competen->idcompetencia3;
                @endphp
                {{-- @dump($nom_competen) --}}
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header bg-info" style="background: linear-gradient(135deg, #311e0b, #d49d5e);">
                                    <h3 class="card-title"><i class="fas fa-users"></i> Tabla lista de estudiantes
                                    </h3>
                                </div>
                                <div class="card-body table-responsive p-2 custom-scroll" style="overflow-x: auto;">
                                    <form id="formNotas">

                                        <input type="hidden" name="iddocente_curso" value="{{ $iddocente_curso }}">

                                        <input type="hidden" name="idcompetencia1" value="{{ $idcompetencia1 }}">
                                        <input type="hidden" name="idcompetencia2" value="{{ $idcompetencia2 }}">
                                        <input type="hidden" name="idcompetencia3" value="{{ $idcompetencia3 }}">

                                        <div style="max-height: 400px; overflow-y: auto;">
                                            <input type="hidden" name="ContCompeNullNota" value="{{ $ContCompeNullNota }}">
                                            <table class="table table-hover table-bordered w-100 text-center shadow"
                                                style="min-width: 1600px;">
                                                <thead style="position: sticky; top: 0; z-index: 10; background: white;">
                                                    <tr>
                                                        <!-- Columna fija a la izquierda -->
                                                        <th style=" background: white;"
                                                            class="p-1" rowspan="2">#</th>

                                                        <!-- Otra columna fija a la izquierda -->
                                                        <th style=" background: white;"
                                                            class="text-left p-1" rowspan="2">Apellidos y Nombres</th>

                                                        @for ($cp = 0; $cp < $cantcompe; $cp++)
                                                            <th style="border-radius: 5px; width: 10%; background: linear-gradient(135deg, #924900, #d49d5e); color: white;"
                                                                class="p-1" colspan="2">
                                                                {{ $list[$cp] }}
                                                            </th>
                                                        @endfor
                                                        {{-- <th style="border-radius: 5px; width: 10%; background: linear-gradient(135deg, #924900, #d49d5e); color: white;"
                                                            class="p-1" colspan="2">
                                                            {{ $verLasCompetenciasT->name1 }}
                                                        </th>
                                                        <th style="border-radius: 5px; width: 10%; background: linear-gradient(135deg, #00779c, #5ed0d4); color: white;"
                                                            class="p-1" colspan="2">
                                                            {{ $verLasCompetenciasT->name2 }}
                                                        </th>
                                                        <th style="border-radius: 5px; width: 10%; background: linear-gradient(135deg, #168400, #5ed466); color: white;"
                                                            class="p-1" colspan="2">
                                                            {{ $verLasCompetenciasT->name3 }}
                                                        </th> --}}

                                                        <th style="width: 6%; text-align: center; vertical-align: middle; background: linear-gradient(135deg, #ffffff, #e2e2e2);"
                                                            class="p-1 text-dark" id="one" rowspan="2">Total</th>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $n = 1;
                                                            $r = 1;
                                                        @endphp
                                                        @for ($cp = 0; $cp < $cantcompe; $cp++)
                                                            <th style="width: 10%;" class="p-1" id="one">Nota
                                                                {{ $n++ }}</th>
                                                            <th style="width: 13%;" class="p-1" id="one">
                                                                Recomendación {{ $r++ }}
                                                            </th>
                                                        @endfor
                                                        {{--
                                                        <th style="width: 10%;" class="p-1" id="one">Nota 2</th>
                                                        <th style="width: 13%;" class="p-1" id="one">
                                                            Recomendación 2
                                                        </th>
                                                        <th style="width: 10%;" class="p-1" id="one">Nota 3</th>
                                                        <th style="width: 13%;" class="p-1" id="one">
                                                            Recomendación 3
                                                        </th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @csrf
                                                    @foreach ($listAlumnos as $index => $alumno)
                                                        <input type="hidden" name="idincripcion_curso[]"
                                                            value="{{ $alumno->idincripcion_curso }}">
                                                        <tr>
                                                            <td style=" background-color: #d4ddea">{{ $index + 1 }}</td>
                                                            <td style=" background-color: #f0f4fa" class="text-left"> <span class="badge badge-{{ $alumno->idtipo_matricula == 1 ? 'success' : 'warning' }}">{{ $alumno->idtipo_matricula == 1 ? 'R' : 'S' }}</span> - {{ $alumno->apellidos_pater_postulante . ' ' . $alumno->apellidos_mater_postulante . ' ' . $alumno->nombres_postulante }}
                                                            </td>
                                                            @php
                                                                $nn = 1;
                                                                $idcali = [
                                                                    $alumno->idCalificaciones1,
                                                                    $alumno->idCalificaciones2,
                                                                    $alumno->idCalificaciones3,
                                                                ];
                                                                $recom = [
                                                                    $alumno->recomendacion_nota1,
                                                                    $alumno->recomendacion_nota2,
                                                                    $alumno->recomendacion_nota3,
                                                                ];
                                                            @endphp
                                                            @for ($cp = 0; $cp < $cantcompe; $cp++)
                                                                <td><select name="nota{{ $nn }}[]" id=""
                                                                        class="form-control form-control-sm mi-select">
                                                                        <option value="0">Seleccione</option>
                                                                        @foreach ($calificaciones as $calificacion)
                                                                            <option
                                                                                value="{{ $calificacion->idCalificaciones }}"
                                                                                {{ $calificacion->idCalificaciones == $idcali[$cp] ? 'selected' : '' }}>
                                                                                {{ $calificacion->nom_califi }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" name="reco{{ $nn }}[]"
                                                                        id="reco[]" class="form-control form-control-sm"
                                                                        value="{{ $recom[$cp] }}">
                                                                </td>
                                                                @php
                                                                    $nn++;
                                                                @endphp
                                                            @endfor


                                                            {{-- <td><select name="nota2[]" id=""
                                                                    class="form-control form-control-sm mi-select">
                                                                    <option value="0">Seleccione</option>
                                                                    @foreach ($calificaciones as $calificacion)
                                                                        <option value="{{ $calificacion->idCalificaciones }}"
                                                                            {{ $calificacion->idCalificaciones == $alumno->idCalificaciones2 ? 'selected' : '' }}>
                                                                            {{ $calificacion->nom_califi }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="reco2[]" id="reco[]"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $alumno->recomendacion_nota2 }}">
                                                            </td>

                                                            <td><select name="nota3[]" id=""
                                                                    class="form-control form-control-sm mi-select">
                                                                    <option value="0">Seleccione</option>
                                                                    @foreach ($calificaciones as $calificacion)
                                                                        <option value="{{ $calificacion->idCalificaciones }}"
                                                                            {{ $calificacion->idCalificaciones == $alumno->idCalificaciones3 ? 'selected' : '' }}>
                                                                            {{ $calificacion->nom_califi }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="reco3[]" id="reco[]"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $alumno->recomendacion_nota3 }}">
                                                            </td> --}}

                                                            <td>
                                                                @if ($alumno->total > 0 && $alumno->total < 11)
                                                                    <div class="form-group m-0">
                                                                        <span class="badge badge-danger p-2"
                                                                            style="font-size: 1rem; ">{{ $alumno->total }}</span>
                                                                    </div>
                                                                @elseif ($alumno->total > 10 && $alumno->total <= 20)
                                                                    <div class="form-group m-0">
                                                                        <span class="badge badge-success p-2"
                                                                            style="font-size: 1rem; ">{{ $alumno->total }}</span>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-success col-sm-2" type="submit"><i
                                                    class="fas fa-save"></i>
                                                Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif



    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/nombreCurso.css') }}">


    @livewireStyles
    <style>
        /* Solo aplica a .custom-scroll */
        .custom-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #006fa2;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #006fa2;
        }

        /* Para Firefox */
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: #006fa2 #f1f1f1;
        }

        #one {
            margin: 2rem auto;
            border-radius: 5px;
            background: linear-gradient(135deg, #0077df, #769dfa);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f7f7f7;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>

    <style>
        #to {
            margin: 2rem auto;
            border-radius: 5px;
            background: linear-gradient(135deg, #eef4fc, #c3cfe2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        #to:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
    </style>

    <style>
        .dominio {
            background-color: #007bff;
            color: white;
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
        }

        .competencia {
            margin-left: 15px;
            margin-top: 10px;
        }

        .descripcion {
            margin-left: 30px;
            font-size: 0.95em;
            color: #555;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    @livewireScripts
    <script>
        $(document).ready(function() {
            $('#competenciasAsignar').on('submit', function(e) {
                e.preventDefault();

                let todosSeleccionados = true;

                $(".competencia-select").each(function() {
                    if ($(this).val() === "") {
                        todosSeleccionados = false;
                        return false; // corta el bucle apenas encuentre uno vacío
                    }
                });

                if (!todosSeleccionados) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debes seleccionar TODAS las competencias'
                    });
                    return;
                }

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('docente.asignarCompetencias') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'COMPETENCIAS ASIGNADAS'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al ASIGNAR COMPETENCIAS'
                        });
                    }
                });
            });

            $('#formNotas').on('submit', function(e) {
                e.preventDefault(); // Evita el envío normal

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('docente.guardarAlumnos') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        // Mostrar alerta de cargando
                        Swal.fire({
                            title: 'Guardando notas...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.actualizados + ' Fila(s) ' + response.message
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al guardar las notas'
                        });
                    }
                });
            });
        });
        $('.mi-select').each(function() {
            if ($(this).val() !== "0") {
                $(this).addClass('bg-success text-dark');
            }
        });

        $('.mi-select').on('change', function() {
            if ($(this).val() !== '') {
                $(this).addClass('bg-warning text-dark');
            } else {
                $(this).removeClass('bg-warning text-dark');
            }
        });
    </script>
    <script>
        @if (session('success'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif

        @if (session('info'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('info') }}",
                icon: "info"
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
