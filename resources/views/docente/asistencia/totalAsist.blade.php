@extends('adminlte::page')

@section('title', 'Ver reporte asistencia')

@section('content_header')
    @can('docente.calificaciones')
        <div class="callout callout-danger mb-0 estiTitulo">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1 style="font-size: 28px;" class="titulo-asistencia"><i class="fas fa-pen"></i> <i
                                    class="fas fa-chalkboard-teacher"></i> - REPORTE DE ASISTENCIA
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
            <div class="row">
                <div class="col-xs-12 col-sm-10 col-md-10">
                    <div class="mb-3">
                        <span class="curso-nombre">
                            <i class="fas fa-book-open"></i> {{ $nombre_curso }}
                        </span>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2 col-md-2">
                    <div class="mb-3">
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

                                'fech_ini' => $fech_ini,
                                'fech_fin' => $fech_fin,

                            ];
                        @endphp
                        <a href="{{ route('docente.exportarPDF', $datos) }}" target="_blank" class="btn btn-danger btn-block btn-sm shadow">
                            <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                        </a>
                    </div>
                </div>
            </div>



        </div>
        {{-- @dump($asistencias) --}}
        <div class="card-body table-responsive p-2 custom-scroll" style="overflow-x: auto;">
            <div class="box box-primary">
                <form id="asistencia">
                    @csrf
                    <div class="box-body">
                        <!-- Tabla de alumnos -->
                        <div class="table-responsive" style="max-height: 500px; overflow: auto;">
                            <table class="table table-bordered table-striped table-hover table-sm"
                                style="min-width: max-content;">
                                <thead style="background-color:#3c8dbc; color:white; position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                        <th rowspan="2"
                                            style=" position: sticky;left: 0;z-index: 3;background-color: #3c8dbc;color: white;text-align: center;vertical-align: middle;width: 60px;">
                                            #
                                        </th>
                                        <th rowspan="2"
                                            style="position: sticky;left: 60px;z-index: 3;background-color: #3c8dbc;color: white;text-align: center;vertical-align: middle;width: 200px;">
                                            Apellidos y Nombres
                                        </th>
                                        @foreach ($fechas as $fecha)
                                            @php
                                                $dia = \Carbon\Carbon::parse($fecha)->dayOfWeek; // 0 = domingo
                                                $abreviado = match ($dia) {
                                                    0 => 'do',
                                                    1 => 'lu',
                                                    2 => 'ma',
                                                    3 => 'mi',
                                                    4 => 'ju',
                                                    5 => 'vi',
                                                    6 => 'sa',
                                                };
                                            @endphp
                                            <th
                                                style="width: 70px; text-align: center; font-size: 0.75rem; font-weight: bold;
                                                    background-color: #2f6fa0; border: 1px solid #3c8dbc;">
                                                {{ strtoupper($abreviado) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($fechas as $fecha)
                                            @php
                                                $dia = \Carbon\Carbon::parse($fecha)->dayOfWeek;
                                                $color = match ($dia) {
                                                    0 => '#ffd1a4', // domingo
                                                    6 => '#ffe5b4', // sábado
                                                    default => '#e6ffe6', // lunes a viernes
                                                };
                                            @endphp
                                            <th
                                                style="width: 70px; text-align: center; background-color: {{ $color }};
                                                        font-size: 0.85rem; font-weight: 600; color: #333;
                                                        border: 1px solid #ccc; padding: 6px 4px;
                                                        box-shadow: inset 0 -1px 0 #bbb;">
                                                {{ \Carbon\Carbon::parse($fecha)->format('d/m') }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($listAlumnos as $index => $alumno)
                                        <tr>
                                            <td
                                                style="
                                                        position: sticky;
                                                        left: 0;
                                                        background-color: #f9f9f9;
                                                        z-index: 1;
                                                        font-weight: 500;
                                                        font-size: 10px;
                                                        text-align: center;
                                                        width: 60px;
                                                    ">
                                                {{ $index + 1 }}
                                            </td>

                                            <td
                                                style="
                                                    position: sticky;
                                                    left: 60px;
                                                    background-color: #f9f9f9;
                                                    z-index: 1;
                                                    font-weight: 500;
                                                    font-size: 10px;
                                                    width: 200px;
                                                ">
                                                {{ $alumno->apellidos_pater_postulante }}
                                                {{ $alumno->apellidos_mater_postulante }},
                                                {{ $alumno->nombres_postulante }}
                                            </td>
                                            @foreach ($fechas as $fecha)
                                                @php
                                                    $key = $alumno->idincripcion_curso . '-' . $fecha;
                                                    $estado = $asistencias[$key]->estado ?? ''; // si no hay registro, vacío
                                                @endphp
                                                <td style="width: 70px; padding: 2px;">
                                                    <select class="form-control asistencia-select"
                                                        style="width: 100%; font-size: 0.85rem; padding: 2px;"
                                                        data-id="{{ $alumno->idincripcion_curso }}"
                                                        data-fecha="{{ $fecha }}">
                                                        <option value="">-</option>
                                                        <option value="P" {{ $estado == 'P' ? 'selected' : '' }}>P
                                                        </option>
                                                        <option value="F" {{ $estado == 'F' ? 'selected' : '' }}>F
                                                        </option>
                                                        <option value="T" {{ $estado == 'T' ? 'selected' : '' }}>T
                                                        </option>
                                                        <option value="J" {{ $estado == 'J' ? 'selected' : '' }}>J
                                                        </option>
                                                    </select>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/nombreCurso.css') }}">

    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

    @livewireScripts
    <script>
        $(document).ready(function() {
            //aplica color al cargar la tabl
            $('.asistencia-select').each(function() {
                aplicarColor($(this));
            });

            // $(".mause").hover(
            //     function() {
            //         $(this).css("color", "#ba9643");
            //     },
            //     function() {
            //         $(this).css("color", "#4a3911");
            //     }
            // );
        });

        $('.asistencia-select').on('change', function() {
            let idInscripcion = $(this).data('id');
            let fecha = $(this).data('fecha');
            let valor = $(this).val();

            //aplica color al seleccionns
            let select = $(this);
            aplicarColor(select);

            $.ajax({
                url: '{{ route('docente.actuAsis') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    idInscripcion: idInscripcion,
                    fecha: fecha,
                    estado: valor
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        }).then(() => {
                            location.reload();
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
    </script>
    <script>
        function aplicarColor(select) {
            const valor = select.val();
            select.removeClass('bg-success bg-warning bg-danger bg-info text-dark');

            if (valor === 'P') {
                select.addClass('bg-success text-dark'); // Presente → verde
            } else if (valor === 'F') {
                select.addClass('bg-danger text-dark'); // Falta → rojo
            } else if (valor === 'T') {
                select.addClass('bg-warning text-dark'); // Tardanza → amarillo
            } else if (valor === 'J') {
                select.addClass('bg-info text-dark'); // Justificado → celeste
            }
        }
    </script>
@stop
