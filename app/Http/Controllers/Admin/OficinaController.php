<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dependencia; // O el nombre de tu modelo para oficinas
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class OficinaController extends Controller
{
    public function index()
    {
        $oficinas = DB::connection('my')
        // return view('admin.oficinas.index', compact('oficinas'));
    }

    public function create()
    {
        return view('admin.oficinas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:dependencias,nombre',
            'siglas' => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Guardar la oficina en la BD
            $oficina = Dependencia::create([
                'nombre' => $request->nombre,
                'siglas' => $request->siglas,
            ]);

            // 2. Crear el Rol de Spatie con el mismo nombre
            $rol = Role::create([
                'name' => $request->nombre,
                'guard_name' => 'web'
            ]);

            // 3. Asignar plantilla de permisos de trámite documentario por defecto
            $permisosBase = [
                'documentario.mesapar.index',
                'documentario.num_tipo_documento_expe',
                'documentario.buscarUsuario',
                'documentario.registrarDocu',
                'documentario.mesapar.emitidos',
                'documentario.mesapar.showEmitido',
                'documentario.mesapar.updateDocuEmi',
                'documentario.mesapar.bandeja',
                'documentario.bandeja.bandejaEstado',
                'documentario.bandeja.recibir',
            ];

            $rol->syncPermissions($permisosBase);
        });

        // return redirect()->route('admin.oficinas.index')
        //     ->with('info', 'Oficina y Rol institucional creados con éxito.');
    }
}
