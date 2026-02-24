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
                        <h1><i class="fas fa-project-diagram"></i> MODULO DE PROCESOS</h1>
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

    @livewireStyles

@stop

@section('content')

<style>
    /* Fondo general de la tabla */
#example1 {
    background-color: #fdfaf6; /* crema suave */
    border: 2px solid #6b4226; /* marrón institucional */
    border-radius: 6px;
    overflow: hidden;
}

/* Encabezados */
#example1 thead {
    background-color: #6b4226; /* marrón */
    color: #fff; /* texto blanco */
}

#example1 thead th {
    font-size: 13px;
    font-weight: bold;
    text-align: center;
    padding: 8px;
    border: 1px solid #5a3520;
}

/* Filas alternas */
#example1 tbody tr:nth-child(odd) {
    background-color: #fdfaf6; /* crema */
}

#example1 tbody tr:nth-child(even) {
    background-color: #f7ede2; /* crema más oscuro */
}

/* Celdas */
#example1 tbody td {
    font-size: 12px;
    font-weight: 500;
    text-align: center;
    padding: 6px;
    border: 1px solid #d3c0a6;
}

/* Hover institucional */
#example1 tbody tr:hover {
    background-color: #e6d2b5; /* crema resaltado */
    transition: 0.3s;
}

/* Botones dentro de la tabla */
#example1 .btn {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
}

/* Botón estado personalizado */
#example1 .estado-toggle.btn-success {
    background-color: #8b5e3c; /* marrón más claro */
    border: none;
}

#example1 .estado-toggle.btn-danger {
    background-color: #c97b63; /* marrón rojizo */
    border: none;
}

.bg-brown {
    background-color: #6b4226; /* marrón institucional */
}
</style>

    <div class="col-md-12">
        <div class="card card-default">
            <!-- /.card-header -->
            <div class="card-body">

                <div class="row mb-2">
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal">
                            <i class="fas fa-plus-square"></i> Crear nuevo proceso
                        </button>
                    </div>
                </div>



                <div class="card">
                    <div class="card-header">
                        <div div class="color-palette-set">

                            <center>
   <div class="card border-2" style="border-color:#6b4226;">
    <div class="card-header bg-brown text-white text-center">
        <h4 class="mb-0">TABLA PROCESOS</h4>
    </div>
</div>
</center>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>NOMBRE</th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>AÑO ADMISION
                                        </th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>NOTA MIN
                                            APROBATORIA
                                        </th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>PERIODO
                                            ACADEMICO
                                        </th>
                                         <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>REOLUCION PROCESO</th>
                                             <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>APROBACION DE METAS</th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>FECHA INICIO DE
                                            INSCRIPCION</th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>FECHA CIERRE DE
                                            INSCRIPCION</th>

                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>FECHA
                                            PUBLICACION DE
                                            RESULTADOS</th>
                                        <th style="font-size: 12px; font-weight: bold;">ESTADO</th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>EDITAR</th>
                                        <th style='font-size: 12px; font-weight: bold; font-weight: bold; '>ELIMINAR</th>
                                    </tr>
                                </thead>

                                @foreach ($procesos as $item)
                                    <tr>

                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->nombre_proceso }}</td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->año_admision }} </td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->nota_min_apro }}</td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->periodo_aca }}</td>
                                        </center>
                                            <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->rd_proceso }}</td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->aprobacion_metas }}</td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->fecha_inscri }}</td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->fecha_cierre_inscri }}
                                            </td>
                                        </center>
                                        <center>
                                            <td style='font-size: 12px; font-weight: bold; font-weight: bold; '>
                                                {{ $item->fecha_publi_resul }}
                                            </td>
                                        </center>
                                        <td>
                                            <button
                                                class="btn estado-toggle {{ $item->estado_proceso ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $item->idprocesos }}"
                                                data-estado="{{ $item->estado_proceso }}">
                                                {{ $item->estado_proceso ? 'Encendido' : 'Apagado' }}
                                            </button>
                                        </td>
                                        <td>
                                            @dump($item->rd_proceso)

                                            <a type="button" class="btn btn-warning btn-sm m-1 verModel"
                                                data-id="{{ $item->idprocesos }}"
                                                data-nombre="{{ $item->nombre_proceso }}"
                                                data-año="{{ $item->año_admision }}"
                                                data-nota="{{ $item->nota_min_apro }}"
                                                data-periodo="{{ $item->periodo_aca }}"
                                                data-rd="{{$item->rd_proceso }}"
                                                data-metas="{{$item->aprobacion_metas }}"
                                                data-inicio="{{ $item->fecha_inscri }}"
                                                data-cierre="{{ $item->fecha_cierre_inscri }}"
                                                data-publicacion="{{ $item->fecha_publi_resul }}" data-bs-toggle="modal"
                                                data-bs-target="#verDetalle">
                                                <i class="fas fa-clipboard"></i>
                                            </a>
                                            
                                        </td>


                                        <td>
                                            

                                            <form action="{{ route('procesos.eliminar', $item->idprocesos) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este proceso?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>


                                    </tr>
                                @endforeach

                            </table>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <!-- /.row -->
    <!-- END ALERTS AND CALLOUTS -->

    <!-- Botón para abrir el modal -->


    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-align-justify"></i> Formulario de
                        Proceso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('procesos.agregar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="nombreproceso">Nombre del Proceso</label>
                            <input type="text" class="form-control" id="nombreproceso" name="nombreproceso" required>
                        </div>
                        <div class="form-group">
                            <label for="añoadmision">Año de Admisión</label>
                            <input type="number" class="form-control" id="añoadmision" name="añoadmision" required>
                        </div>
                        <div class="form-group">
                            <label for="notamin">Nota Mínima Aprobatoria</label>
                            <input type="number" class="form-control" id="notamin" name="notamin" required>
                        </div>
                        <div class="form-group">
                            <label for="periacade">Periodo Académico</label>
                            <select class="form-control" id="periacade" name="periacade" required>
                                <option value="">Seleccione...</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="rd_proceso_form">RD del proceso</label>
                            <input type="text" class="form-control" id="rd_proceso_form" name="rd_proceso_form" required>
                        </div>


                        <div class="form-group">
                            <label for="aprobacion_metas_form">Aprobacion de metas</label>
                            <input type="text" class="form-control" id="aprobacion_metas_form" name="aprobacion_metas_form" required>
                        </div>
                       

                        <div class="form-group">
                            <label for="fechaini">Fecha de Inicio de Inscripción</label>
                            <input type="date" class="form-control" id="fechaini" name="fechaini" required>
                        </div>
                        <div class="form-group">
                            <label for="fechafin">Fecha de Cierre de Inscripción</label>
                            <input type="date" class="form-control" id="fechafin" name="fechafin" required>
                        </div>
                        <div class="form-group">
                            <label for="fecharesu">Fecha de Publicación de Resultados</label>
                            <input type="date" class="form-control" id="fecharesu" name="fecharesu" required>
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

    <div class="modal fade" id="verDetalle" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-pencil-alt"></i> <i
                            class="fas fa-project-diagram"></i> Editar Proceso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('procesos.editar') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                        </div>
                        <div class="mb-3">
                            <label for="año" class="form-label">Año de Admisión</label>
                            <input type="text" class="form-control" id="año" name="año">
                        </div>
                        <div class="mb-3">
                            <label for="nota" class="form-label">Nota Mínima Aprobatoria</label>
                            <input type="text" class="form-control" id="nota" name="nota">
                        </div>
                        <div class="form-group">
                            <label for="periacade">Periodo Académico</label>
                            <select class="form-control" id="periacade" name="periacade" required>
                                <option value="">Seleccione...</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="rd">RD del proceso</label>
                            <input type="text" class="form-control" id="rd" name="rd" required>
                        </div>
                        <div class="form-group">
                            <label for="metas">Aprobacion de metas</label>
                            <input type="text" class="form-control" id="metas" name="metas" required>
                        </div>
                        <div class="mb-3">
                            <label for="inicio" class="form-label">Fecha de Inicio de Inscripción</label>
                            <input type="date" class="form-control" id="inicio" name="inicio">
                        </div>
                        <div class="mb-3">
                            <label for="cierre" class="form-label">Fecha de Cierre de Inscripción</label>
                            <input type="date" class="form-control" id="cierre" name="cierre">
                        </div>
                        <div class="mb-3">
                            <label for="publicacion" class="form-label">Fecha de Publicación de Resultados</label>
                            <input type="date" class="form-control" id="publicacion" name="publicacion">
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
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('.verModel').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = document.querySelector('#verDetalle');
                    modal.querySelector('#id').value = this.dataset.id;
                    modal.querySelector('#nombre').value = this.dataset.nombre;
                    modal.querySelector('#año').value = this.dataset.año;
                    modal.querySelector('#nota').value = this.dataset.nota;
                    modal.querySelector('#periacade').value = this.dataset.periodo;
                    modal.querySelector('#rd').value = this.dataset.rd;
                    modal.querySelector('#metas').value = this.dataset.metas;
                    modal.querySelector('#inicio').value = this.dataset.inicio;
                    modal.querySelector('#cierre').value = this.dataset.cierre;
                    modal.querySelector('#publicacion').value = this.dataset.publicacion;
                    // Mostrar el modal
                    $('#verDetalle').modal('show');
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '.estado-toggle', function() {
            const button = $(this);
            const id = button.data('id');
            const estadoActual = button.data('estado');

            $.ajax({
                url: "{{ route('procesos.cambiarEstado') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    estado: estadoActual
                },
                success: function(response) {
                    if (response.success) {
                        button.data('estado', response.nuevo_estado);
                        button.removeClass('btn-success btn-danger')
                            .addClass(response.nuevo_estado ? 'btn-success' : 'btn-danger')
                            .text(response.nuevo_estado ? 'Encendido' : 'Apagado');
                    }
                }
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

@stop
