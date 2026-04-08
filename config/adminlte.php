<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => '',
    'title_prefix' => 'SIA GBM | ',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => true,
    'favicon' => 'favicons/favicon.ico',

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b style="color: #EDD54C; font-weight: 900;">SIA</b> <span style="color: #DB9502; font-weight: 900;">GBM</span>',

    // 'logo_img' => 'logos/logo.png',
    'logo_img' => null,

    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'SIA. GBM',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-success',
    'usermenu_image' => false,
    'usermenu_desc' => true,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-danger',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn btn-success btn-block',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => 'hold-transition sidebar-mini',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-success elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    // 'classes_sidebar' => 'sidebar-dark-primary elevation-4',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logout_method' => 'POST',

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        // [
        //     'type' => 'navbar-search',
        //     'text' => 'search',
        //     'topnav_right' => true,
        // ],
        // [
        //     'type' => 'fullscreen-widget',
        //     'topnav_right' => true,
        // ],

        // Sidebar items:
        // [
        //     'type' => 'sidebar-menu-search',
        //     'text' => 'search',
        // ],

        //INICION ROL ADMIN
        [
            'header' => 'ADMINISTRADOR',
            'can' => 'rol-admin',
        ],
        [
            'text' => 'Docentes y Usuarios',
            'route' => 'admin.users.index',
            'icon' => 'fas fa-fw fa-user',
            'active' => ['admin/users*'],
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Encargados',
            'route' => 'encargados.index',
            'icon' => 'fas fa-user-tie',
            'active' => ['admin/encargados*'],
            'can' => 'rol-admin'
        ],
        [
            'header' => 'PROCESO MATRICULA',
            'can' => 'rol-admin',
        ],
        [
            'text' => 'Datos Estudiantes',
            'route' => 'admin.verpostulantes',
            'icon' => 'fas fa-users', // Representa grupo de personas: postulantes + alumnos
            'can' => 'rol-admin',
            'active' => ['admin/postulantes/*'],
        ],
        [
            'text' => 'Ver Malla Curricular',
            'route' => 'malla.index',
            'icon' => 'fas fa-th-list', // Representa estructura o malla de materias
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Matricula',
            'route' => 'matricula_proceso.index',
            'icon' => 'fas fa-edit', // Representa proceso de inscripción o edición
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Ver historial Alumnos',
            'route' => 'historialalumno.index',
            'icon' => 'fas fa-history', // Representa historial o registro de eventos
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Semestre Aca.',
            'route' => 'semestre.index',
            'icon' => 'fas fa-layer-group', // Ya está correcto: representa agrupación por semestres
            'can' => 'rol-admin'
        ],
        ['header' => 'ASIGNAR DOCENTE CURSO', 'can' => 'rol-admin',],
        [
            'text' => 'Asignar',
            'route' => 'asignarCurso.index',
            'icon' => 'fas fa-chalkboard-teacher',
            'can' => 'rol-admin'
        ],
        ['header' => 'HORARIO', 'can' => 'rol-admin',],
        [
            'text' => 'Asignar - Ver',
            'route' => 'horario.index',
            'icon' => 'fas fa-calendar-day',
            'active' => ['admin/horario*'],
            'can' => 'rol-admin'
        ],
        ['header' => 'REPORTE SIS. ACA', 'can' => 'rol-admin',],
        [
            'text' => 'Acta Evaluación',
            'route' => 'actaEvalu.index',
            'icon' => 'fas fa-clipboard',
            'active' => ['admin/reporte/acta*'],
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Calificaciones cursos',
            'route' => 'califiCurso.index',
            'icon' => 'fas fa-clipboard-list',
            'active' => ['admin/reporte/calificaciones*'],
            'can' => 'rol-admin'
        ],
        [
            'text' => 'Reporte notas general',
            'route' => 'notageneral.index',
            'icon' => 'fas fa-clipboard',
            'can' => 'rol-admin'
        ],
        // FIN ROL ADMIN

        // INICIO ROL ADMISON
        ['header' => 'PROCESO ADMISION', 'can' => 'rol-admision',],
        [
            'text' => 'Datos Postulantes',
            'route' => 'admin.verpostulantes',
            'icon' => 'fas fa-fw fa-user',
            'can' => 'rol-admision',
            'active' => ['admin/postulantes/*'],
        ],
        [
            'text' => 'Inscripcion',
            'route' => 'inscripcion.index',
            'icon' => 'fas fa-check-circle',
            'can' => 'rol-admision'
        ],

        [
            'text' => 'Procesos',
            'icon' => 'fas fa-id-badge',
            'submenu' => [
                [
                    'text' => 'Procesos',
                    'route' => 'procesos.index',
                    'icon' => 'fas fa-calendar-alt',
                    'can' => 'rol-admision'
                ],
                [
                    'text' => 'Cuadro de Vacantes',
                    'route' => 'procesos.indexvacantes',
                    'icon' => 'fas fa-bars',
                    'can' => 'rol-admision'
                ],


            ],
        ],
        [
            'text' => 'Padron',
            'route' => 'padron.index',
            'icon' => 'fas fa-fw fa-lock',
            'can' => 'rol-admision'
        ],
        [
            'text' => 'Resultados',
            'icon' => 'fas fa-fw fa-user',
            'submenu' => [
                [
                    'text' => 'Proceso resultado',
                    'route' => 'resultado.index',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'rol-admision'

                ],
                [
                    'text' => 'Alumnos ingresantes',
                    'route' => 'ingre.index',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'rol-admision'

                ],
                [
                    'text' => 'Ver primeranota',
                    'route' => 'resultadoprimera.index',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'rol-admision'

                ],
                [
                    'text' => 'Ver Ingresantes',
                    'route' => 'resultadoingresantes.index',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'rol-admision'

                ],
                [
                    'text' => 'Constancia de Ingreso',
                    'route' => 'constancia.index',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'rol-admision'

                ],

            ],
        ],

        [
            'text' => 'Reportes',
            'route' => 'Reportes.index',
            'icon' => 'fas fa-fw fa-lock',
            'can' => 'rol-admision'
        ],
        // FIN ROL ADMISION



        ['header' => 'ACCESO', 'can' => 'postulante.index',],
        [
            'text' => 'Perfil',
            'route' => 'postulante.index',
            'icon' => 'fas fa-fw fa-user',
            'can' => 'postulante.index'
        ],
        [
            'text' => 'Matricula',
            'icon' => 'fas fa-id-card',
            'can' => 'alumno.matricula.index',
            'submenu' => [
                [
                    'text' => 'Actual',
                    'route' => 'alumno.matriActual.index',
                    'icon' => 'fas fa-calendar-check',
                    'active' => ['alumno/horarioAlumno*'],
                    'can' => 'alumno.matriActual.index'
                ],
                [
                    'text' => 'Por Curricula',
                    'route' => 'alumno.matriPorCurri.index',
                    'icon' => 'fas fa-book',
                    'can' => 'alumno.matriPorCurri.index'
                ],
                [
                    'text' => 'Realizadas',
                    'route' => 'alumno.matriReali.index',
                    'icon' => 'fas fa-check-circle',
                    'can' => 'alumno.matriReali.index'
                ],
            ]
        ],

        // INICIO ROL DOCENTE
        ['header' => 'DOCENTE', 'can' => 'rol-docente'],
        [
            'text' => 'Datos',
            'route' => 'docente.datos',
            'icon' => 'fas fa-user',
            'active' => ['docente/Datos*'],
            'can' => 'rol-docente',
        ],
        [
            'text' => 'Horario',
            'route' => 'docente.horario',
            'icon' => 'fas fa-calendar-week',
            'active' => ['docente/verHorario*'],
            'can' => 'rol-docente',
        ],
        [
            'text' => 'Cursos',
            'route' => 'docente.calificaciones',
            'icon' => 'fas fa-pen-fancy',
            'active' => ['docente/verAlumnos*'],
            'can' => 'rol-docente',
        ],
        ['header' => 'SECCION DOCUMENTARIO', 'can' => 'rol-docente'],
        [
            'text' => 'Crear',
            'route' => 'docente.creardocu',
            'icon' => 'fas fa-clipboard',
            'active' => ['docente/Docu-docente*'],
            'can' => 'rol-docente',
        ],
        [
            'text' => 'Bandeja',
            'route' => 'docente.bandejaDoce',
            'icon' => 'fas fa-inbox',
            'active' => ['docente/Bandeja-docente*'],
            'can' => 'rol-docente',
        ],
        [
            'text' => 'Buscar',
            'route' => 'documentario.searchDocu.index',
            'icon' => 'fas fa-search',
            'active' => ['docente/searchDocu*'],
            'can' => 'rol-docente'
        ],
        // FIN ROL DOCENTE

        // INICIO TRAMITE DOCUMENTARIO
        [
            'header' => 'DOCUMENTOS',
            'can' => 'rol-documentario',
        ],
        [
            'text' => 'Crear',
            'route' => 'documentario.mesapar.index',
            'icon' => 'fas fa-fw fa-user',
            'active' => ['admin/Docu*'],
            'can' => 'rol-documentario'
        ],
        [
            'text' => 'Bandeja',
            'route' => 'documentario.mesapar.bandeja',
            'icon' => 'fas fa-inbox',
            'active' => ['admin/recibidos*'],
            'can' => 'rol-documentario'
        ],
        [
            'text' => 'Buscar',
            'route' => 'documentario.searchDocu.index',
            'icon' => 'fas fa-search',
            'active' => ['admin/searchDocu*'],
            'can' => 'rol-documentario'
        ],

        [
            'header' => 'REPORTES',
            'can' => 'rol-documentario'
        ],
        [
            'text' => 'Depencias',
            'route' => 'documentario.reporDepen.index',
            'icon' => 'fas fa-chart-pie',
            'active' => ['reporDepen*'],
            'can' => 'rol-documentario'
        ],
        // FIN TRAMITE DOCUMENTARIO






        ['header' => 'ALUMNO', 'can' => 'alumno.index',],
        ['header' => 'EGRESADO', 'can' => 'egresado.index',],
        // [
        //     'text' => 'contraseña',
        //     'url' => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-lock',
        // ],
        // [
        //     'text' => 'multilevel',
        //     'icon' => 'fas fa-fw fa-share',
        //     'submenu' => [
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //             'submenu' => [
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                 ],
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                     'submenu' => [
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //     ],
        // ],
        // ['header' => 'labels'],
        // [
        //     'text' => 'important',
        //     'icon_color' => 'red',
        //     'url' => '#',
        // ],
        // [
        //     'text' => 'warning',
        //     'icon_color' => 'yellow',
        //     'url' => '#',
        // ],
        // [
        //     'text' => 'information',
        //     'icon_color' => 'cyan',
        //     'url' => '#',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
