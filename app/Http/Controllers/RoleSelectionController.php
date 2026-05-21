<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class RoleSelectionController extends Controller
{
    public function index()
    {
        // Si el usuario no tiene múltiples roles, no debería estar aquí
        if (auth()->user()->roles->count() <= 1) {
            return redirect('/home');
        }
        return view('auth.select-role');
    }

    public function store(Request $request)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user = auth()->user();
        // Seguridad: Verificar que el usuario realmente tenga ese rol
        $role = Role::find($request->role_id);
        if ($user->hasRole($role)) {
            session()->forget(['dependencia_id', 'active_role_name']);

            // dd($role->name);
            // Guardamos en sesión el ID y el nombre del rol activo
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

        return back()->with('error', 'Acceso no autorizado.');
    }
}
