@extends('adminlte::page')

@section('title', 'Editar usuarios')

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
    @endif
    @if (session('error'))
        <div class="alert alert-danger"><strong>{{ session('error') }}</strong></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
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

            <div class="row small font-weight-bold text-muted border-bottom pb-1 mb-1">
                <div class="col-7">ROL / DEPENDENCIA</div>
                <div class="col-5">TIPO DE USUARIO</div>
            </div>

            @foreach ($roles as $role)
                @if (!in_array($role->name, ['postulante', 'alumno', 'egresado']))
                    @php
                        $tieneRol = $use->roles->contains($role->id);
                        $tipoActual = $tiposUsuario[$role->id] ?? 1;
                    @endphp
                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-7">
                            <label class="mb-0">
                                {!! Form::checkbox('roles[]', $role->id, $tieneRol, [
                                    'class' => 'mr-1 chk-rol',
                                    'data-rol' => $role->id,
                                ]) !!}
                                {{ $role->name }}
                            </label>
                        </div>
                        <div class="col-5">
                            <select name="tipo_usuario[{{ $role->id }}]" class="form-control form-control-sm sel-tipo"
                                data-rol="{{ $role->id }}" {{ $tieneRol ? '' : 'disabled' }}>
                                <option value="1" {{ $tipoActual == 1 ? 'selected' : '' }}>Asistente</option>
                                <option value="2" {{ $tipoActual == 2 ? 'selected' : '' }}>Jefe</option>
                            </select>
                        </div>
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

    {{-- <div class="card mt-4">
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
    </div> --}}

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

        // El select de tipo solo se habilita si el rol está marcado
        $(document).on('change', '.chk-rol', function() {
            $('.sel-tipo[data-rol="' + $(this).data('rol') + '"]').prop('disabled', !this.checked);
        });
    </script>
@stop
