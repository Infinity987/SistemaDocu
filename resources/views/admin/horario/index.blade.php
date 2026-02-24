@extends('adminlte::page')

@section('title', 'Horario')

@section('content_header')
    @can('horario.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-calendar-day"></i> - Horario</h1>
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
    @endcan
@stop

@section('content')
    @can('horario.index')
        <div class="card card-success card-default mt-2">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-day"></i> Agregar Horario</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-headersssssssss -->
            <div class="card-body p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            @livewire('admin.traer-cursos-para-horario')
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="callout callout-info card-info card-outline pt-0 pl-0 pr-0">
                        <div class="card-header" style="background-color: rgb(187, 224, 237)">
                            <h3 class="card-title"><i class="fas fa-list-ul"></i> Historial asignacion horario</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="myTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>Año - Periodo</th>
                                            <th>Año malla</th>
                                            <th>Carrera</th>
                                            <th>Ciclo</th>
                                            <th>Aula</th>
                                            <th>Turno</th>
                                            <th>Seccion</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@stop

@section('css')
    @livewireStyles

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />

@stop
@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                language: {
                    decimal: ",",
                    thousands: ".",
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles en la tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                },
                ajax: '{{ route('horario.listar') }}',
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            return row.año + ' - ' + row.periodo;
                        },
                    },
                    {
                        data: 'año_de_inicio',
                        name: 'año_de_inicio'
                    },
                    {
                        data: 'nomcarreraa',
                        name: 'nomcarreraa'
                    },
                    {
                        data: 'nombre_ciclo',
                        name: 'nombre_ciclo'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.codigo_aula + ' - ' + row.aula_nombre;
                        },
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.nombre_turno == 'Tarde') {
                                return '<span class="badge badge-warning">' + row.nombre_turno +
                                    '</span>';
                            } else if (row.nombre_turno == 'Mañana') {
                                return '<span class="badge badge-success">' + row.nombre_turno +
                                    '</span>';
                            } else {
                                return '<span class="badge badge-info">' + row.nombre_turno +
                                    '</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<span class="badge badge-warning">' + row.nom_seccion +
                                '</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.tipodocente_curso == 1) {
                                return '<span class="badge badge-success">REGULAR</span>';
                            } else {
                                return '<span class="badge badge-warning">SUBSANACION</span>';
                            }
                        }
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('submit', '.form-eliminar', function(e) {
                e.preventDefault(); // Prevenir envío automático

                const form = this; // Guardamos el formulario actual

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Enviar el formulario actual
                    }
                });
            });
        });
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}'
            });
        </script>
    @endif
@stop
