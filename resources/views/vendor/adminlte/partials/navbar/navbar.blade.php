@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}"
    style="background: linear-gradient(45deg, #ffffff, #dfbf9a);">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')
        {{ session('active_role_name') }}
        {{-- Custom left links --}}
        @yield('content_top_nav_left')
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{-- {{ mb_strtoupper($rol->nombre_dependencia) ?? 'Sin oficina asignada' }} --}}
            </a>
        </li>
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        <!-- Notificaciones -->

        @can('rol-documentario')
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
                    <a href="{{ route('documentario.mesapar.bandeja') }}" class="dropdown-item dropdown-footer">Ver todas</a>
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
                    <a href="{{ route('docente.bandejaDoce') }}" class="dropdown-item dropdown-footer">Ver todas Do</a>
                </div>
            </li>
        @endcan
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
