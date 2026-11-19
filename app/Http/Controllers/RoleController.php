<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // Lista de roles del sistema que NO se pueden tocar desde el CRUD
    private $rolesProtegidos = [
        'admin', 'docente', 'postulante', 'alumno', 'egresado', 'admision', 'personal_base'
    ];

    public function index()
    {
        // Solo mostramos los roles que NO están en la lista de protegidos (las oficinas)
        $roles = Role::whereNotIn('name', $this->rolesProtegidos)->orderBy('id', 'desc')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        // Crear el rol
        Role::create(['name' => $request->name, 'guard_name' => 'web']);
        DB::connection('mysql_documentario')->table('dependencias')->insert([
            'nombre_dependencia' => $request->name
        ]);

        return redirect()->route('roles.index')->with('success', 'Oficina creada correctamente.');
    }

    public function edit(Role $role)
    {
        // Protección: No permitir editar roles del sistema
        if (in_array($role->name, $this->rolesProtegidos)) {
            abort(403, 'No se puede editar este rol del sistema.');
        }

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        // Protección: No permitir editar roles del sistema
        if (in_array($role->name, $this->rolesProtegidos)) {
            abort(403, 'No se puede editar este rol del sistema.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $nomdepen = DB::table('roles')->where('id', $role->id)->value('name');
        $role->update(['name' => $request->name]);
        $iddependencias = DB::connection('mysql_documentario')->table('dependencias')->where('nombre_dependencia',$nomdepen)->value('iddependencias');
        DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $iddependencias)->update([
            'nombre_dependencia' => $request->name
        ]);

        return redirect()->route('roles.index')->with('success', 'Oficina actualizada correctamente.');
    }

    public function destroy(Role $role)
    {
        // Protección: No permitir eliminar roles del sistema
        if (in_array($role->name, $this->rolesProtegidos)) {
            abort(403, 'No se puede eliminar este rol del sistema.');
        }

        // Opcional: Verificar si hay usuarios con este rol antes de borrar
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar la oficina porque tiene usuarios asignados.');
        }

        $role->delete();
        DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $role->id)->delete();

        return redirect()->route('roles.index')->with('success', 'Oficina eliminada correctamente.');
    }
}
