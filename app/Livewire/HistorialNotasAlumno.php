<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistorialNotasAlumno extends Component
{
    public $dni = '';
    public $alumno = null;
    public $malla = null;
    public $cicloActual = null;

    public $todosCursos = [];
    public $cursosFiltrados = [];

    public $cicloSeleccionado = '';
    public $semestresDisponibles = [];
    public $semestreSeleccionado = '';
    public $tipoMatriculaSeleccionado = '';
    public $resumenCiclo = [];
    public $turnoSeleccionado = '';
public $codigoBoleta = '';

    public $notasPorCiclo = []; 

    

public function buscar()
{
    $this->reset(['alumno', 'malla', 'cicloActual', 'todosCursos', 'cursosFiltrados', 'notasPorCiclo']);

    if (empty($this->dni)) return;

    $this->alumno = DB::connection('mysql')
        ->table('postulante')
        ->where('idpostulante', $this->dni)
        ->first();

    if (! $this->alumno) {
        session()->flash('error', '❌ No se encontró el alumno.');
        return;
    }

    $this->malla = $this->alumno->id_malla;
    $this->cicloActual = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $this->dni)
        ->max('ciclo_matricula');

        if (! $this->cicloActual) {
    // Alumno sin matrícula: mostrar todos los ciclos disponibles
    $this->cursosFiltrados = $this->todosCursos;
    $this->resumenCiclo = [
        'total' => count($this->todosCursos),
        'registrados' => 0,
        'subsanables' => 0,
        'disponibles' => count($this->todosCursos),
    ];

    // Prepara estructura de notas vacías
    foreach ($this->todosCursos as $curso) {
        $this->notasPorCiclo[$curso->idcursos] = [
            'nota' => '',
            'estado' => null,
        ];
    }

    session()->flash('info', 'ℹ️ Alumno sin matrícula previa. Se mostrarán todos los cursos disponibles.');
}

    $this->cargarCursos();
    $this->cargarSemestres();
}
private function cargarCursos()
{
    $this->todosCursos = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as curso', 'plan.idcursos', '=', 'curso.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $this->malla)
        ->select('curso.idcursos', 'curso.nombre_curso', 'curso.credito', 'plan.idciclos')
        ->orderBy('plan.idciclos')
        ->get()
        ->toArray();
}

private function cargarSemestres()
{
    $this->semestresDisponibles = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->orderByDesc('idsemestre_academico')
        ->get()
        ->map(fn($s) => [
            'id' => $s->idsemestre_academico,
            'nombre' => "{$s->año} {$s->periodo}"
        ]);
}
public function updatedCicloSeleccionado()
{
    $this->cursosFiltrados = collect($this->todosCursos)
        ->where('idciclos', $this->cicloSeleccionado)
        ->values()
        ->toArray();

    $this->reset(['notasPorCiclo']);
}
public function guardarNotasPorCiclo()
{
    if (! $this->semestreSeleccionado || ! $this->tipoMatriculaSeleccionado || ! $this->cicloSeleccionado) {
        session()->flash('error', '⚠️ Debe seleccionar semestre, tipo de matrícula y ciclo.');
        return;
    }
    if (! $this->turnoSeleccionado) {
    session()->flash('error', '⚠️ Debe seleccionar el turno.');
    return;
}

   $notasValidas = array_filter($this->notasPorCiclo, fn($n) =>
    isset($n['estado']) && (
        ($n['estado'] === 2) || // ✅ Llevando, nota puede ser null
        (isset($n['nota']) && $n['nota'] !== '') // otros casos requieren nota
    )
);

    $notasValidas = array_filter($this->notasPorCiclo, function ($n) {
    if (!isset($n['estado'])) return false;

    // Estado 2: "Llevando" → nota puede ser null
    if ((int)$n['estado'] === 2) return true;

    // Otros estados → nota debe estar presente
    return isset($n['nota']) && $n['nota'] !== '';
});

if (empty($notasValidas)) {
    session()->flash('error', '⚠️ No hay cursos válidos para registrar. Verifica que al menos uno tenga estado o nota.');
    return;
}

    DB::beginTransaction();
    try {
        $idMatricula = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $this->alumno->idpostulante)
            ->where('idsemestre_academico', $this->semestreSeleccionado)
            ->where('idtipo_matricula', $this->tipoMatriculaSeleccionado)
            ->where('ciclo_matricula', $this->cicloSeleccionado)
            ->where('idmalla', $this->malla)
            ->value('idmatricula');

            $totalCreditos = collect($notasValidas)
    ->map(fn($n, $id) => collect($this->cursosFiltrados)->firstWhere('idcursos', $id)?->credito ?? 0)
    ->sum();

        if (! $idMatricula) {
            $idMatricula = DB::connection('mysql_segunda')
                ->table('matricula')
                ->insertGetId([
                    'id_alumno'            => $this->alumno->idpostulante,
                    'idsemestre_academico' => $this->semestreSeleccionado,
                    'idestado_matricula'   => 1,
                    'idtipo_matricula'     => $this->tipoMatriculaSeleccionado,
                    'fecha_matricula'      => now(),
                    'total_credito'        => $totalCreditos,
                    'credito_alumno'       => $totalCreditos,
                    'ciclo_matricula'      => $this->cicloSeleccionado,
                    'id_turno'             => $this->turnoSeleccionado,
                    'idseccion'            => 1,
                    'idmalla'              => $this->malla,
                    'codigo_boleta' => $this->codigoBoleta !== '' ? $this->codigoBoleta : null,
                    'id_reporte_matricula' => $this->tipoMatriculaSeleccionado,
                ]);
        }

        foreach ($notasValidas as $cursoId => $notaData) {
            $curso = collect($this->cursosFiltrados)->firstWhere('idcursos', $cursoId);

            $docente = DB::connection('mysql_segunda')
                ->table('docente_curso')
                ->where('idcursos', $cursoId)
                ->where('idsemestre_academico', $this->semestreSeleccionado)
                ->where('tipodocente_curso', $this->tipoMatriculaSeleccionado)
                ->first();

            if (! $docente) continue;

            $yaExiste = DB::connection('mysql_segunda')
                ->table('incripcion_curso as ic')
                ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
                ->where('ic.idmatricula', $idMatricula)
                ->where('dc.idcursos', $cursoId)
                ->exists();

            if ($yaExiste) continue;

            DB::connection('mysql_segunda')->table('incripcion_curso')->insert([
                'idmatricula'       => $idMatricula,
                'id_docente_curso'  => $docente->iddocente_curso,
                'credito'           => $curso->credito,
                'idCalificaciones1' => 0,
                'idCalificaciones2' => 0,
                'idCalificaciones3' => 0,
                'total' => isset($notaData['nota']) && $notaData['nota'] !== '' ? $notaData['nota'] : null,
                'estado_nota'       => $notaData['estado'],
            ]);
        }

        DB::commit();
        session()->flash('success', '✅ Notas registradas correctamente.');
        $this->reset(['notasPorCiclo', 'cicloSeleccionado', 'semestreSeleccionado', 'tipoMatriculaSeleccionado']);
        $this->buscar();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Error al guardar notas por ciclo: " . $e->getMessage());
        session()->flash('error', '❌ Error al guardar notas.');
    }
}

public function cargarCursosDelCiclo()
{
    if ($this->tipoMatriculaSeleccionado == 2) {
    $cursosDelCiclo = collect($this->todosCursos)->where('idciclos', $this->cicloSeleccionado);

    $aprobados = 0;
    $totalCursos = $cursosDelCiclo->count();

    foreach ($cursosDelCiclo as $curso) {
        $notaRegular = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->where('m.id_alumno', $this->alumno->idpostulante)
            ->where('m.ciclo_matricula', $this->cicloSeleccionado)
            ->where('dc.idcursos', $curso->idcursos)
            ->where('m.idtipo_matricula', 1) // Regular
            ->value('ic.estado_nota');

        if ($notaRegular === 1) $aprobados++;
    }

    if ($aprobados === $totalCursos) {
        session()->flash('error', '⚠️ Todos los cursos del ciclo ya fueron aprobados en matrícula regular. No se permite subsanación.');
        return;
    }
}
    if (! $this->cicloSeleccionado) {
        session()->flash('error', '⚠️ Seleccione un ciclo.');
        return;
    }

    $this->cursosFiltrados = [];
    

    foreach ($this->todosCursos as $curso) {
        if ($curso->idciclos != $this->cicloSeleccionado) continue;

        $inscripciones = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->where('m.id_alumno', $this->alumno->idpostulante)
            ->where('m.ciclo_matricula', $this->cicloSeleccionado)
            ->where('dc.idcursos', $curso->idcursos)
            ->select('ic.total', 'ic.estado_nota', 'm.idtipo_matricula', 'm.idsemestre_academico')
            ->get();

        $curso->bloqueado = false;
        $curso->nota_registrada = null;
        $curso->estado_registrado = null;

        foreach ($inscripciones as $ins) {
            // Bloquear si ya existe nota en mismo tipo y semestre
            if ($ins->idtipo_matricula == $this->tipoMatriculaSeleccionado &&
                $ins->idsemestre_academico == $this->semestreSeleccionado) {
                $curso->bloqueado = true;
                $curso->nota_registrada = $ins->total;
                $curso->estado_registrado = $ins->estado_nota;
            }

            // Permitir subsanación si fue desaprobado en regular
            if ($ins->idtipo_matricula == 1 && $ins->estado_nota == 0 &&
                $this->tipoMatriculaSeleccionado == 2 &&
                $ins->idsemestre_academico != $this->semestreSeleccionado) {
                $curso->bloqueado = false;
            }
        }

        $this->cursosFiltrados[] = $curso;
    }

    $this->reset(['notasPorCiclo']);

    
$this->resumenCiclo = [
    'total' => count($this->cursosFiltrados),
    'registrados' => 0,
    'subsanables' => 0,
    'disponibles' => 0,
];

foreach ($this->cursosFiltrados as $curso) {
    if ($curso->bloqueado) {
        $this->resumenCiclo['registrados']++;
    } elseif ($this->tipoMatriculaSeleccionado == 2 && $curso->estado_registrado === 0) {
        $this->resumenCiclo['subsanables']++;
    } else {
        $this->resumenCiclo['disponibles']++;
    }
}

$this->notasPorCiclo = [];

foreach ($this->cursosFiltrados as $curso) {
    if (! $curso->bloqueado) {
        $this->notasPorCiclo[$curso->idcursos] = [
            'nota' => '',
            'estado' => null,
        ];
    }
}

}

public function updated($name, $value)
{
    Log::debug("🔄 Cambio detectado: {$name} = {$value}");

    if (str_starts_with($name, 'notasPorCiclo.') && str_ends_with($name, '.nota')) {
       $parts = explode('.', $name);
$cursoId = $parts[1] ?? null;
        $nota = is_numeric($value) ? (int) $value : null;

       if ($cursoId !== null) {
    $nota = is_numeric($value) ? (int) $value : null;

    if ($nota !== null) {
        $estado = $nota > 10 ? 1 : 0;
        $this->notasPorCiclo[$cursoId]['estado'] = $estado;
        Log::debug("✅ Estado actualizado para curso {$cursoId}: {$estado}");
    } else {
        $this->notasPorCiclo[$cursoId]['estado'] = null;
        Log::debug("⚠️ Nota inválida para curso {$cursoId}, estado puesto en null");
    }
}

    }
}


}