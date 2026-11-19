<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ReporteUsuariosController extends Controller
{
public function index()
{
    // 1. Mapa de Oficinas (Spatie Roles)
    $mapaOficinas = \Spatie\Permission\Models\Role::pluck('name', 'id');

    // 2. Obtener el ID exacto de 'personal_base' desde la tabla roles
    $personalBaseId = \Spatie\Permission\Models\Role::where('name', 'personal_base')->value('id');

    // 3. Obtener TODAS las dependencias activas (Oficinas + personal_base)
    $dependenciasActivas = DB::connection('mysql_documentario')
        ->table('dependencia_user')
        ->where('estado', 1)
        ->get()
        ->groupBy('user_id');

    $idsUsuariosActivos = $dependenciasActivas->keys();

    // 4. Perfiles
    $perfiles = DB::connection('mysql_segunda')
        ->table('userprofile')
        ->whereIn('id_users', $idsUsuariosActivos)
        ->get()
        ->keyBy('id_users');

    // 🔥 CAMBIO 1: Traemos también 'updated_at' para poder ordenar después
    $usuariosData = DB::table('users')
        ->whereIn('id', $idsUsuariosActivos)
        ->select('id', 'dni', 'updated_at')
        ->get()
        ->keyBy('id');

    // 5. Construir la lista AGRUPADA
    $listaFinal = [];
    foreach ($dependenciasActivas as $userId => $dependencias) {
        $perfil = $perfiles[$userId] ?? null;
        $userData = $usuariosData[$userId] ?? null;

        // 🟢 VERIFICAR SI TIENE 'personal_base' ANTES DE FILTRAR
        $tienePersonalBase = false;
        if ($personalBaseId) {
            $tienePersonalBase = $dependencias->contains('dependencia_id', $personalBaseId);
        }

        // 🚫 FILTRAR 'personal_base' para que no aparezca como una oficina más en la lista
        $oficinasReales = $dependencias->filter(function ($dep) use ($personalBaseId) {
            return $dep->dependencia_id != $personalBaseId;
        });

        $oficinasUsuario = [];
        foreach ($oficinasReales as $dep) {
            $oficinasUsuario[] = [
                'nombre'       => $mapaOficinas[$dep->dependencia_id] ?? 'Oficina Desconocida (ID: ' . $dep->dependencia_id . ')',
                'tipo'         => $dep->tipo_usuario == 2 ? 'Jefe' : 'Asistente',
                'tiene_base'   => $tienePersonalBase,
            ];
        }

        // Solo agregamos al reporte si tiene al menos una oficina real
        if (!empty($oficinasUsuario)) {
            $listaFinal[] = [
                'user_id'    => $userId,
                'dni'        => $userData->dni ?? 'Sin DNI',
                'nombre'     => $perfil->nombre ?? 'Sin Nombre',
                'correo'     => $perfil->correo ?? 'Sin Correo',
                'updated_at' => $userData->updated_at ?? '1970-01-01 00:00:00', // 🔥 Guardamos la fecha para ordenar
                'oficinas'   => $oficinasUsuario,
            ];
        }
    }

    // 🔥 CAMBIO 2: Ordenar de manera DESCENDENTE por updated_at (el más reciente primero)
    usort($listaFinal, function ($a, $b) {
        // strcmp con $b primero logra el orden descendente en fechas formato Y-m-d H:i:s
        return strcmp($b['updated_at'], $a['updated_at']);
    });

    return view('admin.users.usuarios_activos', compact('listaFinal'));
}
}
        // return view('admin.users.usuarios_activos', compact('listaFinal'));
