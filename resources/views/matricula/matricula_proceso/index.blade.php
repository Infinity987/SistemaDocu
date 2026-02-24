@extends('adminlte::page')

@section('title', 'Padron')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    <style>
        .btn-institucional {
      background: linear-gradient(45deg, #533827, #7a4e2d);
      color: #fad2a3;
      border: none;
  }

  .btn-institucional:hover {
      background: linear-gradient(45deg, #6b3f24, #8a5a36);
      color: #fff3e0;
  }

  .btn-claro {
      background: linear-gradient(45deg, #fad2a3, #ffe4c4);
      color: #533827;
      border: none;
  }

  .btn-claro:hover {
      background: linear-gradient(45deg, #fbcf9e, #ffdab9);
      color: #3e2a1d;
  }

  .tabla-cursos-modal {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  background-color: #fffaf3;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

.tabla-cursos-modal th,
.tabla-cursos-modal td {
  border: 1px solid #d8bfa3;
  padding: 10px;
  text-align: center;
}

.tabla-cursos-modal thead th {
  background: linear-gradient(45deg, #fad2a3, #ffe4c4);
  color: #533827;
  font-weight: bold;
}

.tabla-cursos-modal tbody tr:nth-child(even) {
  background-color: #fff3e0;
}

.tabla-cursos-modal tbody tr:hover {
  background-color: #ffe9d1;
  transition: background-color 0.3s ease;
}
    </style>

    @livewireStyles

    {{-- <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-7">
                <h1><i class="fas fa-indent"></i>
                    MODULO DE MATRICULA</h1>
            </div>
        </div>
    </div> --}}

<div class="callout callout-danger shadow">
    <section class="content-header p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-week"></i> - MODULO DE MATRICULA</h1>
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


@stop

@section('content')

    <div class="container-fluid pb-2">
        <div class="row justify-content-between">
            <!-- Botón izquierdo -->
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal">
                <i class="fas fa-plus-square"></i> Matricula alumno
            </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModaltralado">
                <i class="fas fa-plus-square"></i> Matricula Traslado interno
            </button>


            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#eliminarModal">
                <i class="fas fa-trash-alt"></i> Generar licencia estudiante
            </button>

            {{-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalReporteGlobal">
                <i class="fas fa-file-alt"></i> Reporte general por semestre
            </button> --}}

            {{-- <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modalReportenotas">
                <i class="fas fa-file-alt"></i> Reporte reporte notas
            </button> --}}

        </div>
    </div>





        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-table"></i> GESTIÓN DE MATRÍCULAS Y LICENCIAS</h3>
                        </div>
                        <div class="card-body">

                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                   <a class="nav-link active" id="matricula-tab" data-toggle="tab" href="#matricula" role="tab">📝 Matrículas</a>
                                </li>
                                  <li class="nav-item">
                                   <a class="nav-link" id="matriculasemes-tab" data-toggle="tab" href="#matriculasemestre" role="tab">📝 Matrículas por semestres</a>

                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="licencia-tab" data-toggle="tab" href="#licencia" role="tab">📄 Licencias</a>

                                </li>
                                 <li class="nav-item">
                                    <a class="nav-link" id="pagossubsanacion-tab" data-toggle="tab" href="#pagossubsanacion" role="tab">📄 Pagos docentes subsanacion</a>

                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div class="tab-pane fade show active" id="matricula" role="tabpanel">
  <div wire:ignore>
    <livewire:select-ver-matriculas />
  </div>
</div>

<div class="tab-pane fade" id="matriculasemestre" role="tabpanel">
  <div wire:ignore>
    <h5 class="mt-3 mb-2 text-primary">📊 Resumen de Matrícula por Malla</h5>
    <livewire:ver-resumen-matricula-por-malla />
  </div>

  <hr class="my-4">


</div>

<div class="tab-pane fade" id="licencia" role="tabpanel">
  <div wire:ignore>
    <livewire:Admin.ver-licencia-alumnos />
  </div>
</div>

<div class="tab-pane fade" id="pagossubsanacion" role="tabpanel">
   <div wire:ignore>
    <h5 class="mt-3 mb-2 text-success">👨‍🏫 Docentes de Subsanación y Alumnos</h5>
    <livewire:docentessubsanacionpago />
  </div>
</div>



                            </div>

                        </div>
                    </div>
                </div>
            </div>

            </div>
    </div>


            <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                wire:ignore aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Panel para Matricular alumnos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">



                            <livewire:select-matricula-proceso />




                        </div>
                    </div>
                </div>
            </div>
    </div>


            <div class="modal fade" id="formModaltralado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                wire:ignore aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Panel para Matricular alumnos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">



                            <livewire:matricula-traslado-interno />




                        </div>
                    </div>
                </div>
            </div>





            <!-- Modal de Reporte semestre -->
            <div class="modal fade" id="modalReporteGlobal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Generar Reporte por semestre</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <form action="{{ route('matricula_proceso.semestrepdf') }}" method="POST"
                                enctype="multipart/form-data" target="_blank">
                                @csrf
                                <label for="Semestre"><i class="fas fa-project-diagram"></i> Semestre proceso:</label>
                                <select id="semestre_proceso_pdf" name="semestre_proceso_pdf" class="form-control">
                                    <option value="">Seleccione un semestre</option>
                                    @foreach ($semestres as $semestrepdf)
                                        <option value="{{ $semestrepdf->idsemestre_academico }}">
                                            {{ $semestrepdf->periodo }}</option>
                                    @endforeach
                                </select>

                                <label for="Semestre"><i class="fas fa-project-diagram"></i> carrera:</label>
                                <select id="carrera_pdf" name="carrera_pdf" class="form-control">
                                    <option value="">Seleccione un semestre</option>
                                    @foreach ($carrera as $carrerapdf)
                                        <option value="{{ $carrerapdf->idcarreras }}">{{ $carrerapdf->nombre_de_carrera }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                            class="fas fa-window-close"></i> Cerrar</button>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                        Guardar</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
    </div>





            <!-- Modal de Reporte semestre -->
            {{-- <div class="modal fade" id="modalReportenotas" tabindex="-1" role="dialog" aria-hidden="true"
                wire:ignore>
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg rounded">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">📄 Generar Reporte por Semestre</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body bg-light">
                            <form action="{{ route('matricula_proceso.semestrenotaspdf') }}" method="POST"
                                enctype="multipart/form-data" target="_blank">
                                @csrf

                                <div class="p-3 bg-white border rounded">
                                    <livewire:reporte-notas-semestre-pdf />

                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-pdf"></i> Generar PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer bg-secondary text-white justify-content-between">
                            <small class="text-white">Verifica todos los campos antes de generar el reporte.</small>
                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
     --}}





    <!-- Modal de Reporte semestre -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg rounded">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">📄 Generarlicencia de alumno</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body bg-light">

                    <div class="p-3 bg-white border rounded">
                        <livewire:licencia-alumnos />

                        <div class="text-right mt-4">

                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-secondary text-white justify-content-between">
                    <small class="text-white">Verifica todos los campos antes de generar la licencia.</small>
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    </div>




@stop

@section('js')

    @livewireScripts
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

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
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('abrirModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('formModal'));
                modal.show();
            });

            Livewire.on('cerrarModal', () => {
                const modalElement = document.getElementById('formModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let targetId = $(e.target).attr('href');
                Livewire.restart(); // esto fuerza a Livewire a refrescar componentes activos
            });
        });
    </script>

<script>
    Livewire.on('abrirModalDiagnostico', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalDiagnostico'));
    modal.show();
});
</script>

@stop
