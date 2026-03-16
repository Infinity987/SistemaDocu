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

            // dd($role->name);
            // Guardamos en sesión el ID y el nombre del rol activo
            session([
                'dependencia_id' => $role->id,
                'active_role_name' => $role->name
            ]);

            if ($role->name === 'admin') {
                return redirect('admin/users');
            } else if ($role->name === 'docente') {
                return redirect('/docente/horario');
            } else if ($role->name === 'alumno') {
                return redirect('/alumno/matriReali');
            } else if ($role->name === 'postulante') {
                return redirect()->route('postulante.index');
            } else if ($role->name === 'egresado') {
                return redirect('/egresado/index');
            } else if ($role->name === 'admision') {
                return redirect('admin/procesos');
            } else if ($role->name === 'Dirección') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Jefatura de unidad Académica') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Jefatura de unidad Administrativa') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Secretaria Académica') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Coordin. Prog. Estudios Educ. Inicial') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Coordin. Prog. Estudios Primaria Epib') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Coordin. Prog. Estudios Educ. Física') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Coordin. Prog. Educac. Secundaria') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'J. Area Acad. Educ. Básica Regular') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Jefe de Unidad de Formación Contínua') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'J. Unidad de bienestar y empleabilidad') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'J. Unidad de Investigación') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'J. Area de Calidad') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Coord. del área de Práctica Profesional e investigación') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Biblioteca') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Y/O Cargos') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'PPD') {
                return redirect()->route('documentario.mesapar.index');
            } else if ($role->name === 'Mesa de partes') {
                return redirect()->route('documentario.mesapar.index');
            }
        }

        return back()->with('error', 'Acceso no autorizado.');
    }
}
