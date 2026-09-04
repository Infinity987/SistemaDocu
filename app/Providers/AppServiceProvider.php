<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\AssignOp\Concat;

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

            $activeRole = session('active_role_name');
            $userId = auth()->id();
            $dni = auth()->user()->dni;
            $id_depen = session('dependencia_id');

            $rol = DB::connection('mysql_documentario')
                ->table('dependencias')
                ->where('iddependencias', $id_depen)
                ->first();

            if (in_array($activeRole, ['alumno', 'egresado', 'postulante'])) {
                $nom_usu = DB::table('postulante')
                    ->where('idpostulante', $dni)
                    ->selectRaw("CONCAT(apellidos_pater_postulante, ' ', apellidos_mater_postulante, ' ',nombres_postulante) AS nombre")
                    ->value('nombre');
            } else {
                $nom_usu = DB::connection('mysql_segunda')
                    ->table('userprofile')
                    ->where('id_users', Auth::user()->id)
                    ->value('nombre');
            }

            if (in_array($activeRole, ['docente', 'alumno', 'egresado', 'postulante'])) {
                $cont_est = DB::connection('mysql_documentario')->select(
                    'SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos),0) as cont_estado
                        FROM estado
                        LEFT JOIN movimiento
                            ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            AND id_user_receptor = ?
                        WHERE estado.idestado IN (1,2,3)
                        GROUP BY estado.idestado',
                    [$id_depen, $userId]
                );
            } else {
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
            }

            $view->with(compact(
                'nom_usu',
                'id_depen',
                'rol',
                'cont_est'
            ));
        });
    }
}
