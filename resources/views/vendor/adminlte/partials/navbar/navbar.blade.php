@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}"
    style="background: linear-gradient(45deg, #ffffff, #dbb37f);">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')
        {{-- {{ session('active_role_name') }} --}}
        {{-- Custom left links --}}
        @yield('content_top_nav_left')
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                @if (session('dependencia_id') == 3)
                    <span class="d-inline-block text-truncate rol-navbar">
                        <i class="fas fa-user-plus"></i> - {{ mb_strtoupper(session('active_role_name')) ?? '-|-' }}
                    </span>
                @elseif (session('dependencia_id') == 4)
                    <span class="d-inline-block text-truncate rol-navbar">
                        <i class="fas fa-user-graduate"></i> - {{ mb_strtoupper(session('active_role_name')) ?? '-|-' }}
                    </span>
                @else
                    <span class="d-inline-block text-truncate rol-navbar">
                        <i class="fas fa-building"></i> - {{ mb_strtoupper(session('active_role_name')) ?? '-|-' }}
                    </span>
                @endif
                <style>
                    .rol-navbar {
                        max-width: 80px;
                        vertical-align: middle;
                    }

                    @media (min-width: 768px) {
                        .rol-navbar {
                            max-width: 200px;
                        }
                    }

                    @media (min-width: 992px) {
                        .rol-navbar {
                            max-width: none;
                        }
                    }
                </style>
                {{-- @if (session('dependencia_id') == 3)
                    <span class="d-inline-block d-md-inline text-truncate" style="max-width: 100%; max-width: 100px;">
                        <i class="fas fa-user-plus"></i> - {{ mb_strtoupper(session('active_role_name')) ?? '-|-' }}
                    </span>
                @endif

                @if (session('dependencia_id') != 4)
                    <span class="d-inline-block d-md-inline text-truncate" style="max-width: 100%; max-width: 100px;">
                        <i class="fas fa-building"></i> - {{ mb_strtoupper(session('active_role_name')) ?? '-|-' }}
                    </span>
                @endif --}}
            </a>
        </li>
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        <!-- Notificaciones -->

        @can('rol-documentario')
            @php
                $badge = [
                    1 => ['clase' => 'badge-soft-asistente', 'icono' => 'fas fa-user'],
                    2 => ['clase' => 'badge-soft-jefe', 'icono' => 'fas fa-crown'],
                ][(int) $tipo] ?? ['clase' => 'badge-soft-sin', 'icono' => 'fas fa-user-slash'];
            @endphp

            <li class="nav-item d-flex align-items-center mr-2">
                <span class="badge-tipo-soft {{ $badge['clase'] }}" title="{{ $tipo_usuario }}">
                    <i class="{{ $badge['icono'] }}"></i>
                    <span class="d-none d-lg-inline ml-1">{{ $tipo_usuario }}</span>
                </span>
            </li>

            <style>
                /* Base común: píldora suave, texto plomo oscuro */
                .badge-tipo-soft {
                    display: inline-flex;
                    align-items: center;
                    padding: .45em .9em;
                    border-radius: 50rem;
                    font-size: .78rem;
                    font-weight: 600;
                    letter-spacing: .2px;
                    color: #3d4852;
                    /* plomo oscuro, no negro */
                    border: 1px solid transparent;
                    box-shadow: 0 1px 2px rgba(61, 72, 82, .08);
                }

                .badge-tipo-soft i {
                    font-size: .85em;
                }

                /* Jefe: ámbar suave, en la familia de tu sistema (#f19e40) */
                .badge-soft-jefe {
                    background: linear-gradient(180deg, #fdf7ea, #faefd9);
                    border-color: #edd9ae;
                }

                .badge-soft-jefe i {
                    color: #a9762a;
                }

                /* Asistente: azul acero suave */
                .badge-soft-asistente {
                    background: linear-gradient(180deg, #eef4f9, #e6eef5);
                    border-color: #cfdeea;
                }

                .badge-soft-asistente i {
                    color: #2f6b8c;
                }

                /* Sin asignar: gris neutro suave */
                .badge-soft-sin {
                    background: linear-gradient(180deg, #f2f3f5, #eceef0);
                    border-color: #dcdfe3;
                }

                .badge-soft-sin i {
                    color: #78828c;
                }
            </style>

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span id="badge-alerts"
                        class="badge badge-danger navbar-badge">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"
                        id="noti-count">{{ $cont_est[0]->cont_estado == 0 ? '0' : $cont_est[0]->cont_estado }}
                        notificaciones- -</span>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('documentario.mesapar.bandeja') }}" class="dropdown-item dropdown-footer">Ver
                        todas</a>
                </div>
            </li>
        @endcan

        @can('rol-docente')
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span id="badge-alerts"
                        class="badge badge-danger navbar-badge">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"
                        id="noti-count">{{ $cont_est[0]->cont_estado == 0 ? '0' : $cont_est[0]->cont_estado }}
                        notificaciones</span>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('documentario.mesapar.bandeja') }}" class="dropdown-item dropdown-footer">Ver
                        todas..</a>
                    {{-- <a href="{{ route('docente.bandejaDoce') }}" class="dropdown-item dropdown-footer">Ver todas no.</a> --}}
                </div>
            </li>
        @endcan

        {{-- @can('rol-egresado')
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span id="badge-alerts"
                        class="badge badge-danger navbar-badge">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"
                        id="noti-count">{{ $cont_est[0]->cont_estado == 0 ? '0' : $cont_est[0]->cont_estado }}
                        notificaciones</span>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('docente.bandejaDoce') }}" class="dropdown-item dropdown-footer">Ver todas no.</a>
                </div>
            </li>
        @endcan --}}
        {{-- @if (session('dependencia_id') != 1)
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    @dump($cont_est[0]->cont_estado)
                    <span id="badge-alerts"
                        class="badge badge-danger navbar-badge">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"
                        id="noti-count">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}
                        notificaciones</span>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('documentario.mesapar.bandeja') }}" class="dropdown-item dropdown-footer">Ver todas</a>
                </div>
            </li>
        @endif --}}
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if (Auth::user())
            @if (config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if ($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
