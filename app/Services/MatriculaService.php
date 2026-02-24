<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MatriculaService
{
    public function revocarAlumnosInactivos($idSemestre)
    {
        Log::debug("🧠 Ejecutando revocación para semestre {$idSemestre}");

        // 1. Validar semestre
        $semestre = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('idsemestre_academico', $idSemestre)
            ->first();

        if (! $semestre) {
            Log::warning("⚠️ Semestre no encontrado: $idSemestre");
            return;
        }

        // 2. Validar cierre de matrícula
        $finMatricula = Carbon::parse($semestre->fecha_fin_matricula)->endOfDay();
        if (now()->lessThan($finMatricula)) {
            Log::info("📅 Matrícula aún abierta. No se ejecuta revocación.");
            return;
        }

        // 3. Obtener alumnos activos con malla asignada
        $alumnos = DB::connection('mysql')
            ->table('postulante')
            ->join('users', 'postulante.idpostulante', '=', 'users.dni')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 4)
            ->whereNotNull('postulante.id_malla')
            ->select('users.id as user_id', 'users.dni as dni')
            ->get();

        foreach ($alumnos as $alumno) {
            Log::debug("🔍 Evaluando alumno DNI {$alumno->dni}");

            // 4. Verificar si está matriculado en el semestre
            $yaMatriculado = DB::connection('mysql_segunda')
                ->table('matricula')
                ->where('id_alumno', $alumno->dni)
                ->where('idsemestre_academico', $idSemestre)
                ->exists();

            if (! $yaMatriculado) {
                $this->revocarYReasignar($alumno->user_id, $alumno->dni);
            }
        }

        Log::info("✅ Revocación completada para semestre $idSemestre.");
    }

    private function revocarYReasignar($userId, $dni)
{
    // 1. Revocar rol de alumno
    DB::connection('mysql')
        ->table('model_has_roles')
        ->where('model_id', $userId)
        ->where('role_id', 4)
        ->delete();

    // 2. Asignar rol de postulante si no lo tiene
    $tieneRolPostulante = DB::connection('mysql')
        ->table('model_has_roles')
        ->where('model_id', $userId)
        ->where('role_id', 3)
        ->exists();

    if (! $tieneRolPostulante) {
        DB::connection('mysql')
            ->table('model_has_roles')
            ->insert([
                'role_id' => 3,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ]);
    }

    // 3. Limpiar solo el campo id_malla
$actualizado = DB::connection('mysql')
    ->table('postulante')
    ->where('idpostulante', $dni)
    ->update(['id_malla' => null]);

Log::debug("🧹 Resultado de limpieza de malla para DNI $dni: $actualizado");

    // 4. Log detallado
    Log::info("🔄 Usuario $userId (DNI $dni): rol de alumno revocado, rol de postulante asignado, malla eliminada.");
}
}