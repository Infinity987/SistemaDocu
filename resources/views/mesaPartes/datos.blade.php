@extends('adminlte::page')

@section('title', 'Datos')

@section('content_header')
    @can('documentario.mesapar.index')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-calendar-week"></i> - DATOS DEL USUARIO</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">inicio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('documentario.mesapar.index')
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-2 mb-2">
                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#exampleModal">
                        <i class="fas fa-pencil-alt"></i> Editar datos
                    </button>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#exampleModalp">
                        <i class="fas fa-key"></i> Cambiar contraseña
                    </button>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="card card-outline card-brown">
                        <div class="card-header bg-gradient-brown d-flex align-items-center"
                            style="background: linear-gradient(135deg, #924900, #d49d5e);">
                            <h3 class="card-title mb-0 text-white">
                                <i class="fas fa-user-circle mr-2"></i> Datos Personales
                            </h3>
                        </div>

                        <div class="card-body form-brown">
                            <!-- DNI -->
                            <div class="form-group">
                                <label for="dni"><i class="fas fa-id-card"></i> Número de DNI</label>
                                <input type="text" value="{{ $dni }}" class="form-control" id="dni"
                                    placeholder="Ingrese su DNI" readonly>
                            </div>

                            <!-- Apellidos -->
                            <div class="form-group">
                                <label for="apellidos"><i class="fas fa-user-friends"></i> Apellidos y Nombres</label>
                                <input type="text" value="{{ $datos->nombre }}" class="form-control" id="apellidos"
                                    placeholder="Ingrese sus apellidos" readonly>
                            </div>

                            <!-- Celular -->
                            <div class="form-group">
                                <label for="celular"><i class="fas fa-phone"></i> Número de celular</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" value="{{ $datos->num_celualr }}" class="form-control" id="celular"
                                        placeholder="Ingrese su celular" readonly>
                                </div>
                            </div>

                            <!-- Correo -->
                            <div class="form-group">
                                <label for="correo"><i class="fas fa-envelope"></i> Correo electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" value="{{ $datos->correo }}" class="form-control" id="correo"
                                        placeholder="Ingrese su correo" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-header bg-gradient-success"
                            style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <h3 class="card-title mb-0 text-white">
                                <i class="fas fa-user-shield mr-2"></i> Delegación de Funciones
                            </h3>
                        </div>
                        <div class="card-body">
                            @php
                                // Verificamos si el usuario logueado es JEFE (tipo_usuario = 2) en su dependencia actual
                                $esJefe = DB::connection('mysql_documentario')
                                    ->table('dependencia_user')
                                    ->where('user_id', Auth::id())
                                    ->where('dependencia_id', session('dependencia_id'))
                                    ->where('tipo_usuario', 2)
                                    ->where('estado', 1)
                                    ->exists();

                                // Obtenemos el estado actual del permiso
                                $estadoPermiso =
                                    DB::connection('mysql_documentario')
                                        ->table('dependencia_user')
                                        ->where('user_id', Auth::id())
                                        ->where('dependencia_id', session('dependencia_id'))
                                        ->where('estado', 1)
                                        ->where('tipo_usuario', 2)
                                        ->value('dar_permiso') ?? 0;
                            @endphp

                            @if ($esJefe)
                                <p class="text-muted mb-3">
                                    Active esta opción para permitir que sus <strong>asistentes</strong> puedan enviar o
                                    responder documentos en nombre de esta jefatura.
                                </p>

                                <div
                                    class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success mb-2">
                                    <input type="checkbox" class="custom-control-input" id="switchPermisoAsistentes"
                                        {{ $estadoPermiso == 1 ? 'checked' : '' }} data-user-id="{{ Auth::id() }}">
                                    <label class="custom-control-label font-weight-bold" for="switchPermisoAsistentes">
                                        {{ $estadoPermiso == 1 ? 'Permiso ACTIVO' : 'Permiso INACTIVO' }}
                                    </label>
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Los cambios se guardan
                                    automáticamente.</small>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-lock text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Esta opción solo está disponible para el <strong>Jefe del
                                            Área</strong>.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
                        @livewire('update-users', ['id' => $datos->id_users])
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
                        @livewire('update-password', ['id' => $datos->id_users])
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />

    <style>
        .form-brown .form-control {
            border: 1px solid #8B5E3C;
            /* Marrón */
            background-color: #fdfaf7;
            /* Beige claro */
            color: #4a2c1a;
            /* Marrón oscuro */
        }

        .form-brown .form-control:focus {
            border-color: #a97455;
            box-shadow: 0 0 0 0.2rem rgba(139, 94, 60, 0.25);
        }

        .form-brown label {
            color: #5a3d2b;
            font-weight: 600;
        }

        .form-brown .input-group-text {
            background-color: #8B5E3C;
            color: #fff;
            border: none;
        }
    </style>


    @livewireStyles
@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var modal = document.getElementById('exampleModal');
                if (!modal) {
                    return;
                }

                var userId = @json($datos->id_users);
                modal.addEventListener('shown.bs.modal', function() {
                    $wire.dispatch('refreshUserData', userId);
                });
            }, 500);
        });
    </script>

    <script>
        $('#switchPermisoAsistentes').on('change', function() {
            let isChecked = $(this).is(':checked');
            let status = isChecked ? 1 : 0;
            let $switch = $(this);
            let $label = $(this).next('label');

            // Bloquear el switch temporalmente mientras se guarda
            $switch.prop('disabled', true);

            $.ajax({
                url: "{{ route('documentario.usuario.togglePermiso') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar el texto de la etiqueta
                        $label.text(status == 1 ? 'Permiso ACTIVO' : 'Permiso INACTIVO');

                        // SweetAlert de éxito
                        Swal.fire({
                            title: status == 1 ? '¡Permiso Activado!' : 'Permiso Desactivado',
                            text: response.message,
                            icon: status == 1 ? 'success' : 'warning',
                            confirmButtonColor: status == 1 ? '#28a745' : '#dc3545',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        // Revertir el switch si falló
                        $switch.prop('checked', !isChecked);
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    // Revertir el switch si hubo error de red
                    $switch.prop('checked', !isChecked);
                    Swal.fire('Error de Conexión', 'No se pudo guardar el cambio. Intente nuevamente.',
                        'error');
                },
                complete: function() {
                    // Desbloquear el switch
                    $switch.prop('disabled', false);
                }
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
@stop
