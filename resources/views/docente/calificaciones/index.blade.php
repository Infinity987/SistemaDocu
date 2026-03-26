@extends('adminlte::page')

@section('title', 'Cursos - calificaciones')

@section('content_header')
    @can('docente.calificaciones')
        <div class="callout callout-danger estiTitulo">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-9">
                            <h1><i class="fas fa-pen-fancy" aria-hidden="true"></i><i class="fas fa-chalkboard-teacher"></i> -
                                CURSOS - CALIFICACIONES</h1>
                        </div>
                        <div class="col-sm-3">
                            <ol class="breadcrumb float-sm-right">
                                {{-- <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('docente.horario') }}">Inicio</a></li> --}}
                                <li class="breadcrumb-item active">inicio</li>
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

        @if (!empty($listCursos))
            <div class="container-fluid">
                @php
                    $agrupadoPorCarrera = collect($listCursos)->groupBy('nombre_de_carrera');
                @endphp

                <div class="row" id="grid-carreras">

                    @foreach ($agrupadoPorCarrera as $nombreCarrera => $cursosCarrera)
                        @php $slug = Str::slug($nombreCarrera, '_'); @endphp

                        <div class="col-sm-3 col-lg-3 col-6">
                            <div class="small-box bg-success carrera-box" id="box_{{ $slug }}" style="cursor: pointer;"
                                onclick="mostrarCursos('{{ $slug }}')">
                                <div class="inner" style="height: 100px">
                                    <h4 class="mb-1 text-md h5 h4"><i class="fas fa-university"></i> {{ $nombreCarrera }}</h4>
                                    <p>{{ $cursosCarrera->count() }} curso(s)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="small-box-footer">
                                    Ver cursos <i class="fas fa-arrow-circle-down"></i>
                                </div>
                            </div>

                            <!-- Cursos ocultos, clonables al contenedor final -->
                            <div id="detalle_{{ $slug }}" style="display: none;">
                                @php $porCiclo = $cursosCarrera->groupBy('nombre_ciclo'); @endphp
                                <div class="card card-outline card-warning mt-3">
                                    <div class="card-header">
                                        <strong><i class="fas fa-university"></i> {{ $nombreCarrera }} - Cursos por
                                            ciclo</strong>
                                    </div>
                                    <div class="card-body">

                                        @foreach ($porCiclo as $ciclo => $listaCursos)
                                            <h5 class="mb-2 p-1"
                                                style="background: linear-gradient(135deg, #003983, #5eb8d4); color: white;"><i
                                                    class="fas fa-sign-out-alt"></i> Ciclo {{ $ciclo }}
                                            </h5>

                                            <ul>

                                                @foreach ($listaCursos as $curso)
                                                    <li class="m-1">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div class="col-sm-8">
                                                                    <span class="badge bg-warning"
                                                                        style="font-size: 0.9rem; padding:0.5em 1em;"><i
                                                                            class="fas fa-book"></i> Malla curricular:
                                                                        {{ $curso->año_de_inicio }}
                                                                    </span>
                                                                    @if ($curso->tipodocente_curso == 1)
                                                                        <span class="badge badge-success">R</span>
                                                                    @elseif ($curso->tipodocente_curso == 2)
                                                                        <span class="badge badge-info">S</span>
                                                                    @else
                                                                        <span class="badge badge-info">-</span>
                                                                    @endif
                                                                    <span>
                                                                        - {{ $curso->nombre_curso }}
                                                                    </span>

                                                                </div>
                                                                <div class="col-sm-4 d-flex align-items-center flex-wrap gap-2">
                                                                    @if ($curso->nombre_turno)
                                                                        <form action="{{ route('docente.verAlumnos') }}"
                                                                            method="get">
                                                                            @csrf
                                                                            <input type="hidden" name="iddocente_curso"
                                                                                value="{{ $curso->iddocente_curso }}">
                                                                            <input type="hidden" name="nombre_de_carrera"
                                                                                value="{{ $curso->nombre_de_carrera }}">
                                                                            <input type="hidden" name="nombre_curso"
                                                                                value="{{ $curso->nombre_curso }}">
                                                                            <input type="hidden" name="idciclos"
                                                                                value="{{ $curso->idciclos }}">
                                                                            <input type="hidden" name="nombre_ciclo"
                                                                                value="{{ $curso->nombre_ciclo }}">
                                                                            <input type="hidden" name="año"
                                                                                value="{{ $curso->año }}">
                                                                            <input type="hidden" name="periodo"
                                                                                value="{{ $curso->periodo }}">

                                                                            <input type="hidden" name="año_de_inicio"
                                                                                value="{{ $curso->año_de_inicio }}">
                                                                            <input type="hidden" name="nom_seccion"
                                                                                value="{{ $curso->nom_seccion }}">
                                                                            <input type="hidden" name="idcursos"
                                                                                value="{{ $curso->idcursos }}">
                                                                            <input type="hidden" name="tipodocente_curso"
                                                                                value="{{ $curso->tipodocente_curso }}">

                                                                            <input type="hidden" name="idturno"
                                                                                value="{{ $curso->idturno }}">

                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-info mr-2 mb-1"><i
                                                                                    class="fas fa-users"></i> Ver
                                                                                alumnos</button>
                                                                        </form>
                                                                    @endif
                                                                    @if ($curso->nombre_turno)
                                                                        <span
                                                                            class="badge badge-secondary mr-1">{{ $curso->nombre_turno }}</span>
                                                                        <span
                                                                            class="badge badge-success mr-1">"{{ $curso->nom_seccion }}"</span>
                                                                    @endif
                                                                    @if ($curso->codigo_aula)
                                                                        <span
                                                                            class="badge badge-info">{{ $curso->codigo_aula }}
                                                                            -
                                                                            {{ $curso->aula_nombre }}</span>
                                                                    @else
                                                                        <span class="badge badge-danger">No asignado</span>
                                                                    @endif

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </li>
                                                    <hr class="m-0 p-0">
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Contenedor final donde se muestra el detalle dinámicamente -->
                <div id="contenedor-cursos" class="mt-4 fade-slide"></div>

            </div>
        @else
            <div class="alert alert-info alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Alerta !!!</h5>
                Usted no tienes cursos asignados.
            </div>
        @endif
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">


    @livewireStyles
    <style>
        .fade-slide {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .fade-slide.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    @livewireScripts
    <script>
        function mostrarCursos(id) {
            // Resetear el estilo de todas las cajas
            document.querySelectorAll('.carrera-box').forEach(box => {
                box.classList.remove('bg-warning');
                box.classList.add('bg-success');
            });

            // Estilo de la caja seleccionada
            const boxSeleccionado = document.getElementById('box_' + id);
            boxSeleccionado.classList.remove('bg-success');
            boxSeleccionado.classList.add('bg-warning');

            // Mostrar los cursos en el contenedor inferior
            const contenedor = document.getElementById('contenedor-cursos');
            const detalle = document.getElementById('detalle_' + id);
            contenedor.innerHTML = detalle.innerHTML;

            // Remover la animación antes de insertar nuevo contenido
            contenedor.classList.remove('show');

            setTimeout(() => {
                contenedor.innerHTML = detalle.innerHTML;
                contenedor.classList.add('show');
            }, 50);
        }
    </script>
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
