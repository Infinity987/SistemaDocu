@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    {{-- @can('documentario.mesapar.index') --}}
        <div class="callout callout-danger mb-0">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-search"></i> <i class="fas fa-file-alt"></i>
                                - BUSCAR</h1>
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
    {{-- @endcan --}}
@stop

@section('content')
    {{-- @can('documentario.mesapar.index') --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-1">
                    <form action="{{ route('documentario.searchDocu.numex') }}" method="get" class="form-inline pb-2">
                        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">N° de Expediente:</label>
                        <input type="text" class="form-control my-1 mr-sm-2" name="num_expe" placeholder="Ingrese" required>

                        <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Buscar</button>
                    </form>
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @if (isset($xdocumen))
                        <div class="timeline">
                            @foreach ($xdocumen as $doc)
                                <div>
                                    <i
                                        class="fas fa-envelope bg-{{ $doc->idestado == 3 ? 'success' : ($doc->idestado == 2 ? 'info' : 'warning') }}"></i>
                                    <div class="timeline-item">
                                        <span
                                            class="time text-{{ $doc->idestado == 3 ? 'white' : ($doc->idestado == 2 ? 'white' : 'dark') }}">
                                            <i class="fas fa-clock"></i>
                                            {{ $doc->fecha_finalizacion ? \Carbon\Carbon::parse($doc->fecha_finalizacion)->format('d/m/Y H:i') : '---'}}
                                        </span>
                                        <h3
                                            class="timeline-header bg-{{ $doc->idestado == 3 ? 'success' : ($doc->idestado == 2 ? 'info' : 'warning') }}">
                                            <strong><strong>De:</strong> {{ $doc->emisorr }} <i class="fas fa-arrow-right"></i>
                                                <strong>para:</strong> {{ $doc->receptorr }}</strong>
                                            <small>(#{{ $doc->iddocumentos }})</small>
                                        </h3>

                                        <div class="timeline-body">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <p><strong>Asunto:</strong> {{ $doc->asunto }}</p>
                                                        <p><strong>Folio:</strong> {{ $doc->folio }}</p>
                                                        <p><strong>Estado:</strong>
                                                            @php
                                                                $estadoText = [
                                                                    1 => 'Pendiente',
                                                                    2 => 'En Revisión',
                                                                    3 => 'Finalizado',
                                                                ];
                                                                $estadoColor = [
                                                                    1 => 'warning',
                                                                    2 => 'info',
                                                                    3 => 'success',
                                                                ];
                                                            @endphp
                                                            <span
                                                                class="badge badge-{{ $estadoColor[$doc->idestado] ?? 'secondary' }}">
                                                                {{ $estadoText[$doc->idestado] ?? 'Desconocido' }}
                                                            </span>
                                                        </p>

                                                        @php
                                                            if (!function_exists('tiempoHumanoss')) {
                                                                function tiempoHumanoss($segundos)
                                                                {
                                                                    $d = floor($segundos / 86400); // 86400 seg = 1 día
                                                                    $h = floor(($segundos % 86400) / 3600); // horas restantes
                                                                    $m = floor(($segundos % 3600) / 60); // minutos restantes
                                                                    $s = $segundos % 60; // segundos restantes

                                                                    return trim(
                                                                        ($d ? "$d d " : '') .
                                                                            ($h ? "$h h " : '') .
                                                                            ($m ? "$m m " : '') .
                                                                            ($s ? "$s s" : ''),
                                                                    );
                                                                }
                                                            }
                                                        @endphp


                                                        @php
                                                            if (!function_exists('contarDiasHabiles')) {
                                                                function contarDiasHabiles($inicio, $fin)
                                                                {
                                                                    $inicio = \Carbon\Carbon::parse($inicio);
                                                                    $fin = \Carbon\Carbon::parse($fin);

                                                                    $diasHabiles = 0;

                                                                    while ($inicio->lte($fin)) {
                                                                        $feriados = ['2025-07-28', '2025-07-29']; // ejemplo

                                                                        if (
                                                                            !$inicio->isWeekend() &&
                                                                            !in_array(
                                                                                $inicio->toDateString(),
                                                                                $feriados,
                                                                            )
                                                                        ) {
                                                                            $diasHabiles++;
                                                                        }
                                                                        $inicio->addDay();
                                                                    }

                                                                    return $diasHabiles;
                                                                }
                                                            }

                                                        @endphp

                                                        @php

                                                            $envio = $doc->fecha_de_envio
                                                                ? \Carbon\Carbon::parse($doc->fecha_de_envio)
                                                                : null;
                                                            $recepcion = $doc->fecha_de_recepcion
                                                                ? \Carbon\Carbon::parse($doc->fecha_de_recepcion)
                                                                : null;
                                                            $final = $doc->fecha_finalizacion
                                                                ? \Carbon\Carbon::parse($doc->fecha_finalizacion)
                                                                : now();

                                                            $durPendSeg =
                                                                $envio && $recepcion
                                                                    ? $envio->diffInSeconds($recepcion)
                                                                    : 0;
                                                            $durRevSeg =
                                                                $recepcion && $final
                                                                    ? $recepcion->diffInSeconds($final)
                                                                    : 0;
                                                            $durTotalSeg = $durPendSeg + $durRevSeg;

                                                            $tiempoPend = tiempoHumanoss($durPendSeg);
                                                            $tiempoRev = tiempoHumanoss($durRevSeg);
                                                            $tiempoTot = tiempoHumanoss($durTotalSeg);

                                                            //para la estadis en horas
                                                            $diaspen =
                                                                $envio && $recepcion
                                                                    ? contarDiasHabiles($envio, $recepcion)
                                                                    : 0;

                                                            $diasrevi =
                                                                $recepcion && $final
                                                                    ? contarDiasHabiles($recepcion, $final)
                                                                    : 0;

                                                            $diastott =
                                                                $envio && $final
                                                                    ? contarDiasHabiles($envio, $final)
                                                                    : 0;

                                                            $colorPendiente =
                                                                $diaspen < 5
                                                                    ? '#28a745'
                                                                    : ($diaspen > 4 && $diaspen < 8
                                                                        ? '#ffc107'
                                                                        : '#dc3545');

                                                            $colorRevision =
                                                                $diasrevi < 5
                                                                    ? '#28a745'
                                                                    : ($diasrevi > 4 && $diasrevi < 8
                                                                        ? '#ffc107'
                                                                        : '#dc3545');
                                                        @endphp

                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    {{-- fechas --}}
                                                                    <div class="card collapsed-card card-danger">
                                                                        <div
                                                                            class="card-header pt-1 pb-1 pl-2 bg-{{ $doc->idestado == 3 ? 'success' : ($doc->idestado == 2 ? 'info' : 'warning') }}">
                                                                            <h3 class="card-title"><i
                                                                                    class="fas fa-calendar-alt"></i> Ver
                                                                                fechas</h3>

                                                                            <div class="card-tools">
                                                                                <button type="button" class="btn btn-tool"
                                                                                    data-card-widget="collapse">
                                                                                    <i class="fas fa-plus"></i>
                                                                                    {{-- ícono se transforma automáticamente --}}
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="card-body">
                                                                            <ul class="list-group">
                                                                                <ul class="list-unstyled">
                                                                                    {{-- <li>📥 Ingreso:
                                                                                    {{ \Carbon\Carbon::parse($doc->fecha_ingreso)->format('d/m/Y H:i') }}</li> --}}
                                                                                    <li>🚚 Envío:
                                                                                        {{ $doc->fecha_de_envio ? \Carbon\Carbon::parse($doc->fecha_de_envio)->format('d/m/Y H:i:s') : '—' }}
                                                                                    </li>
                                                                                    <li>📩 Recepción:
                                                                                        {{ $doc->fecha_de_recepcion ? \Carbon\Carbon::parse($doc->fecha_de_recepcion)->format('d/m/Y H:i:s') : '—' }}
                                                                                    </li>
                                                                                    <li>✅ Finalización:
                                                                                        {{ $doc->fecha_finalizacion ? \Carbon\Carbon::parse($doc->fecha_finalizacion)->format('d/m/Y H:i:s') : '—' }}
                                                                                    </li>

                                                                                </ul>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    {{-- tiempos --}}
                                                                    <div class="card collapsed-card card-danger">
                                                                        <div
                                                                            class="card-header pt-1 pb-1 pl-2 bg-{{ $doc->idestado == 3 ? 'success' : ($doc->idestado == 2 ? 'info' : 'warning') }}">
                                                                            <h3 class="card-title"><i class="far fa-clock"></i>
                                                                                Tiempos calendarios
                                                                            </h3>

                                                                            <div class="card-tools">
                                                                                <button type="button" class="btn btn-tool"
                                                                                    data-card-widget="collapse">
                                                                                    <i class="fas fa-plus"></i>
                                                                                    {{-- ícono se transforma automáticamente --}}
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="card-body">
                                                                            <ul class="list-group">
                                                                                <ul class="list-unstyled">
                                                                                    <li>
                                                                                        <strong>Tiempo en Pendiente:</strong>
                                                                                        {{ $tiempoPend }}
                                                                                    </li>
                                                                                    <li>
                                                                                        <strong>Tiempo en Revisión:</strong>
                                                                                        {{ $tiempoRev }}
                                                                                    </li>
                                                                                    <li>
                                                                                        <strong>Tiempo total:</strong>
                                                                                        {{ $tiempoTot }}
                                                                                    </li>
                                                                                </ul>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    {{-- estadistica --}}
                                                                    <div class="card collapsed-card card-danger">
                                                                        <div
                                                                            class="card-header pt-1 pb-1 pl-2 bg-{{ $doc->idestado == 3 ? 'success' : ($doc->idestado == 2 ? 'info' : 'warning') }}">
                                                                            <h3 class="card-title"><i
                                                                                    class="fas fa-calendar-alt"></i>
                                                                                Días laborables</h3>

                                                                            <div class="card-tools">
                                                                                <button type="button" class="btn btn-tool"
                                                                                    data-card-widget="collapse">
                                                                                    <i class="fas fa-plus"></i>
                                                                                    {{-- ícono se transforma automáticamente --}}
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="card-body">
                                                                            <div class="container-fluid">
                                                                                <div class="row ">
                                                                                    <!-- ./col -->
                                                                                    <div class="col-4 col-md-4 text-center">
                                                                                        <div
                                                                                            style="display:inline;width:90px;height:90px;">
                                                                                            <input type="text" class="knob"
                                                                                                value="{{ $diaspen }}"
                                                                                                data-min="0"
                                                                                                data-max="{{ $diaspen < 8 ? 7 : ($diaspen > 7 && $diaspen < 22 ? 21 : 100) }}"
                                                                                                data-width="90" data-height="90"
                                                                                                data-fgcolor="{{ $colorPendiente }}"
                                                                                                data-thickness="0.2"
                                                                                                style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background: none; font: bold 18px Arial; text-align: center; color: rgb(245, 105, 84); padding: 0px; appearance: none;"
                                                                                                readonly>
                                                                                        </div>

                                                                                        <div class="knob-label">Dias en Pendiente
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- ./col -->
                                                                                    <div class="col-4 col-md-4 text-center">
                                                                                        <div
                                                                                            style="display:inline;width:90px;height:90px;">
                                                                                            <input type="text" class="knob"
                                                                                                value="{{ $diasrevi }}"
                                                                                                data-min="0"
                                                                                                data-max="{{ $diasrevi < 8 ? 7 : ($diasrevi > 7 && $diasrevi < 22 ? 21 : 100) }}"
                                                                                                data-width="90"
                                                                                                data-height="90"
                                                                                                data-fgcolor="{{ $colorRevision }}"
                                                                                                data-thickness="0.2"
                                                                                                style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background: none; font: bold 18px Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; appearance: none;"
                                                                                                readonly>
                                                                                        </div>

                                                                                        <div class="knob-label">Dias en revisión
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- ./col -->
                                                                                    <div class="col-4 col-md-4 text-center">
                                                                                        <div
                                                                                            style="display:inline;width:90px;height:90px;">
                                                                                            <input type="text"
                                                                                                class="knob"
                                                                                                value="{{ $diastott }}"
                                                                                                data-width="90"
                                                                                                data-height="90"
                                                                                                data-fgcolor="#00c0ef"
                                                                                                style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background: none; font: bold 18px Arial; text-align: center; color: rgb(0, 192, 239); padding: 0px; appearance: none;" readonly>
                                                                                        </div>

                                                                                        <div class="knob-label">Total dias
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="timeline-footer">
                                            <a class="btn btn-primary btn-sm" href="#">Ver Detalles</a>
                                        </div> --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- <div class="alert alert-info">
                            Realiza una búsqueda para ver el historial del expediente.
                        </div> --}}
                    @endif
                </div>
            </div>
        </div>
    {{-- @endcan --}}
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
    <script>
        $(function() {
            $('.knob').knob();
        });
    </script>

    <script>
        let dependenciaId = {{ $id_depen }};
        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.DocumentoRecibido', (e) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                            var audio = new Audio('{{ asset('sound/noti.mp3') }}'); // Ruta sonido
                            audio.play();
                        },
                        didClose: () => {
                            $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                                .cont_estados[0]
                                .cont_estado);
                        }
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Nuevo documento recibido"
                    });
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.noEditarDocumento', (e) => {
                    $('#datatablesSimple').DataTable().ajax.reload();
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.editarDocumento', (e) => {
                    $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                });
        });
    </script>

    <script>
        function bloqueare(event) {
            if (event.key === "e" || event.key === "E" || event.key === "-" || event.key === ".") {
                return false;
            }
        }

        $(document).ready(function() {

        })
    </script>
@stop
