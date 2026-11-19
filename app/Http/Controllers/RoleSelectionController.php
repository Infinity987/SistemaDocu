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
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = auth()->user();
        $role = Role::find($request->role_id);

        // Seguridad: Verificar que el usuario realmente tenga ese rol asignado
        if ($user->hasRole($role->name)) {

            // Limpiar sesión anterior y establecer la nueva
            session()->forget(['dependencia_id', 'active_role_name']);
            session([
                'dependencia_id' => $role->id,
                'active_role_name' => $role->name
            ]);

            // Redirección inteligente y 100% automática
            return match ($role->name) {
                'admin'      => redirect('admin/users'),
                'docente'    => redirect('/docente/horario'),
                'alumno'     => redirect('/alumno/matriReali'),
                'postulante' => redirect()->route('postulante.index'),
                'egresado'   => redirect('/egresado/index'),
                'admision'   => redirect('admin/procesos'),

                // 🌟 LA CLAVE: CUALQUIER otro rol (Dirección, Biblioteca, o una nueva oficina
                // como "Auxiliar", "Tesorería", etc.) caerá aquí automáticamente.
                // ¡Nunca más tendrás que agregar nombres de oficinas aquí!
                default => redirect()->route('documentario.mesapar.index'),
            };
        }

        return back()->with('error', 'Acceso no autorizado a este rol.');
    }
}
