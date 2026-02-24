@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    @livewireStyles

@stop

@section('content')

    <style>
        .table thead {
            background-color: #343a40;
            color: #fff;
            text-align: center;
        }

        .table tbody td,
        .table tbody th {
            vertical-align: middle;
            text-align: center;
        }

        .heading-section {
            font-weight: bold;
            color: #2c3e50;
            font-size: 2rem;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
    </style>

    <div class="callout callout-danger mb-0">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-table"></i> TABLA DE INGRESANTES</h1>
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

    <section class="ftco-section">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-center mb-4">LISTA DE INGRESANTES POR PROCESOS</h4>
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow-sm rounded">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>FECHA</th>
                                        <th>NOMBRE DEL PROCESO</th>
                                        <th>NOMBRE DE LA MODALIDAD</th>
                                        <th>PDF</th>
                                        <th>EXCEL</th>
                                        <th>ELIMINAR NOTAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($procesosresultadoprimera as $item)
                                        <tr>
                                            <th>{{ $item->idpdfingresantes }}</th>
                                            <td>{{ $item->fecha }}</td>
                                            <td>{{ $item->nombre_proceso }}</td>
                                            <td>{{ $item->nombre_modalidad }}</td>
                                            <td>
                                                <form action="{{ route('pdf.fichaingresantes') }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <button class="btn btn-info" data-toggle="tooltip" data-placement="top"
                                                        title="Generar PDF" type="submit" id="idpdfingresa"
                                                        name="idpdfingresa" value="{{ $item->idpdfingresantes }}"><i
                                                            class="fas fa-file-pdf"></i>&nbsp;Generar
                                                        ficha</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('resultadoingresantes.excel') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="idpdfingresa"
                                                        value="{{ $item->idpdfingresantes }}">
                                                    <button class="btn btn-success" data-toggle="tooltip"
                                                        title="Exportar Excel">
                                                        <i class="fas fa-file-excel"></i>&nbsp;Excel
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('resultadoingresantes.eliminaringresantes') }}"
                                                    method="POST" class="form-eliminar">
                                                    @csrf
                                                    <input type="hidden" name="idpdfingresantes"
                                                        value="{{ $item->idpdfingresantes }}">
                                                    <button class="btn btn-danger btn-sm btn-confirm-delete" type="button"
                                                        data-toggle="tooltip" title="Eliminar nota">
                                                        <i class="fas fa-trash-alt"></i>&nbsp;Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>



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
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-confirm-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const notaId = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Una vez eliminada, no podrás recuperar el proceso y se realizara todo de nuevo.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>


@stop
