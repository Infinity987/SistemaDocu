@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    <style>
        /* Estilo para inputs readonly */
        .input-readonly {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            font-weight: bold;
        }

        /* Estilo para inputs editables */
        .input-editable {
            background-color: #ffffff;
        }

        /* Encabezado institucional */
        .bg-institucional {
            background-color: #004080;
            /* Ajusta al color institucional */
            color: white;
            padding: 8px;
            border-radius: 4px;
        }


        .tabla-resultados {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 0.95rem;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        h2 {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .tabla-resultados thead th {
            position: sticky;
            top: 0;
            background-color: #004080;
            z-index: 2;
        }

        .tabla-resultados thead {
            background-color: #004080;
            /* Color institucional */
            color: white;
            text-align: center;
        }

        .tabla-resultados th,
        .tabla-resultados td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .tabla-resultados tbody tr.table-secondary td {
            background-color: #e9f1ff;
            font-weight: bold;
            font-size: 1rem;
        }

        .tabla-resultados tbody tr.table-success td {
            background-color: #e6ffe6;
        }

        .tabla-resultados tbody tr.table-danger td {
            background-color: #ffe6e6;
        }

        .tabla-resultados input[readonly],
        .tabla-resultados textarea[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            font-weight: bold;
        }

        .tabla-resultados input,
        .tabla-resultados textarea {
            font-size: 0.9rem;
        }

        .tabla-resultados .grupo-carrera {
            background: linear-gradient(to right, #004080, #0066cc);
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 12px 16px;
            border-top: 3px solid #00264d;
            border-bottom: 1px solid #ccc;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.2);
        }
    </style>

    <div class="callout callout-danger mb-0">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-poll"></i>
                            MODULO DE RESULTADOS</h1>
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
    @livewire('selecresultados')
    {{-- <div class="col-md-12"> --}}
        {{-- <div class="card card-default"> --}}
            <!-- /.card-header -->
            {{-- <div class="card-body"> --}}
                {{-- <div class="callout callout-info">
                <button type="button" class="btn btn-outline-info btn-lg btn-block">
                    REALIZAR PROCESO DE RESULTADOS
                </button>
                </div> --}}

                {{-- <div class="card"> --}}
                    {{-- <div class="card-header"> --}}
                        {{-- <div div class="color-palette-set"> --}}

                            {{-- <CENter>
                                <div class="bg-lightblue disabled color-palette"><span>TABLA DE RESULTADOS</span></div>
                            </CENter> --}}






                        {{-- </div> --}}

                    {{-- </div> --}}
                    <!-- /.card-header -->


                {{-- </div> --}}
                <!-- /.card-body -->
            {{-- </div> --}}
            <!-- /.card -->
        {{-- </div> --}}
    {{-- </div> --}}
    <input type="hidden" name="nota1" id="nota1" value="{{ session('nota1', 0) }}">
    <input type="hidden" name="nota1" id="nota23" value="{{ session('nota23', 0) }}">
    <input type="hidden" name="nota1" id="nota24" value="{{ session('nota24', 0) }}">




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
        function enviarFormulario(ruta) {
            let form = document.getElementById('formulario');
            form.action = ruta;
            form.submit();
        }
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {});

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


    @livewireScripts


@stop
