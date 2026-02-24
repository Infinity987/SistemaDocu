@extends('adminlte::page')

@section('title', 'Padron')

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
                        <h1><i class="fas fa-indent"></i>
                            MODULO DE PADRON</h1>
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

    <div class="container-fluid">
    <div class="row mb-4">
            <div class="col-sm-3">
                <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                    data-target="#formModal">
                    <i class="fas fa-plus"></i> REALIZAR PADRON POR PROCESO
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-table"></i> Tabla de Padrones
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPadron" class="table table-hover table-bordered table-striped text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Proceso</th>
                                <th>Modalidad</th>
                                <th>Postulantes</th>
                                <th>Aulas</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($padron as $item)
                                <tr>
                                    <td>{{ $item->id_padron }}</td>
                                    <td>{{ $item->nombre_proceso }}</td>
                                    <td>{{ $item->nombre_modalidad }}</td>
                                    <td>{{ $item->cantidad_postulantes }}</td>
                                    <td>{{ $item->numero_de_aulas }}</td>
                                    <td>{{ $item->fecha }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('pdf.fichapadron') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <button class="btn btn-info btn-sm" type="submit" name="idpadron"
                                                    value="{{ $item->id_padron }}">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            </form>
                                            <form id="formEliminar{{ $item->id_padron }}"
                                                action="{{ route('padron.eliminar') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="idpadron" value="{{ $item->id_padron }}">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmarEliminacion({{ $item->id_padron }})">
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
        </div>

        <!-- /.card-header -->
    </div>
    <!-- /.card-body -->





    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de Padron</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('padron.generaraulas') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="col-sm-12 mb-3">
                            <label for="lugar_dominicio" class="form-label">PROCESOS DE PADRON:</label>
                            <livewire:selecpadron />
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
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


        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará el registro de forma permanente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formEliminar' + id).submit();
                }
            });
        }
    </script>




    @livewireScripts
@stop
