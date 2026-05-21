<li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"
        @if (config('adminlte.sidebar_collapse_remember')) data-enable-remember="true" @endif
        @if (!config('adminlte.sidebar_collapse_remember_no_transition')) data-no-transition-after-reload="false" @endif
        @if (config('adminlte.sidebar_collapse_auto_size')) data-auto-collapse-size="{{ config('adminlte.sidebar_collapse_auto_size') }}" @endif>
        <i class="fas fa-bars"></i>
        <span class="sr-only">{{ __('adminlte::adminlte.toggle_navigation') }}</span>
    </a>
</li>

<li class="nav-item">

    <div class="nav-link" href="#">
        <span class="d-inline-block d-md-inline text-truncate" style="max-width: 100%; max-width: 120px;"
            title="">
            @if (session('dependencia_id') == 4)
                <i class="fas fa-user-graduate"></i> -
                {{ !empty($nom_carre) ? mb_strtoupper($nom_carre, 'UTF-8') : '' }}
            @endif

            @if (session('dependencia_id') == 5)
                <i class="fas fa-graduation-cap"></i> -
                {{ !empty($nom_carre) ? mb_strtoupper($nom_carre, 'UTF-8') : '' }}
            @endif
        </span>
    </div>
</li>
