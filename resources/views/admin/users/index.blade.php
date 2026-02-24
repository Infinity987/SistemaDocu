@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    @can('admin.users.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fa fa-users" aria-hidden="true"></i> - LISTA DE USUARIOS</h1>
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
        <div class="container-fluid mb-2">
            <div class="row">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-user" aria-hidden="true"></i> <i class="fa fa-plus" aria-hidden="true"></i> Registrar
                        Usuario
                    </button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> Registrar Usuario <i
                                class="fas fa-list"></i> </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @livewire('register-userss')
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        @livewire('admin.users-index')
        <div id="modal-confirmacion" style="display: none;"></div>
    @endcan
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('mensaje', (event) => {
                Swal.fire({
                    title: "BUEN TRABAJO!",
                    text: event[0].msm,
                    icon: "success"
                });
            });
        });
    </script>

    <script>
        $('#dni').on('keypress', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
            }
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('mostrar-modal-confirmacion', () => {
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
                        Livewire.dispatch('eliminar');
                    }
                });
            });

            Livewire.on('alerta-exito', (datos) => {
                const {
                    titulo,
                    mensaje,
                    icono
                } = datos[0]; // Extraer desde el primer elemento del array

                Swal.fire({
                    title: titulo,
                    text: mensaje,
                    icon: icono,
                    confirmButtonText: 'Aceptar'
                });
            });
        });
    </script>
@stop
