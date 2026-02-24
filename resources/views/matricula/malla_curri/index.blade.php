@extends('adminlte::page')

@section('title', 'Padron')

@section('content_header')

<style>
  .btn-custom {
      padding: 12px 18px;
      font-weight: bold;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
  }

  .btn-custom:hover {
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0px 6px 12px rgba(0,0,0,0.2);
  }

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
</style>

<style>
  .form-group label {
    font-weight: bold;
    color: #533827;
  }

  .form-control {
    border: 2px solid #fad2a3;
    border-radius: 8px;
    background-color: #fffaf5;
    transition: box-shadow 0.3s ease;
  }

  .form-control:focus {
    box-shadow: 0 0 5px rgba(250, 210, 163, 0.8);
    border-color: #533827;
  }

  .table th {
    background-color: #533827;
    color: #fad2a3;
    font-weight: bold;
    text-transform: uppercase;
    border: none;
  }

  .table td {
    vertical-align: middle;
    border: none;
  }

  .table-primary {
    background-color: #fad2a3 !important;
    color: #533827;
  }

  .badge.bg-success {
    background-color: #533827 !important;
    color: #fad2a3;
  }

  .btn-warning {
    background-color: #fad2a3;
    color: #533827;
    border: none;
  }

  .btn-warning:hover {
    background-color: #f5c88d;
    color: #3e2a1d;
  }

  .btn-info {
    background-color: #533827;
    color: #fad2a3;
    border: none;
  }

  .btn-info:hover {
    background-color: #6b3f24;
    color: #fff3e0;
  }

  .btn-success {
    background-color: #fad2a3;
    color: #533827;
    border: none;
  }

  .btn-success:hover {
    background-color: #fbcf9e;
    color: #3e2a1d;
  }

  .modal-header {
    background-color: #533827;
    color: #fad2a3;
    border-bottom: none;
  }

  .modal-title i {
    margin-right: 8px;
  }

  .btn-primary {
    background-color: #533827;
    border: none;
    color: #fad2a3;
  }

  .btn-primary:hover {
    background-color: #6b3f24;
    color: #fff3e0;
  }

  .alert-warning {
    background-color: #fff3e0;
    color: #533827;
    border: 1px solid #fad2a3;
  }
  .table {
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
  border: 2px solid #533827;
  background-color: #fffaf5;
}

.table th,
.table td {
  border: 1px solid #533827;
  padding: 10px;
  vertical-align: middle;
}

.table th {
  background-color: #533827;
  color: #fad2a3;
  text-transform: uppercase;
  font-weight: bold;
  font-size: 0.9rem;
}

.table-primary {
  background-color: #fad2a3 !important;
  color: #533827;
  font-weight: bold;
  border-top: 2px solid #533827;
  border-bottom: 2px solid #533827;
}

.table tbody tr:hover {
  background-color: #fff3e0;
  transition: background-color 0.3s ease;
}

.badge.bg-success {
  background-color: #533827 !important;
  color: #fad2a3;
  font-size: 0.7rem;
  padding: 4px 8px;
  border-radius: 10px;
}

.table-sm td,
.table-sm th {
  padding: 8px;
}
.btn-institucional {
  background-color: #533827;
  color: #fad2a3;
  border: none;
}
.btn-institucional:hover {
  background-color: #6b3f24;
  color: #fff3e0;
}
</style>


    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    @livewireStyles

    <div class="callout callout-danger shadow">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fa fa-book" aria-hidden="true"></i> MODULO MALLA CURRICULAR</h1>
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

@stop

@section('content')

   <div class="container-fluid pb-2">
    <div class="row">
        <div class="col-12 d-flex justify-content-between flex-wrap gap-2">
          <button type="button" class="btn btn-custom btn-institucional" data-toggle="modal" data-target="#formModal">
    <i class="fas fa-plus-square"></i> AGREGAR NUEVA MALLA CURRICULAR
</button>

<button type="button" class="btn btn-custom btn-claro" data-toggle="modal" data-target="#competencias">
    <i class="fas fa-plus-square"></i> AGREGAR COMPETENCIAS
</button>

<button type="button" class="btn btn-custom btn-institucional" data-toggle="modal" data-target="#eliminarcompetencias">
    <i class="fas fa-trash-alt"></i> ELIMINAR COMPETENCIAS
</button>

<button type="button" class="btn btn-custom btn-claro" data-toggle="modal" data-target="#eliminarModal">
    <i class="fas fa-trash-alt"></i> ELIMINAR PLAN DE ESTUDIO
</button>
        </div>
    </div>
</div>


    <div class="container-fluid pt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header ">
                        <h3 class="card-title"><i class="fas fa-table"></i> TABLA DE MALLA CURRICULAR</h3>
                    </div>
                    <div class="card-body">
                        <livewire:admin.selectmallacurri />
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #533827; color: #fad2a3;">
  <h5 class="modal-title" id="exampleModalLabel">
    <i class="fas fa-upload me-2"></i> Panel para subir Malla Curricular
  </h5>
  <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
                    <div class="modal-body">
                        <form action="{{ route('malla.archivocsv') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                           <div class="form-group">
  <label for="csvFile" class="fw-bold text-dark">
    <i class="fas fa-file-csv me-1"></i> Subir archivo CSV
  </label>
  <input type="file" class="form-control border border-warning" id="csvFile" name="csvFile" accept=".csv" required>
  <small class="form-text text-muted">Solo se aceptan archivos con extensión .csv</small>
</div>

<div class="mb-3">
  <a href="{{ asset('plantillas/modelo_de_subir_malla.xlsx') }}" class="btn btn-outline-primary btn-sm" download>
    <i class="fas fa-download"></i> Descargar plantilla CSV
  </a>
</div>


           <div class="modal-footer">
  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
    <i class="fas fa-times-circle"></i> Cerrar
  </button>
  <button type="submit" class="btn btn-institucional">
    <i class="fas fa-save"></i> Guardar
  </button>
</div>
          </form>
        </div>
      </div>
    </div>
  </div>

        {{-- modal para subir competencias --}}

        <div class="modal fade" id="competencias" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Panel para subir competencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('malla.subircompe') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="id_plan">Seleccione una Malla Curricular</label>
                                <select class="form-control" name="id_plan" id="id_plan" required>
                                    <option value="">-- Seleccione una malla --</option>
                                    @foreach ($mallas as $malla)
                                        <option value="{{ $malla->idmalla_curricular }}"
                                            data-descripcion="{{ $malla->nombre_malla_curricular }} - {{ $malla->año_de_inicio }}">
                                            {{ $malla->nombre_malla_curricular }} - {{ $malla->año_de_inicio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_malla">Descripción de la Malla Seleccionada</label>
                                <textarea class="form-control" id="descripcion_malla" rows="3" readonly></textarea>
                            </div>

                            <div class="form-group">
                                <label for="csvFile">Subir archivo CSV</label>
                                <input type="file" class="form-control-file" id="csvFile" name="csvFile"
                                    accept=".csv" required>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="verDetalle" tabindex="-1" aria-labelledby="verDetalleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verDetalleLabel"><i class="fas fa-sign-in-alt"></i> Detalles del
                            curso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('malla.cursosmodi') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="idcurso" name="idcurso">
                            <div class="form-group">
                                <label for="nombrecurso">Nombre del curso</label>
                                <input id="nombrecurso" name="nombrecurso" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="horacurso">horas</label>
                                <input id="horacurso" name="horacurso" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="creditocurso">creditos</label>
                                <input id="creditocurso" name="creditocurso" type="text" class="form-control">
                            </div>
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

        {{-- modal para eleminar  --}}

        <div class="modal fade" id="eliminarModal" tabindex="-1" role="dialog" aria-labelledby="eliminarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('malla.eliminar') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="eliminarModalLabel"><i class="fas fa-exclamation-triangle"></i>
                                Eliminar Plan de Estudio</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <p>¿Estás seguro que deseas eliminar este plan de estudio?</p>

                            <div class="form-group">
                                <label for="id_plan">Seleccione una Malla Curricular</label>
                                <select class="form-control" name="id_plan" id="id_plan" required>
                                    <option value="">-- Seleccione una malla --</option>
                                    @foreach ($mallas as $malla)
                                        <option value="{{ $malla->idmalla_curricular }}"
                                            data-descripcion="{{ $malla->nombre_malla_curricular }} - {{ $malla->año_de_inicio }}">
                                            {{ $malla->nombre_malla_curricular }} - {{ $malla->año_de_inicio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_malla">Descripción de la Malla Seleccionada</label>
                                <textarea class="form-control" id="descripcion_malla" rows="3" readonly></textarea>
                            </div>


                        </div>


                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        {{-- modal para eleminar  --}}


        <div class="modal fade" id="eliminarcompetencias" tabindex="-1" aria-labelledby="verDetalleLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-sm">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="verDetalleLabel">
                            <i class="fas fa-trash-alt me-2"></i>Eliminar Competencias
                        </h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @livewire('eliminar-competencias')

                    </div>
                </div>
            </div>
        </div>

    @stop

    @section('js')
        <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
        <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
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
            console.log('Script is running...');
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOMContentLoaded');
                initModalEvents();
            });

            document.addEventListener('livewire:load', function() {
                console.log('livewire:load');
                initModalEvents();
            });

            document.addEventListener('livewire:update', function() {
                console.log('livewire:update');
                initModalEvents();
            });

            function initModalEvents() {
                console.log('Initializing modal events...');

                document.body.addEventListener('click', function(event) {
                    const button = event.target.closest('.verModel');
                    if (!button) return;

                    // Asignamos valores desde los atributos data-*
                    const id = button.dataset.idcurso;
                    const nombre = button.dataset.nombrecurso;
                    const hora = button.dataset.horacurso;
                    const credito = button.dataset.creditocurso;

                    const modal = document.querySelector('#verDetalle');
                    modal.querySelector('#idcurso').value = id;
                    modal.querySelector('#nombrecurso').value = nombre;
                    modal.querySelector('#horacurso').value = hora;
                    modal.querySelector('#creditocurso').value = credito;

                    // Abrimos el modal correctamente con Bootstrap 5
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                });
            }
        </script>



        <script>
            document.getElementById('id_plan').addEventListener('change', function() {
                const descripcion = this.options[this.selectedIndex].getAttribute('data-descripcion');
                document.getElementById('descripcion_malla').value = descripcion || '';
            });
        </script>

        <script>
            function abrirModalPrecurso() {
                $('#modalPrecurso').modal('show');
            }
        </script>

        <script>
            Livewire.on('cerrar-modal-precurso', () => {
                // Quitar foco antes de cerrar
                document.activeElement?.blur();

                // Cerrar modal después de un pequeño delay
                setTimeout(() => {
                    $('#modalPrecurso').modal('hide');
                }, 50);
            });
        </script>

        <script>
            function abrirModalCompetencias() {
                $('#modalCompetencias').modal('show');
            }

            Livewire.on('cerrar-modal-competencias', () => {
                document.activeElement.blur();
                $('#modalCompetencias').modal('hide');
            });
        </script>

<script>
  function abrirModalFormacion() {
    $('#modalFormacion').modal('show');
  }
</script>

<script>
  Livewire.on('cerrar-modal-formacion', () => {
    document.activeElement?.blur();
    setTimeout(() => {
      $('#modalFormacion').modal('hide');
    }, 50);
  });
</script>

@stop
