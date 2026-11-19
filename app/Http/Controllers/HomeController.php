<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $roles = $user->roles;

        if ($roles->count() > 1) {
            session()->forget(['dependencia_id', 'active_role_name']);
            return redirect()->route('selector.roles');
        }

        if ($roles->count() === 1) {
            $role = $roles->first();

            // Limpiamos lo viejo antes de setear lo nuevo
            session()->forget(['dependencia_id', 'active_role_name']);

            session([
                'dependencia_id' => $role->id,
                'active_role_name' => $role->name
            ]);

            return match ($role->name) {
                'admin'      => redirect('admin/users'),
                'docente'    => redirect('/docente/horario'),
                'alumno'     => redirect('/alumno/matriReali'),
                'postulante' => redirect()->route('postulante.index'),
                'egresado'   => redirect('/egresado/index'),
                'admision'   => redirect('admin/procesos'),

                'Dirección',
                'Jefatura de unidad Académica',
                'Jefatura de unidad Administrativa',
                'Secretaria Académica',
                'Mesa de partes',
                'Biblioteca',
                'PPD',
                'Y/O Cargos',
                'J. Unidad de Investigación',
                'J. Area de Calidad',
                // ... puedes seguir agregando los nombres exactos aquí
                => redirect()->route('documentario.mesapar.index'),

                default => redirect()->route('documentario.mesapar.index'),
            };
        }

        return redirect()->route('login');
    }
}
