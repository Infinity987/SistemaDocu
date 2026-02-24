<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Composer global para todas las vistas
        View::composer('*', function () {
            $encargadoActivo = DB::connection('mysql_segunda')->table('encargados')->select('logo')->where('estado', 1)->first();
            // dd($encargadoActivo);
            // Si existe y tiene logo, lo usamos; si no, usamos un logo por defecto
            $logo = $encargadoActivo && $encargadoActivo->logo
                ? $encargadoActivo->logo
                : 'logos/default-user.jpg';

            // ✅ Actualizar la configuración de AdminLTE dinámicamente
            config(['adminlte.logo_img' => $logo]);
            config(['adminlte.preloader.img.path' => $logo]);
        });
    }
}
