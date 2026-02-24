@extends('adminlte::page')

@section('title', 'DATOS PRE')

@section('content_header')

    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    <div class="callout callout-danger mb-0 estiTitulo">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-users"></i> - TABLA DATOS
                            @can('admin.verpostulantes')
                                POSTULANTES
                            @endcan

                            @can('admin.users.index')
                                ESTUDIANTES
                            @endcan
                        </h1>
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
    <div class="row mt-3 mb-3">
        <div class="col-md-12 shadow-lg mb-4 p-2">
            <div class="table-responsive">
                <table id="example1" class="table table-hover">
                    <thead style="background: linear-gradient(180deg, #6e3904, #a8702f);">
                        <tr class="text-white text-center">
                            <th>N° DNI</th>
                            <th>APELLIDOS Y NOMBRES</th>
                            <th># CELULAR</th>
                            <th>CORREO</th>
                            <th>FECHA INSCRIPCION</th>
                            @role('admision')
                                <th>PROGRAMA DE ESTUDIO</th>
                            @endrole
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        //tabla postu
        function eliminarPostulante(id) {
            Swal.fire({
                title: '¿Eliminar Postulante?',
                text: 'Esta acción eliminará al postulante registrado',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const rutaEliminar = "{{ route('eliminarPostulante', ['idpostulante' => 'ID_REEMPLAZO']) }}";
                    const urlEliminar = rutaEliminar.replace('ID_REEMPLAZO', id);

                    const btn = $(`#btnEliminar_${id}`);
                    const originalContent = btn.html();

                    // Mostrar spinner
                    btn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );
                    $.ajax({
                        url: urlEliminar,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: response.icon || 'info',
                                title: response.title || 'Alerta',
                                text: response.mensaje || 'Postulante eliminado correctamente',
                                // timer: 1500,
                                showConfirmButton: true
                            });
                            $('#example1').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: response.title || 'Alerta',
                                text: response.mensaje || 'Error al eliminar',
                            });
                        },
                        complete: function() {
                            // Restaurar contenido original del botón
                            btn.html(originalContent);
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            let userRole = "{{ Auth::user()->getRoleNames()->first() }}";
            let rutaEditar = "{{ route('verDetalle.postulante', ['idpostulante' => 'ID_REEMPLAZO']) }}";

            if (userRole === "admision") {
                $('#example1').DataTable({
                    autoWidth: false,
                    aaSorting: [],
                    language: {
                        processing: `
                                    <div style="font-size: 18px; font-weight: bold; color: #1b566f;">
                                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 40px;"></i>
                                        Cargando...
                                    </div>
                                `,
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "Ningún valor encontrado",
                        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
                        infoEmpty: "Ningún valor encontrado",
                        infoFiltered: "(filtrados desde _MAX_ registros totales)",
                        search: "Buscar:",
                        loadingRecords: "Cargando...",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                        emptyTable: "Ningun registro en la tabla",
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('ajaxPostulantes') }}",
                    columns: [{
                            data: 'idpostulante',
                            name: 'idpostulante',
                            render: function(data, type, row) {
                                return '<div class=""><p class="fw-semibold text-dark">' +
                                    '' + data + '</p></div>';
                            }
                        }, {
                            data: "nombre_postu",
                            name: 'nombre_postu',
                            render: function(data, type, row) {
                                return '<div class=""><p class="fw-semibold text-dark">' +
                                    '' + data + '</p></div>';
                            }
                        },

                        {
                            data: "celular",
                            name: 'celular',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: "correo",
                            name: 'correo',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: "fecha_inscripcion",
                            name: 'fecha_inscripcion',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: "nombre_de_carrera",
                            name: 'nombre_de_carrera',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: 'idpostulante',
                            name: 'acciones',
                            orderable: false,
                            render: function(data, type, row) {
                                let url = rutaEditar.replace('ID_REEMPLAZO', data);

                                return `<a href="${url}" title="Editar" class="btn btn-warning btn-sm m-1">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <button id="btnEliminar_${data}" title="Eliminar" class="btn btn-sm btn-danger" onclick="eliminarPostulante(${data})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>`;
                            }
                        }
                    ],
                    autoWidth: false,
                    columnDefs: [{
                        targets: 0,
                        width: "80px",
                        searchable: true
                    }, {
                        targets: 1,
                        width: "400px",
                        searchable: true
                    }, {
                        targets: 2,
                        width: "80px",
                        searchable: true
                    }, {
                        targets: 3,
                        width: "200px",
                        searchable: true
                    }, {
                        targets: 4,
                        width: "120px",
                        searchable: false
                    }, {
                        targets: 5,
                        width: "200px",
                        searchable: true
                    }, {
                        targets: 6,
                        width: "40px",
                        searchable: false
                    }],
                    responsive: true,
                });
            } else {
                $('#example1').DataTable({
                    autoWidth: false,
                    aaSorting: [],
                    language: {
                        processing: `
                                    <div style="font-size: 18px; font-weight: bold; color: #1b566f;">
                                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 40px;"></i>
                                        Cargando...
                                    </div>
                                `,
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "Ningún valor encontrado",
                        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
                        infoEmpty: "Ningún valor encontrado",
                        infoFiltered: "(filtrados desde _MAX_ registros totales)",
                        search: "Buscar:",
                        loadingRecords: "Cargando...",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                        emptyTable: "Ningun registro en la tabla",
                    },
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('ajaxPostulantes') }}",
                    columns: [{
                            data: 'idpostulante',
                            name: 'idpostulante',
                            render: function(data, type, row) {
                                return '<div class=""><p class="fw-semibold text-dark">' +
                                    '' + data + '</p></div>';
                            }
                        }, {
                            data: "nombre_postu",
                            name: 'nombre_postu',
                            render: function(data, type, row) {
                                return '<div class=""><p class="fw-semibold text-dark">' +
                                    '' + data + '</p></div>';
                            }
                        },

                        {
                            data: "celular",
                            name: 'celular',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: "correo",
                            name: 'correo',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: "fecha_inscripcion",
                            name: 'fecha_inscripcion',
                            render: function(data, type, row) {
                                return '<p>' + data + '</p>';
                            }
                        },
                        {
                            data: 'idpostulante',
                            name: 'acciones',
                            orderable: false,
                            render: function(data, type, row) {
                                let url = rutaEditar.replace('ID_REEMPLAZO', data);

                                return `<a href="${url}" title="Editar" class="btn btn-warning btn-sm m-1">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <button id="btnEliminar_${data}" title="Eliminar" class="btn btn-sm btn-danger" onclick="eliminarPostulante(${data})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>`;
                            }
                        }
                    ],
                    autoWidth: false,
                    columnDefs: [{
                        targets: 0,
                        width: "80px",
                        searchable: true
                    }, {
                        targets: 1,
                        width: "400px",
                        searchable: true
                    }, {
                        targets: 2,
                        width: "80px",
                        searchable: true
                    }, {
                        targets: 3,
                        width: "200px",
                        searchable: true
                    }, {
                        targets: 4,
                        width: "120px",
                        searchable: false
                    }, {
                        targets: 5,
                        width: "40px",
                        searchable: false
                    }],
                    responsive: true,
                });
            }
        });
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
    </script>
@stop
