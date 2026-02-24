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
            title="{{ !empty($nom_carre) ? $nom_carre : '' }}">
            {{ !empty($nom_carre) ? $nom_carre : '' }}
        </span>
    </div>
</li>
