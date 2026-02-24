@extends('adminlte::page')

@section('title', 'Horario')

@section('content_header')
    @can('horario.index')
        <div class="callout callout-danger mb-0">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1><i class="fas fa-calendar-alt"></i> -
                                @if ($editar == 1)
                                    Editar
                                @else
                                    Agregar
                                @endif - {{ $nomCarrera }}
                            </h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('horario.index') }}">Inicio</a></li>
                                <li class="breadcrumb-item active">Agregar horario</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('horario.index')
        @php
            $cursosHoras = collect($query)
                ->filter(function ($c) {
                    return !is_null($c->dc_iddocen_curso); // Solo cursos con docente asignado
                })
                ->mapWithKeys(function ($c) {
                    return [
                        $c->dc_iddocen_curso => [
                            'horas' => $c->horas,
                            'nombre' => $c->nombre_curso,
                            'nombre_doce' => $c->nombre
                        ]
                    ];
                });
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left shadow-sm" style="background-color: rgb(177, 232, 176)">
                        <li class="breadcrumb-item"><a
                                style="color: #392903; text-decoration: none;">{{ 'Malla curricular: ' . $nomAño }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                style="color: #392903; text-decoration: none;">{{ 'Semestre académico: ' . $nomSemestre }}</a>
                        </li>
                    </ol>
                </div>
                {{-- <div class="col-sm-6 ">
                                @if ($tipodocente_curso == 2)
                                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $nombreTipoDocenCurso }}</strong>.
                                    </div>
                                @endif
                            </div> --}}
            </div>
        </div>

        {{-- para ver los docentes y cursos --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card collapsed-card shadow-sm">
                        <div class="card-header bg-gradient-info text-white m-1 p-2 pr-4">
                            <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Cursos y Docentes</h3>

                            <div class="card-tools" <button type="button" class="btn btn-tool text-white"
                                data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="container-fluid">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="text-center"
                                            style="background: linear-gradient(135deg, #5d3106, #b16816); color: white;">
                                            <tr>
                                                <th>Num</th>
                                                <th>Cursos</th>
                                                <th>Cant. Hora</th>
                                                <th>Docente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($query as $index => $cursoDoce)
                                                <tr>
                                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                                    <td>{{ $cursoDoce->nombre_curso }}</td>
                                                    <td style="text-align: center;">{{ $cursoDoce->horas }}</td>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- para consultar --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">

                    <form action="{{ route('agreindex.index') }}" method="POST" class="">
                        @csrf
                        <input type="hidden" name="selectCarrera" value="{{ $selectCarrera }}">
                        <input type="hidden" name="idmalla" value="{{ $idmalla }}">
                        <input type="hidden" name="semestre_acad" value="{{ $semestre_acad }}">

                        <input type="hidden" name="nomAño" value="{{ $nomAño }}">
                        <input type="hidden" name="nomCarrera" value="{{ $nomCarrera }}">
                        <input type="hidden" name="nomSemestre" value="{{ $nomSemestre }}">

                        <input type="hidden" name="activaHorario" value="1">
                        <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">

                        <div class="form-group row align-items-center">
                            <label for="" class="col-auto col-form-label">Ciclo:</label>
                            <div class="col-sm-1">
                                <select name="selectCiclo" id="selecCicloo" class="form-control form-control-sm"
                                    {{ $editar == 1 ? 'disabled' : '' }}>
                                    <option value="">Seleccione</option>
                                    @foreach ($ciclos as $ciclo)
                                        <option value="{{ $ciclo->idciclos }}"
                                            {{ $ciclo->idciclos == $selectCiclo ? 'selected' : '' }}>
                                            {{ $ciclo->nombre_ciclo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <label for="" class="col-auto col-form-label">Tipo:</label>
                            <div class="col-sm-2">
                                <select name="tipoo" id="selectTipo" class="form-control form-control-sm"
                                    {{ $editar == 1 ? 'disabled' : '' }}>
                                    <option value="">Seleccione</option>
                                    @if ($tipoReguSubsa != 2)
                                        @foreach ($listReguSupsa as $listReguSups)
                                            <option value="{{ $listReguSups->idtipo_matricula }}"
                                                {{ $listReguSups->idtipo_matricula == $tipoReguSubsa ? 'selected' : '' }}>
                                                {{ $listReguSups->nombre_tipo_matricula }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="2" selected>
                                            SUBSANACION
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <label for="" class="col-auto col-form-label">Turno:</label>
                            <div class="col-sm-1">
                                @if ($activaHorario == 1)
                                    <select class="form-control form-control-sm" id="turno" name="turno" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($turnos as $turno)
                                            <option value="{{ $turno->idturno }}"
                                                {{ $turno->idturno == $turn ? 'selected' : '' }}>{{ $turno->nombre_turno }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control form-control-sm" id="turno" name="turno" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($turnos as $turno)
                                            <option value="{{ $turno->idturno }}">{{ $turno->nombre_turno }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <label for="" class="col-auto col-form-label">Sección:</label>
                            <div class="col-sm-1">
                                @if ($activaHorario == 1)
                                    <select class="form-control form-control-sm" id="seccion" name="seccion" disabled>
                                        <option value="">Seleccione</option>
                                        @foreach ($secciones as $seccion)
                                            <option value="{{ $seccion->idseccion }}"
                                                {{ $seccion->idseccion == $secc ? 'selected' : '' }}>
                                                {{ $seccion->nom_seccion }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control form-control-sm" id="seccion" name="seccion" disabled>
                                        <option value="">Seleccione</option>
                                        @foreach ($secciones as $seccion)
                                            <option value="{{ $seccion->idseccion }}"
                                                {{ $seccion->idseccion == $secc ? 'selected' : '' }}>
                                                {{ $seccion->nom_seccion }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <label for="" class="col-auto col-form-label">Aula:</label>
                            <div class="col-sm-2">
                                @if ($activaHorario == 1)
                                    <select class="form-control form-control-sm" id="aula" name="aula" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($aulas as $aula)
                                            <option value="{{ $aula->idaula }}"
                                                {{ $aula->idaula == $aul ? 'selected' : '' }}>
                                                {{ $aula->codigo_aula . ' - ' . $aula->aula_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control form-control-sm" id="aula" name="aula" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($aulas as $aula)
                                            <option value="{{ $aula->idaula }}">
                                                {{ $aula->codigo_aula . ' - ' . $aula->aula_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            @if ($editar != 1)
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-sm btn-info form-control form-control-sm"><i
                                            class="fas fa-search"></i> Consultar</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

            </div>
        </div>

        @if ($activaHorario == 1)
            {{-- @dump($query) --}}
            {{-- @dump($turn)
            @dump($aul) --}}
            {{-- @dump($asignacionesMap) --}}

            <div class="card card-info card-outline" id="tabla-horario">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Izquierda -->
                        <div>
                            <h3 class="card-title mb-0">
                                <i class="fas fa-edit"></i>
                                @if ($editar == 1)
                                    Editar
                                @else
                                    Asignar
                                @endif
                                horario
                            </h3>
                        </div>

                        <!-- Derecha agrupada -->
                        <div class="d-flex gap-3 flex-nowrap">
                            <div class="d-flex align-items-center gap-2 mr-4">
                                @if ($editar == 1)
                                    <label id="nombre-turno" class="mb-0 font-weight-bold text-primary"></label>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if ($editar == 1)
                                    <label id="nombre-aula" class="mb-0 font-weight-bold text-success"></label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('guardarHorario') }}" method="post" id="formHorario">
                        @csrf
                        @if ($editar == 1)
                            <input type="hidden" name="editar" value="{{ $editar }}">
                        @endif
                        <input type="hidden" name="selectCarrera" value="{{ $selectCarrera }}">
                        <input type="hidden" name="idmalla" value="{{ $idmalla }}">
                        <input type="hidden" name="semestre_acad" value="{{ $semestre_acad }}">
                        <input type="hidden" name="selectCiclo" value="{{ $selectCiclo }}">

                        <input type="hidden" name="nomCarrera" value="{{ $nomCarrera }}">
                        <input type="hidden" name="nomSemestre" value="{{ $nomSemestre }}">

                        <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">
                        <input type="hidden" name="nomAño" value="{{ $nomAño }}">
                        <input type="hidden" name="secc" value="{{ $secc }}">


                        {{-- variables que vienen por defecto no cambian turno y aula --}}
                        <input type="hidden" name="turnoPrime" id="turnoPrime" value="{{ $turn }}">
                        <input type="hidden" name="aulaPrime" id="aulaPrime" value="{{ $aul }}">

                        {{-- variables si cambian de turno y aula se guarda aqui --}}
                        <input type="hidden" name="turnoo" id="turnoo" value="{{ $turn }}">
                        <input type="hidden" name="aulaa" id="aulaa" value="{{ $aul }}">

                        <input type="hidden" name="tipoo" id="tipo" value="{{ $tipoReguSubsa }}">

                        {{-- @dump($dias) --}}
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="text-white" style="background: linear-gradient(135deg, #582d03, #a15b0a);">
                                    <tr>
                                        <th style="width: 15%;">Hora</th>
                                        @foreach ($dias as $dia)
                                            <th style="width: 15%;">{{ $dia->nom_dia }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($horasTurno as $horasTurn)
                                        <tr>
                                            <td class="font-weight-bold">{{ $horasTurn->nom_hora }}</td>

                                            @foreach ($dias as $dia)
                                                <td>
                                                    @if ($horasTurn->idhora != 5 && $horasTurn->idhora != 14)
                                                        @php
                                                            $clave = $horasTurn->idhora . '-' . $dia->iddias;
                                                            $asignacion = $asignacionesMap[$clave] ?? null;
                                                        @endphp

                                                        <div class="d-flex align-items-center">
                                                            <select name="asignacion[]" class="form-control select-curso mr-1"
                                                                data-idhorario="{{ $asignacion['idHorario'] ?? '' }}"
                                                                data-idhora="{{ $horasTurn->idhora }}"
                                                                data-iddia="{{ $dia->iddias }}"
                                                                {{ $asignacion ? 'disabled' : '' }}>
                                                                <option value="">Seleccione curso</option>
                                                                @foreach ($query as $curso)
                                                                    @php
                                                                        $valor =
                                                                            $horasTurn->idhora .
                                                                            '-' .
                                                                            $dia->iddias .
                                                                            '-' .
                                                                            $curso->dc_iddocen_curso;
                                                                        $selected =
                                                                            $asignacion &&
                                                                            $asignacion['id_docente_curso'] ==
                                                                                $curso->dc_iddocen_curso
                                                                                ? 'selected'
                                                                                : '';
                                                                    @endphp
                                                                    <option value="{{ $valor }}" {{ $selected }}>
                                                                        {{ $curso->nombre_curso }} - {{ $curso->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            @if ($asignacion)
                                                                <button type="button" title="Eliminar"
                                                                    class="btn btn-sm btn-danger btn-eliminar-asignado"
                                                                    data-idhorario="{{ $asignacion['idHorario'] }}">
                                                                    &times;
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span>RECESO</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-success" type="submit"><i class="fas fa-sign-in-alt"></i>
                                @if ($editar == 1)
                                    Actualizar
                                @else
                                    Registrar
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
        @endif
    @endcan
    @if ($mensaje != null)
        @php
            $tipo = $mensaje['tipo'] ?? ''; // success, danger, warning, info
            $texto = $mensaje['texto'] ?? '';
        @endphp

        <div class="alert alert-{{ $tipo }} alert-dismissible fade show auto-dismiss cerrar" role="alert">
            <i class="fas fa-check-circle mr-1"></i>
            {{ $texto }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@stop

@section('css')
    @livewireStyles
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }

        /* Fondo del menú desplegable */
        .select2-container .select2-dropdown {
            background-color: #ececec;
            /* color más oscuro */
            color: #ffffff;
            /* texto blanco */
            border: 1px solid #444;
        }

        /* Estilo de cada opción */
        .select2-container .select2-results__option {
            padding: 8px 12px;
            font-size: 14px;
            color: #5f2e00;
            background-color: #f3f3f3;
        }

        /* Hover sobre opciones */
        .select2-container--default .select2-results__option--highlighted {
            background-color: #a69690 !important;
            color: #fff !important;
        }

        /* estilo al momento de seleccionar un selectt :V */
        .select-marron {
            border-color: #503027 !important;
            background-color: #c6b5a3 !important;
            color: #492f27 !important;
            font-weight: bold;
        }
    </style>
@stop


@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Cierra automáticamente las alertas después de 3 segundos (3000ms)
        setTimeout(function() {
            $('.cerrar').alert('close');
        }, 5000);
    </script>

    <script>
        //idcursos_docente y horas de cada curso ...e
        // const cursosHoras = @json(collect($query)->mapWithKeys(fn($c) => [$c->dc_iddocen_curso => $c->horas]));

        const cursosHoras = @json($cursosHoras);



        $(document).ready(function() {
            $('.select-curso').select2({
                templateResult: formatOption
            });

            // para poner el color cuando selecciona un select de cursdos
            $('.select-curso').on('change', function() {
                const value = $(this).val();
                const container = $(this).next('.select2-container');

                if (value) {
                    container.find('.select2-selection').addClass('select-marron');
                } else {
                    container.find('.select2-selection').removeClass('select-marron');
                }
            });

            //para el color de fondo de los selects
            function formatOption(option) {
                if (!option.id) return option.text;

                // Dividir curso y docente
                let parts = option.text.split(' - ');
                let curso = parts[0] || '';
                let docente = parts[1] || '';

                return $(
                    `<span>${curso} - <span style="color: #5D4037; font-weight: bold;">${docente}</span></span>`
                );
            }

            //para contar si se pasa de horas en cursos
            function contarAsignaciones() {
                const conteo = {};

                $('.select-curso').each(function() {
                    const val = $(this).val();
                    if (val) {
                        const partes = val.split('-'); // formato: idhora-iddia-idcurso
                        const idCurso = partes[2];

                        if (!conteo[idCurso]) conteo[idCurso] = 0;
                        conteo[idCurso]++;
                    }
                });

                return conteo;
            }
            ////para contar si se pasa de horas en cursos
            $('.select-curso').on('change', function() {
                const val = $(this).val();
                if (!val) return;

                const partes = val.split('-');
                const idCurso = partes[2];
                const curso = cursosHoras[idCurso]; // ahora es un objeto con horas y nombre
                const horasPermitidas = curso.horas;
                const nombreCurso = curso.nombre;
                const conteo = contarAsignaciones();

                if (conteo[idCurso] > horasPermitidas) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Límite alcanzado',
                        text: `El curso "${nombreCurso}" ya tiene asignadas todas sus horas (${horasPermitidas}).`,
                    });

                    $(this).val('').trigger('change'); // deselecciona
                }
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('formHorario');
            const selects = document.querySelectorAll('.select-curso');

            form.addEventListener('submit', function(e) {
                const conteo = {};

                selects.forEach(select => {
                    const val = select.value;
                    if (val) {
                        const partes = val.split('-'); // formato: idhora-iddia-idcurso
                        const idCurso = partes[2];

                        if (!conteo[idCurso]) conteo[idCurso] = 0;
                        conteo[idCurso]++;
                    }
                });

                let faltantes = [];
                let excesos = [];

                for (const idCurso in cursosHoras) {
                    const curso = cursosHoras[idCurso];
                    const horasPermitidas = curso.horas;
                    const nombreCurso = curso.nombre;
                    const horasAsignadas = conteo[idCurso] || 0;

                    if (horasAsignadas > horasPermitidas) {
                        excesos.push(
                            `"${nombreCurso}" tiene ${horasAsignadas} horas asignadas, máximo permitido: ${horasPermitidas}.`
                            );
                    }

                    if (horasAsignadas < horasPermitidas) {
                        faltantes.push(
                            `"${nombreCurso}" tiene solo ${horasAsignadas} horas asignadas, faltan ${horasPermitidas - horasAsignadas}.`
                            );
                    }
                }

                let html = '';

                if (excesos.length) {
                    html +=
                        `<b style="color:#a94442;">Excesos:</b><ul style="text-align:left;">${excesos.map(e => `<li>${e}</li>`).join('')}</ul>`;
                }

                if (faltantes.length) {
                    html +=
                        `<b style="color:#8a6d3b;">Faltantes:</b><ul style="text-align:left;">${faltantes.map(e => `<li>${e}</li>`).join('')}</ul>`;
                } else {
                    html =
                        `<b style="color:#8a6d3b;">Faltantes:</b><ul style="text-align:left;">${faltantes.map(e => `<li>${e}</li>`).join('')}</ul><b style="color:#8a6d38;">Desea guardar ?:</b>`;
                }

                // if (faltantes.length > 0 || excesos.length > 0) {
                //     e.preventDefault(); // bloquea el envío
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Asignación inválida',
                //         html: html,
                //         confirmButtonText: 'Aceptar'
                //     });
                // }

                if (faltantes.length > 0 || excesos.length > 0) {

                    Swal.fire({
                        icon: 'question',
                        title: '¡Advertencia!',
                        html: html + '<br>¿Deseas continuar con el envío de todas formas?',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'No, revisar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();

                        } else {}
                    });
                    e.preventDefault();
                }
            });
        });
    </script>


    <script>
        $(document).on('click', '.btn-eliminar-asignado', function() {
            const button = $(this);
            const td = button.closest('td');
            const idHorario = button.data('idhorario');

            $.post('{{ route('horario.eliminarPorId') }}', {
                _token: '{{ csrf_token() }}',
                idHorario: idHorario
            }, function(res) {
                if (res.success) {
                    const select = td.find('select');
                    select.prop('disabled', false); // Habilitar select
                    select.val('').trigger('change'); // Limpiar selección y forzar cambio visual
                    button.remove(); // Quitar el botón eliminar
                }
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('mensaje', (event) => {
                Swal.fire({
                    title: "BUEN TRABAJO!",
                    text: event[0].msm,
                    icon: "success"
                });
            });
        });
    </script>

    <script>
        $('#turno').on('change', function() {
            $('#turnoo').val($(this).val());
            const textTurno = $(this).find('option:selected').text();
            const textValueTurno = $(this).val();

            if (textTurno == 'Seleccione' || textValueTurno == $('#turnoPrime').val()) {
                $('#nombre-turno').text('');
            } else {
                $('#nombre-turno').text('Cambiar al TURNO: ' + textTurno);
            }
        });

        $('#aula').on('change', function() {
            $('#aulaa').val($(this).val());
            const textAula = $(this).find('option:selected').text();
            const textValueAula = $(this).val();

            if (textAula == 'Seleccione' || textValueAula == $('#aulaPrime').val()) {
                $('#nombre-aula').text('');
            } else {
                $('#nombre-aula').text('Cambiar al AULA: ' + textAula);
            }
        })

        $('#selecCicloo').on('change', function() {
            $('#turno').val('').trigger('change');
            $('#aula').val('').trigger('change');
            $('#selectTipo').val('').trigger('change');
            $('#tabla-horario').remove();
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
