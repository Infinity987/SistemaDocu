@extends('adminlte::page')

@section('title', 'Reporte de Usuarios Activos')

@section('content_header')
    @can('admin.users.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fas fa-users-cog" aria-hidden="true"></i> - LISTA DE USUARIOS ACTIVOS
                            </h1>
                        </div>
                        <style>
                            @media (max-width: 767px) {
                                #titulo-rol {
                                    font-size: 20px !important;
                                    margin-top: 10px;
                                }
                            }
                        </style>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Usuarios</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('admin.users.index')
        <div class="card-body">
            {{-- Leyenda para el administrador --}}
            <div class="mb-3 p-2 bg-light rounded border">
                <small class="text-muted mr-3"><strong>Estado de Trámites Documentarios:</strong></small>
                <span class="text-success mr-3"><i class="fas fa-circle"></i> Tiene permisos (personal_base)</span>
                <span class="text-danger"><i class="fas fa-circle"></i> Sin permisos de documentario</span>
            </div>

            <table id="tablaUsuarios" class="table table-bordered table-striped table-hover dt-responsive" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 10%">DNI</th>
                        <th style="width: 20%">Nombre Completo</th>
                        <th style="width: 20%">Correo</th>
                        <th style="width: 35%">Oficinas / Cargos Asignados</th>
                        <th style="width: 15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaFinal as $usuario)
                        <tr>
                            <td><strong>{{ $usuario['dni'] }}</strong></td>
                            <td><i class="fas fa-user-circle text-muted"></i> {{ $usuario['nombre'] }}</td>
                            <td><small class="text-muted">{{ $usuario['correo'] }}</small></td>
                            <td>
                                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                    @foreach ($usuario['oficinas'] as $oficina)
                                        <div class="d-flex align-items-center mb-1">
                                            {{-- Badge de la Oficina --}}
                                            <span class="badge badge-info mr-1" style="font-size: 0.85em; padding: 5px 8px;">
                                                <i class="fas fa-building"></i> {{ $oficina['nombre'] }}
                                            </span>

                                            {{-- Badge del Tipo (Jefe/Asistente) --}}
                                            @if ($oficina['tipo'] == 'Jefe')
                                                <span class="badge badge-success mr-1" style="font-size: 0.75em;">Jefe</span>
                                            @else
                                                <span class="badge badge-warning text-dark mr-1"
                                                    style="font-size: 0.75em;">Asist.</span>
                                            @endif

                                            {{-- 🟢🔴 La Bolita de Estado (personal_base) --}}
                                            @if ($oficina['tiene_base'])
                                                <span class="text-success" title="Tiene acceso a Trámites Documentarios">
                                                    <i class="fas fa-circle" style="font-size: 10px;"></i>
                                                </span>
                                            @else
                                                <span class="text-danger" title="NO tiene acceso a Trámites Documentarios">
                                                    <i class="fas fa-circle" style="font-size: 10px;"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-xs btn-default" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endcan
@stop

@section('css')
    {{-- Estilos para que los badges se vean modernos y no rompan la fila --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .gap-1 {
            gap: 5px;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
            border-radius: 4px;
        }

        .table td {
            vertical-align: middle;
        }

        /* Ajuste para DataTables responsive */
        .dt-responsive {
            font-size: 0.9rem;
        }
    </style>
@stop

@section('js')
    {{-- Scripts de DataTables (si no los tienes cargados globalmente en tu layout) --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "infoThousands": ",",
                    "loadingRecords": "Cargando...",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },

                },
                "pageLength": 10,
                "order": false,
                "columnDefs": [{
                        "orderable": false,
                        "targets": 4
                    } // Desactivar ordenamiento en la columna "Acciones" (índice 4)
                ],
                "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip', // Diseño moderno de controles
                "responsive": true,
                "autoWidth": false
            });
        });
    </script>
@stop
