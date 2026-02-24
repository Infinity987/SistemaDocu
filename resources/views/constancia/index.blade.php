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
                        <h1><i class="fas fa-file-alt"></i> MODULO DE CONSTANCIAS DE INGRESO</h1>
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

<livewire:admin.selecconstancia />
    <div class="col-md-12">
        {{-- <div class="card card-default"> --}}
            <!-- /.card-header -->
            {{-- <div class="card-body"> --}}
                {{-- <div class="callout callout-info">
                    <button type="button" class="btn btn-outline-info btn-lg btn-block">
                    REALIZAR PROCESO DE RESULTADOS
                    </button>
                </div> --}}

                {{-- <div class="card">
                    <div class="card-header">
                        <div div class="color-palette-set"> --}}

                            {{-- <CENter>
                                <div class="bg-lightblue disabled color-palette"><span>TABLA DE RESULTADOS</span></div>
                                </CENter> --}}





                        {{-- </div> --}}
                        <!-- /.card-header -->


                    {{-- </div> --}}
                    <!-- /.card-body -->
                {{-- </div> --}}
                <!-- /.card -->
            {{-- </div> --}}
        {{-- </div> --}}

    @stop

    @section('js')
        <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
        <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

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
