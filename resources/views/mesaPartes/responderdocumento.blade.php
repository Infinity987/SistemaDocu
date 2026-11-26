@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')

<style>
    /* 1. Mejorar la legibilidad general */
    body, .form-control, .form-select, .btn {
        font-size: 1.05rem !important; /* Un poco más grande que el estándar */
    }

    /* 2. Hacer que los campos de texto sean más obvios y fáciles de clickear */
    .form-control, .form-select {
        border-width: 2px !important;
        border-color: #dee2e6 !important;
        padding: 0.75rem 1rem !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd !important; /* Azul fuerte al hacer clic */
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
    }

    /* 3. Etiquetas (Labels) muy claras */
    .form-label {
        font-weight: 600 !important;
        color: #212529 !important; /* Gris muy oscuro, casi negro */
        margin-bottom: 0.4rem !important;
    }

    /* 4. Botones de selección (Radio) más grandes y amigables */
    .btn-check:checked + .btn-outline-primary,
    .btn-check:checked + .btn-outline-success,
    .btn-check:checked + .btn-outline-info,
    .btn-check:checked + .btn-outline-warning {
        border-width: 2px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
    }

    /* 5. Zona de carga de archivos más invitante */
    .border-dashed {
        border-color: #0d6efd !important;
        background-color: #f8f9fa !important;
        cursor: pointer;
    }
    .border-dashed:hover {
        background-color: #e9ecef !important;
        border-color: #0a58ca !important;
    }
</style>


    <div class="callout callout-danger">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-sign-in-alt"></i> <i class="fas fa-inbox"></i> - Formulario de respuesta</h1>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">inicio</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>

@stop

@section('content')
    @livewire('responder-documento', [
        'documento' => $documento,
        'dependencias' => $dependencias,
        'detalledocumento' => $detalledocumento,
        'id_depen' => $depen,
    ])


@stop

@section('css')

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
    @livewireStyles
@stop


@section('js')

    <script>
        window.dependenciaId = {{ $depen }}; // Esto se usa dentro de app.js
    </script>
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    
    @livewireScripts

    <script>
        window.dependenciaId = {{ $depen }}; // Esto se usa dentro de app.js
    </script>
    @vite('resources/js/app.js')

    <script>
        let dependenciaId = {{ $depen }};
        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.DocumentoRecibido', (e) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                            var audio = new Audio('{{ asset('sound/noti.mp3') }}'); // Ruta sonido
                            audio.play();
                        },
                        didClose: () => {
                            $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                                .cont_estados[0].cont_estado);
                            $('#badgeNotificaciones1').text(e.cont_estados[0].cont_estado == 0 ?
                                '' : e.cont_estados[0]
                                .cont_estado);
                            $('#badgeNotificaciones2').text(e.cont_estados[1].cont_estado == 0 ?
                                '' : e.cont_estados[1]
                                .cont_estado);
                            $('#badgeNotificaciones3').text(e.cont_estados[2].cont_estado == 0 ?
                                '' : e.cont_estados[2]
                                .cont_estado);
                            // $('#badgeVerde').text(e.cont_fechas[0].verde);
                            // $('#badgeAmarillo').text(e.cont_fechas[0].amarillo);
                            // $('#badgeRojo').text(e.cont_fechas[0].rojo);
                            console.log('recargar datatable ahora peroj jjjjj');

                            $('#datatablesS').DataTable().ajax.reload();
                        }
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Nuevo documento recibido"
                    });
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.editarDocumento', (e) => {
                    $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                    $('#badgeNotificaciones1').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                    $('#badgeNotificaciones2').text(e.cont_estados[1].cont_estado == 0 ? '' : e.cont_estados[1]
                        .cont_estado);
                    $('#badgeNotificaciones3').text(e.cont_estados[2].cont_estado == 0 ? '' : e.cont_estados[2]
                        .cont_estado);
                    $('#badgeVerde').text(e.cont_fechas[0].verde);
                    $('#badgeAmarillo').text(e.cont_fechas[0].amarillo);
                    $('#badgeRojo').text(e.cont_fechas[0].rojo);
                    $('#datatablesS').DataTable().ajax.reload();
                });
        });
    </script>

@stop
