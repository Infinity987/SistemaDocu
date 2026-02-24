@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

    <div class="callout callout-danger shadow">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 id="titulo-rol">
                            <i class="fas fa-user-edit"></i> - ASIGNAR ROL Y EDITAR
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
                            <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                    href="{{ route('admin.users.index') }}">Usuarios</a></li>
                            <li class="breadcrumb-item active">Asig. rol</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>
@stop

@section('content')

    @if (session('info'))
        <div class="alert alert-success">
            <strong>{{ session('info') }}</strong>
        </div>
    @else
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-8 mb-2">
                <input type="text" class="form-control" id="namedate" aria-describedby="emailHelp"
                    value="{{ $use->nombre ?? 'vacio' }}" readonly>
            </div>
            <div class="col-sm-4">
                <div class="container-fluid">
                    <div class="row justify-content-end">
                        <div class="col-sm-6 mb-2">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                                data-target="#exampleModal">
                                <i class="fas fa-pencil-alt"></i> Editar usuario
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                                data-target="#exampleModalp">
                                <i class="fas fa-key"></i> Editar contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-list-ul"></i> Listado de Roles</h5>
            {!! Form::model($use, ['route' => ['admin.users.update', $use->id], 'method' => 'put']) !!}
            @foreach ($roles as $role)
                @if ($role->name != 'postulante' && $role->name != 'alumno' && $role->name != 'egresado')
                    <div>
                        <label>
                            {!! Form::checkbox('roles[]', $role->id, null, ['class' => 'mr-1']) !!}
                            {{ $role->name }}
                        </label>
                    </div>
                @endif
            @endforeach
            {!! Form::button('<i class="fas fa-save"></i> Asignar rol', [
                'type' => 'submit',
                'class' => 'btn btn-primary mt-2',
            ]) !!}

            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-user"></i> Editar Usuario</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @livewire('update-users', ['id' => $use->id])
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="exampleModalp">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-key"></i> Editar Contraseña</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @livewire('update-password', ['id' => $use->id])
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var modal = document.getElementById('exampleModal');
                if (!modal) {
                    return;
                }

                var userId = @json($use->id);

                modal.addEventListener('shown.bs.modal', function() {
                    $wire.dispatch('refreshUserData', userId);
                });
            }, 500);
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
            Livewire.on('mensaje', (event) => {
                document.getElementById('namedate').value = event[0].name;
                Swal.fire({
                    title: "BUEN TRABAJO!",
                    text: event[0].msm,
                    icon: "success"
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".mause").hover(
                function() {
                    $(this).css("color", "#ba9643");
                },
                function() {
                    $(this).css("color", "#4a3911");
                }
            );
        });
    </script>
@stop
