@extends('adminlte::page')

@section('title', 'Inscripción')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    @livewireStyles

    <div class="callout callout-danger mb-0">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-chart-pie"></i> MODULO DE REPORTES</h1>
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

@stop

@section('content')

    <div class="accordion" id="reportesAccordion">

        <!-- Reporte de Postulantes por Carrera -->
        <div class="card">
            <div class="card-header" id="headingPostulantes">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left text-white" type="button" data-toggle="collapse"
                        data-target="#collapsePostulantes" aria-expanded="true" aria-controls="collapsePostulantes"
                        style="background-color: #007bff;">
                        📘 Reporte de Postulantes por Carrera
                    </button>
                </h2>
            </div>
            <div id="collapsePostulantes" class="collapse" aria-labelledby="headingPostulantes"
                data-parent="#reportesAccordion">
                <div class="card-body">
                    <livewire:admin.selecnumeroproceso />
                </div>
            </div>
        </div>

        <!-- Reporte de Ingresantes por Carrera -->
        <div class="card">
            <div class="card-header" id="headingIngresantes">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left text-white" type="button" data-toggle="collapse"
                        data-target="#collapseIngresantes" aria-expanded="false" aria-controls="collapseIngresantes"
                        style="background-color: #28a745;">
                        🟢 Reporte de Ingresantes por Carrera
                    </button>
                </h2>
            </div>
            <div id="collapseIngresantes" class="collapse" aria-labelledby="headingIngresantes"
                data-parent="#reportesAccordion">
                <div class="card-body">
                    <livewire:admin.selecnumeroingresantes />
                </div>
            </div>
        </div>

        <!-- Reporte de Postulantes por Carrera y Género -->
        <div class="card">
            <div class="card-header" id="headingGeneroPostulantes">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left text-white" type="button" data-toggle="collapse"
                        data-target="#collapseGeneroPostulantes" aria-expanded="false"
                        aria-controls="collapseGeneroPostulantes" style="background-color: #ff7f50;">
                        🧡 Reporte de Postulantes por Carrera y Género
                    </button>
                </h2>
            </div>
            <div id="collapseGeneroPostulantes" class="collapse" aria-labelledby="headingGeneroPostulantes"
                data-parent="#reportesAccordion">
                <div class="card-body">
                    <livewire:admin.selecpostulanteporgenero />
                </div>
            </div>
        </div>

        <!-- Reporte de Ingresantes por Carrera y Género -->
        <div class="card">
            <div class="card-header" id="headingGeneroIngresantes">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left text-white" type="button" data-toggle="collapse"
                        data-target="#collapseGeneroIngresantes" aria-expanded="false"
                        aria-controls="collapseGeneroIngresantes" style="background-color: #6f42c1;">
                        💜 Reporte de Ingresantes por Carrera y Género
                    </button>
                </h2>
            </div>
            <div id="collapseGeneroIngresantes" class="collapse" aria-labelledby="headingGeneroIngresantes"
                data-parent="#reportesAccordion">
                <div class="card-body">
                    <livewire:admin.seleingresantesporgenero />
                </div>
            </div>
        </div>

    </div>



@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>


    <script src="{{ asset('datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Bootstrap (opcional) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


    <script>
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })
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
@stop
