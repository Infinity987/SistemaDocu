@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />



    <div class="callout callout-danger mb-0">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-list"></i>
                            MODULO DE VACANTES</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Inicio</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        .hidden {
            display: none;
        }
    </style>

@stop

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-sm-4">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal">
                    <i class="fas fa-plus-square"></i> Crear Tabla de Vacantes
                </button>
            </div>
        </div>
    </div>





    <!-- Botón para abrir el modal -->


    <!-- Modal -->
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-align-center"></i> Formulario de
                        registro de
                        vacantes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('procesos.agregarvacantes') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="proceso"><i class="fas fa-project-diagram"></i> PROCESOS:</label>
                            <select id="proceso" name="proceso" class="form-control" onchange="mostrarModalidadYTabla()">
                                <option value="">Seleccione un proceso:</option>
                                @foreach ($procesos as $item)
                                    <option value="{{ $item->idprocesos }}">{{ $item->nombre_proceso }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modalidad"><i class="fas fa-project-diagram"></i> MODALIDAD:</label>
                            <select id="modalidad" name="modalidad" class="form-control" style="display:none;">
                                <option value="">Seleccione una modalidad</option>
                                @foreach ($modalidad as $item)
                                    <option value="{{ $item->idmodalidad }}">{{ $item->nombre_modalidad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inicio" class="form-label">Fecha de Inicio de Inscripción</label>
                            <input type="date" class="form-control" id="inicio" name="inicio">
                        </div>
                        <div class="form-group">
                            <label for="cierre" class="form-label">Fecha de Cierre de Inscripción</label>
                            <input type="date" class="form-control" id="cierre" name="cierre">
                        </div>

                        <div class="form-group">
                            <table class="table" id="tabla-vacantes" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre de Carrera</th>
                                        <th>Número de Vacantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($carreras as $index => $carrera)
                                        <tr>
                                            <td width="120px"><input type="number" class="form-control"
                                                    id="idvacante_{{ $index }}"
                                                    name="vacantes[{{ $index }}][idcarrera]"
                                                    value="{{ $carrera->idcarreras }}" readonly>
                                            </td>
                                            <td><input type="text" class="form-control"
                                                    id="nombrecarrera_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nombrecarrera]"
                                                    value="{{ $carrera->nombre_de_carrera }}" readonly></td>
                                            <td width="120px"><input type="number" class="form-control"
                                                    id="cantivacantes_{{ $index }}"
                                                    name="vacantes[{{ $index }}][cantivacantes]" required></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                    class="fas fa-window-close"></i>
                                Cerrar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>


    @livewire('selectvacaproce')


    <div class="modal fade" id="verDetalle" tabindex="-1" aria-labelledby="verDetalleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verDetalleLabel"><i class="fas fa-sign-in-alt"></i> Detalles de la
                        Vacante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('procesos.editarvacantes') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="idvaca" name="idvaca">
                        <div class="form-group">
                            <label for="nombreproce">Nombre de la Modalidad</label>
                            <input id="nombreproce" name="nombreproce" type="text" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nombrecarre">Nombre de la Carrera</label>
                            <input id="nombrecarre" name="nombrecarre" type="text" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="cantiva">Cantidad de Vacantes</label>
                            <input id="cantiva" name="cantiva" type="text" class="form-control">
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                    class="fas fa-window-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@stop
@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Bootstrap (opcional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        function mostrarModalidadYTabla() {
            // Obtener los elementos de los selectores y la tabla
            var selectProceso = document.getElementById('proceso');
            var selectModalidad = document.getElementById('modalidad'); // Cambié el id a 'modalidad'
            var tabla = document.getElementById('tabla-vacantes');

            // Mostrar el selector de modalidad si hay un proceso seleccionado
            if (selectProceso.value) {
                selectModalidad.style.display = 'block'; // Mostrar modalidad
            } else {
                selectModalidad.style.display = 'none'; // Ocultar modalidad
                tabla.style.display = 'none'; // Ocultar tabla
            }

            // Mostrar la tabla si hay una modalidad seleccionada
            selectModalidad.addEventListener('change', function() {
                if (selectModalidad.value) {
                    tabla.style.display = 'table';
                } else {
                    tabla.style.display = 'none';
                }
            });
        }
    </script>



    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
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

            // Use delegated events
            document.body.addEventListener('click', function(event) {
                if (event.target.closest('.verModel')) {
                    let button = event.target.closest('.verModel');

                    // Asignamos los valores a los campos del modal
                    const modal = document.querySelector('#verDetalle');
                    modal.querySelector('#idvaca').value = button.dataset.idvaca;
                    modal.querySelector('#nombreproce').value = button.dataset.nombreproce;
                    modal.querySelector('#nombrecarre').value = button.dataset.nombrecarre;
                    modal.querySelector('#cantiva').value = button.dataset.cantiva;

                    // Mostramos el modal (actualización manual)
                    modal.style.display = 'block';
                }
            });
        }
    </script>

    <script>
        $('#verDetalle').modal('show');
    </script>

    @livewireScripts

@stop
