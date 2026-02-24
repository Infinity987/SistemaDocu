@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @can('docente.horario')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-calendar-week"></i> - HORARIOS</h1>
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
    @endcan
@stop

@section('content')
    @can('docente.horario')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-day"></i> Historial asignacion horario</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background: linear-gradient(-20deg, #017a52, #85cd78); color: rgb(255, 255, 255);">
                                        <tr>
                                            <th class="text-center">Año - Periodo</th>
                                            <th class="text-center">Programa de estudio</th>
                                            <th class="text-center">Año Malla</th>
                                            <th class="text-center">Ciclo</th>
                                            <th class="text-center">Nombre curso</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">turno</th>
                                            <th class="text-center">Aula - Código</th>
                                            <th class="text-center">Sección</th>
                                            <th class="text-center">Acciones</th>
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
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />

    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    @livewireScripts
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: false,
                ordering: true,
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
                ajax: '{{ route('docente.listarHorario') }}',
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            return row.año + ' - ' + row.periodo;
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'nombre_de_carrera',
                        name: 'nombre_de_carrera'
                    },
                    {
                        data: 'año_de_inicio',
                        name: 'año_de_inicio',
                        className: 'text-center'
                    },
                    {
                        data: 'nombre_ciclo',
                        name: 'nombre_ciclo',
                        className: 'text-center'
                    },
                    {
                        data: 'nombre_curso',
                        name: 'nombre_curso'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.tipodocente_curso == 1) {
                                return '<span class="badge badge-success">R</span>';
                            } else if (row.tipodocente_curso == 2) {
                                return '<span class="badge badge-info">S</span>';
                            } else {
                                return '<span class="badge badge-danger">-</span>';
                            }
                        },
                        className: 'text-center'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row.nombre_turno) {
                                return '<span class="badge badge-danger">No asig.</span>';
                            } else if (row.nombre_turno == 'Mañana') {
                                return '<span class="badge badge-primary">' + row.nombre_turno +
                                    '</span>';
                            } else if (row.nombre_turno == 'Tarde') {
                                return '<span class="badge badge-warning">' + row.nombre_turno +
                                    '</span>';
                            } else {
                                return '<span class="badge badge-danger">Sin Asig..</span>';
                            }
                        },
                        className: 'text-center'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row.codigo_aula && !row.aula_nombre) {
                                return '<span class="badge badge-danger">No asig.</span>';
                            } else {
                                return '<span class="badge text-white" style="background: linear-gradient(135deg, #017a52, #97e789);">' + row.codigo_aula +
                                    ' ' + row.aula_nombre + '</span>';
                            }
                        },
                        className: 'text-center'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row.codigo_aula && !row.aula_nombre) {
                                return '<span class="badge badge-danger">No asig.</span>';
                            } else {
                                return '<span class="badge badge-info">' + row.nom_seccion + '</span>';
                            }
                        },
                        className: 'text-center'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!row.codigo_aula || !row.nombre_turno) {
                                return '';
                            }
                            return row.acciones;
                        },
                        className: 'text-center'
                    }
                ]
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
