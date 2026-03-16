<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            $encargadoActivo = DB::connection('mysql_segunda')->table('encargados')->select('logo')->where('estado', 1)->first();
            // dd($encargadoActivo);
            // Si existe y tiene logo, lo usamos; si no, usamos un logo por defecto
            $logo = $encargadoActivo && $encargadoActivo->logo
                ? $encargadoActivo->logo
                : 'logos/default-user.jpg';

            config(['adminlte.logo_img' => $logo]);
            config(['adminlte.preloader.img.path' => $logo]);

            //------------------------------------------- para los estados en todas las vistas----------

            if (!Auth::check()) {
                return;
            }

            $nom_usu = DB::connection('mysql_segunda')
                ->table('userprofile')
                ->where('id_users', Auth::user()->id)
                ->value('nombre');

            $id_depen = session('dependencia_id');

            $rol = DB::connection('mysql_documentario')
                ->table('dependencias')
                ->where('iddependencias', $id_depen)
                ->first();

            $cont_est = DB::connection('mysql_documentario')->select(
                'SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos),0) as cont_estado
             FROM estado
             LEFT JOIN movimiento
                ON movimiento.idestado = estado.idestado
                AND movimiento.iddependencias_receptor = ?
             WHERE estado.idestado IN (1,2,3)
             GROUP BY estado.idestado',
                [$id_depen]
            );

            $view->with(compact(
                'nom_usu',
                'id_depen',
                'rol',
                'cont_est'
            ));
        });
    }
}
