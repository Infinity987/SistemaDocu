@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if ($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if ($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if (!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if ($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')

    @vite('resources/js/app.js')

    {{-- Script Global de Notificaciones en Tiempo Real --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Verificamos que el usuario esté autenticado para evitar errores
            const userId = "{{ auth()->id() }}";
            const depenId = "{{ session('dependencia_id') }}";
            const activeRole = "{{ session('active_role_name') }}";

            if (!userId || typeof Echo === 'undefined') return;

            const notificationSound = new Audio('{{ asset('sound/noti.mp3') }}');

            const procesarNotificacion = (e) => {
                // 1. Sonido
                notificationSound.play().catch(err => console.log("Audio en espera de interacción"));

                // 2. Alerta Visual (SweetAlert2)
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: "info",
                    title: "Nuevo documento recibido",
                    text: e.message || "Revisa tu bandeja de entrada"
                });

                // 3. Actualizar el Badge del Navbar dinámicamente
                // e.cont_estados[0] suele ser el estado 'Pendiente'
                $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                    .cont_estados[0].cont_estado);
                $('#noti-count').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                    .cont_estados[0].cont_estado+' notificaciones ..');
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
                if ($.fn.DataTable.isDataTable('#datatablesS')) {
                    console.log('Recargando DataTable...');
                    $('#datatablesS').DataTable().ajax.reload(null, false);
                    // El 'false' es para que no se resetee la paginación al recargar
                }
            };

            // --- CANAL PERSONAL (Docente, Alumno, etc) ---
            Echo.private('App.Models.User.' + userId)
                .listen('.DocumentoRecibido', (e) => {
                    console.log('Notificación personal recibida');
                    procesarNotificacion(e);
                });

            Echo.private('App.Models.User.' + userId)
                .listen('.noEditarDocumento', (e) => {
                    if ($.fn.DataTable.isDataTable('#datatablesSimple')) {
                        console.log('Recargando DataTable...');
                        $('#datatablesSimple').DataTable().ajax.reload(null, false);
                        // El 'false' es para que no se resetee la paginación al recargar
                    }
                });

            // --- CANAL DE DEPENDENCIA (Oficinas) ---
            // Solo escuchamos si el rol activo es de oficina
            if (depenId && !['docente', 'alumno', 'egresado', 'postulante'].includes(activeRole)) {
                Echo.private('dependencia.' + depenId)
                    .listen('.DocumentoRecibido', (e) => {
                        console.log('Notificación de oficina recibida');
                        procesarNotificacion(e);
                    });

                Echo.private('dependencia.' + depenId)
                    .listen('.noEditarDocumento', (e) => {
                        if ($.fn.DataTable.isDataTable('#datatablesSimple')) {
                            console.log('Recargando DataTable...');
                            $('#datatablesSimple').DataTable().ajax.reload(null, false);
                            // El 'false' es para que no se resetee la paginación al recargar
                        }
                    });

            }
        });
    </script>
@stop
