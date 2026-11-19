@extends('adminlte::page')

@section('content_header')
    @can('admin.users.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fas fa-fw fa-building" aria-hidden="true"></i> - Gestión de Oficinas</h1>
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
                                <li class="breadcrumb-item active">Oficinas</li>
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

        <div class="container-fluid">

            {{-- Mensajes --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            {{-- Tarjeta principal --}}
            <div class="card office-card">

                <div class="card-header office-card-header">
                    <div>
                        <h3 class="card-title">
                            <i class="fas fa-list-ul mr-2"></i>
                            Listado de Oficinas
                        </h3>
                        <small>Consulta, busca y administra las oficinas registradas.</small>
                    </div>

                    <a href="{{ route('roles.create') }}" class="btn btn-register">
                        <i class="fas fa-plus mr-1"></i>
                        Registrar nueva oficina
                    </a>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tabla-oficinas" class="table table-hover table-bordered office-table w-100">

                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Nombre de la Oficina / Cargo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td class="text-center">
                                            <span class="id-badge">
                                                #{{ $role->id }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="office-name">
                                                <div class="office-name-icon">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <span>{{ $role->name }}</span>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <div class="action-buttons">

                                                <a href="{{ route('roles.edit', $role->id) }}" class="btn-action btn-edit"
                                                    title="Editar oficina">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                    class="form-delete">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn-action btn-delete"
                                                        title="Eliminar oficina">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>

                                                </form>


                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="card-footer office-footer">
                    <i class="fas fa-info-circle mr-1"></i>
                    Administración de oficinas y cargos registrados.
                </div>

            </div>

        </div>

    @endcan
@endsection


@section('css')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        /* ENCABEZADO */

        .office-header {
            background: linear-gradient(135deg, #243B55, #141E30);
            color: #fff;
            padding: 22px 25px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .office-header-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 255, 255, .13);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            margin-right: 16px;
        }

        .office-header h1 {
            font-size: 23px;
            font-weight: 700;
            margin: 0;
        }

        .office-header p {
            margin: 5px 0 0;
            color: #cbd5e1;
            font-size: 13px;
        }

        .office-header-badge {
            background: rgba(255, 255, 255, .12);
            padding: 9px 15px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* TARJETA */

        .office-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .07);
            overflow: hidden;
        }

        .office-card-header {
            background: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid #edf0f4;
        }

        .office-card-header .card-title {
            float: none;
            font-size: 17px;
            font-weight: 700;
            color: #27364b;
            margin: 0;
        }

        .office-card-header small {
            display: block;
            margin-top: 5px;
            color: #8893a2;
            font-size: 12px;
        }

        .btn-register {
            background: #198754;
            color: #fff !important;
            border-radius: 7px;
            padding: 10px 17px;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .btn-register:hover {
            background: #146c43;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, .2);
        }

        /* TABLA */

        .office-table {
            border-collapse: separate !important;
            border-spacing: 0;
            margin-top: 10px !important;
        }

        .office-table thead th {
            background: #263b59;
            color: #fff;
            border: none !important;
            padding: 15px 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            vertical-align: middle;
        }

        .office-table tbody td {
            padding: 13px 12px;
            vertical-align: middle !important;
            color: #465367;
            font-size: 13px;
            border-color: #edf0f4 !important;
        }

        .office-table tbody tr {
            transition: background .2s;
        }

        .office-table tbody tr:hover {
            background: #f5f8fc !important;
        }

        .office-table tbody tr:nth-child(even) {
            background-color: #fafbfd;
        }

        /* IDENTIFICADOR */

        .id-badge {
            background: #e8f1ff;
            color: #2563a6;
            padding: 6px 11px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
        }

        /* NOMBRE */

        .office-name {
            display: flex;
            align-items: center;
            gap: 11px;
            font-weight: 600;
            color: #34445a;
        }

        .office-name-icon {
            width: 34px;
            height: 34px;
            background: #edf3fb;
            color: #42658d;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* BOTONES */

        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
        }

        .form-delete {
            display: inline-flex;
            margin: 0;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 7px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-edit {
            background: #fff4dc;
            color: #d69b16;
        }

        .btn-edit:hover {
            background: #f3b632;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #fdeaea;
            color: #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
            transform: translateY(-2px);
        }

        /* DATATABLES */

        .dataTables_wrapper {
            font-size: 13px;
            color: #596579;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dce2ea;
            border-radius: 6px;
            padding: 6px 10px;
            outline: none;
            color: #49566a;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #4a83bd;
            box-shadow: 0 0 0 3px rgba(74, 131, 189, .1);
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 7px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 5px !important;
            border: none !important;
            margin: 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #263b59 !important;
            color: white !important;
            border: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e8f1ff !important;
            color: #263b59 !important;
            border: none !important;
        }

        .office-footer {
            background: #fafbfd;
            color: #8893a2;
            font-size: 12px;
            padding: 13px 20px;
        }

        /* RESPONSIVE */

        @media (max-width: 767px) {

            .office-header {
                padding: 17px;
            }

            .office-header h1 {
                font-size: 18px;
            }

            .office-header-icon {
                width: 42px;
                height: 42px;
                font-size: 19px;
                margin-right: 10px;
            }

            .office-header-badge {
                display: none;
            }

            .office-card-header {
                align-items: flex-start;
            }

            .btn-register {
                width: 100%;
                text-align: center;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left !important;
                float: none !important;
                width: 100%;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 65%;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: center !important;
                float: none !important;
                margin-top: 10px;
            }
        }
    </style>

@stop


@section('js')

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            $('#tabla-oficinas').DataTable({

                language: {
                    processing: "Procesando...",
                    search: "",
                    searchPlaceholder: "Buscar oficina...",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ oficinas",
                    infoEmpty: "Mostrando 0 a 0 de 0 oficinas",
                    infoFiltered: "(filtrado de _MAX_ oficinas)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron oficinas",
                    emptyTable: "No hay oficinas registradas",

                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    }
                },
                ordering: false,

                pageLength: 10,

                lengthMenu: [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, "Todos"]
                ],

                responsive: true,
                autoWidth: false,

                columnDefs: [{
                    targets: 2,
                    orderable: true,
                    searchable: false
                }],

                dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                    '<"row"<"col-12"tr>>' +
                    '<"row mt-3"<"col-md-5"i><"col-md-7"p>>'

            });

            // CONFIRMAR ELIMINACIÓN
            $(document).on('submit', '.form-delete', function(e) {

                e.preventDefault();

                let formulario = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta oficina será eliminada permanentemente.',
                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',

                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',

                    reverseButtons: true,
                    focusCancel: true,

                    customClass: {
                        popup: 'swal-office-popup'
                    }

                }).then((result) => {

                    if (result.isConfirmed) {
                        formulario.submit();
                    }

                });

            });


            // MENSAJE DE ÉXITO
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación exitosa!',
                    text: @json(session('success')),
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Aceptar',
                    timer: 3500,
                    timerProgressBar: true
                });
            @endif


            // MENSAJE DE ERROR
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Atención!',
                    text: @json(session('error')),
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Aceptar'
                });
            @endif

        });
    </script>

@stop
