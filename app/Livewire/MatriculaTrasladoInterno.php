<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatriculaTrasladoInterno extends Component
{
    public $dni;
    public $alumno;
    public $mallaAnterior;
    public $mallaNueva;
    public $mallasDisponibles = [];
    public $cicloActual;
    public $semestreActivo;
    public $cursosFEPendientes = [];
    public $cursosFESeleccionados = [];
    public $creditosSeleccionados = 0;
    public $cursosFGAprobados = [];
    public $cursosFGDesaprobados = [];
    public $nombreMallaAnterior = '';
    public $nombreCicloActual = '';
    public $cicloExpandido = [];
    public $cursosFGAprobadosPorCiclo = [];
    public $cursosFGDesaprobadosPorCiclo = [];
    public $cursosFEPendientesPorCiclo = [];
    public $cursosConvalidables = [];
    public $cursosConvalidablesPorCiclo = [];
    public $aprobados = [];
    public $desaprobados = [];
    public $yaTieneTrasladoInterno = false;
    public $yaHizoTrasladoHistorico = false;
    public $mostrarSelectorMalla = true;
    public $cursosDelCicloSiguiente = [];
public $turno = 1;
public $codigoBoleta;
public $cursosPendientesPorCiclo = [];




    public function mount()
    {
        // 🔄 Obtener semestre activo para matrícula regular (FE)
        $this->semestreActivo = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('estado_matricula', 1)
            ->first();

        // 📚 Cargar mallas disponibles para traslado
        $this->mallasDisponibles = DB::connection('mysql_segunda')
            ->table('malla_curricular')
            ->select('idmalla_curricular', 'nombre_malla_curricular as nombre_malla')
            ->get();
    }

    public function buscarAlumno()
    {
        // 🧹 Resetear datos previos
        $this->reset([
            'alumno',
            'mallaAnterior',
            'mallaNueva',
            'cicloActual',
            'cursosFGAprobados',
            'cursosFGDesaprobados',
            'cursosFEPendientes',
            'cursosFESeleccionados',
            'creditosSeleccionados',
        ]);

        // ✅ Reasignar manualmente para que no se borre
        $this->mostrarSelectorMalla = true;

        // 🔒 Validar DNI
        if (empty($this->dni) || !ctype_digit($this->dni)) {
            session()->flash('error', '⚠️ Ingresa un DNI válido.');
            return;
        }

        // 🔍 Buscar alumno en base principal
        $alumno = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $this->dni)
            ->first();

        if (! $alumno) {
            session()->flash('error', '❌ No se encontró un alumno con ese DNI.');
            return;
        }


        $matriculaTraslado = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $alumno->idpostulante)
            ->where('idsemestre_academico', $this->semestreActivo->idsemestre_academico)
            ->where('id_reporte_matricula', 4) // ✅ traslado interno
            ->first();


        $this->yaTieneTrasladoInterno = $matriculaTraslado ? true : false;
        $this->yaHizoTrasladoHistorico = $this->yaHizoTrasladoInterno($alumno->idpostulante);
        $this->mostrarSelectorMalla = ! $this->yaHizoTrasladoHistorico;

        $this->alumno = $alumno;
        $this->mallaAnterior = $alumno->id_malla;

        $this->alumno = $alumno;
        $this->mallaAnterior = $alumno->id_malla;

        // 📘 Obtener nombre de la malla anterior
        $malla = DB::connection('mysql_segunda')
            ->table('malla_curricular')
            ->where('idmalla_curricular', $alumno->id_malla)
            ->select('nombre_malla_curricular')
            ->first();

        $this->nombreMallaAnterior = $malla->nombre_malla_curricular ?? 'Sin nombre';

        // 📅 Obtener ciclo actual desde la última matrícula regular
        $cicloMax = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $alumno->idpostulante)
            ->where('idmalla', $alumno->id_malla)
            ->where('idtipo_matricula', 1) // solo matrícula regular
            ->max('ciclo_matricula');

        $this->cicloActual = $cicloMax ?? 1;

        // 🏷️ Obtener nombre del ciclo actual
        $ciclo = DB::connection('mysql_segunda')
            ->table('ciclos')
            ->where('idciclos', $this->cicloActual)
            ->select('nombre_ciclo')
            ->first();

        $this->nombreCicloActual = $ciclo->nombre_ciclo ?? "Ciclo {$this->cicloActual}";

        $this->yaHizoTrasladoHistorico = $this->yaHizoTrasladoInterno($alumno->idpostulante);

        Log::info("🔍 Evaluación de traslado interno para alumno {$alumno->idpostulante}", [
            'yaTieneTrasladoInterno' => $this->yaTieneTrasladoInterno,
            'yaHizoTrasladoHistorico' => $this->yaHizoTrasladoHistorico,
            'mostrarSelectorMalla' => $this->mostrarSelectorMalla,
            'semestreActivo' => $this->semestreActivo->idsemestre_academico ?? 'NULL',
        ]);



        if ($this->mostrarSelectorMalla) {
            session()->flash('success', '✅ Alumno encontrado. Puedes seleccionar la nueva malla.');
        } else {
            session()->flash('success', '✅ Alumno encontrado. Ya realizó traslado interno en este semestre. Puedes nivelar cursos FE pendientes.');
        }
    }

    public function yaHizoTrasladoInterno($idAlumno)
    {
        return DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $idAlumno)
            ->where('id_reporte_matricula', 4)
            ->exists();
    }



 public function convalidar()
{
    if (! $this->alumno || ! $this->mallaAnterior || ! $this->mallaNueva || ! $this->cicloActual) {
        session()->flash('error', '❌ Faltan datos clave para convalidar cursos.');
        return;
    }

    $idAlumno = $this->alumno->idpostulante;

    // ✅ Cursos aprobados en la malla anterior
    $cursosAprobados = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $this->mallaAnterior)
        ->where('ic.estado_nota', 1)
        ->pluck('c.nombre_curso')
        ->unique();

    // ❌ Cursos desaprobados en la malla anterior
    $cursosDesaprobados = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $this->mallaAnterior)
        ->where('ic.estado_nota', 0)
        ->pluck('c.nombre_curso')
        ->unique();

    $this->aprobados = $cursosAprobados;
    $this->desaprobados = $cursosDesaprobados;
        // 📘 Cursos FG, FP, FEL en la nueva malla hasta el ciclo actual
    $cursosConvalidables = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $this->mallaNueva)
        ->whereIn('c.Formacion', ['FG', 'FP', 'FEL', 'FE'])
        ->where('plan.idciclos', '<=', $this->cicloActual)
        ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas', 'c.Formacion', 'plan.idciclos')
        ->get();

    // 🧠 Clasificar estado de nota
    $this->cursosConvalidables = $cursosConvalidables->map(function ($curso) use ($cursosAprobados, $cursosDesaprobados) {
        if ($cursosAprobados->contains($curso->nombre_curso)) {
            $curso->estado_nota = 1; // aprobado
        } elseif ($cursosDesaprobados->contains($curso->nombre_curso)) {
            $curso->estado_nota = 0; // desaprobado
        } else {
            $curso->estado_nota = 2; // pendiente
        }
        return $curso;
    });

    // 🧩 Agrupar por ciclo
    $this->cursosConvalidablesPorCiclo = $this->cursosConvalidables->groupBy('idciclos');
        // 📚 Cursos FE pendientes hasta el ciclo actual
    $mallaReferencia = $this->mallaNueva;

    $tiposFormacion = ['FG', 'FP', 'FEL', 'FE'];

$cursosPendientesPorTipo = collect();

foreach ($tiposFormacion as $tipo) {
    $cursosDelTipo = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $this->mallaNueva)
        ->where('c.Formacion', $tipo)
        ->where('plan.idciclos', '<=', $this->cicloActual)
        ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas', 'c.Formacion', 'plan.idciclos')
        ->get();

    $pendientes = $cursosDelTipo->filter(function ($curso) {
        return $this->cursosConvalidables->firstWhere('idcursos', $curso->idcursos)?->estado_nota !== 1;
    });

    $cursosPendientesPorTipo = $cursosPendientesPorTipo->merge($pendientes);
}

$this->cursosPendientesPorCiclo = $cursosPendientesPorTipo
    ->map(function ($curso) {
        $curso->estado_nota = 0; // todos van como desaprobados
        return $curso;
    })
    ->groupBy('idciclos')
     ->sortKeys();

   $cursosFE = DB::connection('mysql_segunda')
    ->table('plan_de_estudio as plan')
    ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
    ->where('plan.malla_curricular_idmalla_curricular', $mallaReferencia)
    ->where('c.Formacion', 'FE')
    ->where('plan.idciclos', '<=', $this->cicloActual)
    ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas', 'plan.idciclos', 'c.Formacion') // 👈 agrega esto
    ->get();

    $this->cursosDelCicloSiguiente = DB::connection('mysql_segunda')
    ->table('plan_de_estudio as plan')
    ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
    ->where('plan.malla_curricular_idmalla_curricular', $this->mallaNueva)
    ->where('plan.idciclos', $this->cicloActual + 1)
    ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas', 'c.Formacion')
    ->get();

    // 🧹 Filtrar cursos FE ya aprobados
    $aprobadosFE = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('ic.estado_nota', 1)
        ->pluck('dc.idcursos')
        ->unique();

    // 📦 Cursos FE pendientes
   // 🧹 Excluir cursos FE que ya fueron convalidados como aprobados
$feConvalidados = $this->cursosConvalidables->filter(function ($curso) {
    return $curso->Formacion === 'FE' && $curso->estado_nota === 1;
})->pluck('idcursos');

$this->cursosFEPendientes = $cursosFE->filter(function ($curso) use ($aprobadosFE, $feConvalidados) {
    return ! $aprobadosFE->contains($curso->idcursos) && ! $feConvalidados->contains($curso->idcursos);
})->values();

    $this->cursosFEPendientesPorCiclo = $this->cursosFEPendientes->groupBy('idciclos');
        // 🚀 Mostrar ciclo siguiente donde se matriculará
    $cicloSiguiente = $this->cicloActual + 1;

    session()->flash('success', "✅ Cursos convalidables y FE pendientes detectados correctamente. El alumno se matriculará automáticamente en el ciclo {$cicloSiguiente}.");
}



    public function yaHizoTrasladoEnSemestreActual($idAlumno)
    {
        return DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $idAlumno)
            ->where('idsemestre_academico', $this->semestreActivo->idsemestre_academico)
            ->where('id_reporte_matricula', 4)
            ->exists();
    }



   public function registrarMatriculaTraslado()
{
    if (! $this->alumno || ! $this->mallaNueva) {
        session()->flash('error', '❌ Faltan datos clave para registrar la matrícula.');
        return;
    }

    $idAlumno = $this->alumno->idpostulante;
    $fecha = now()->format('Y-m-d');
    $turno = 1;

    // 🔄 Actualizar malla en postulante
    DB::connection('mysql')
        ->table('postulante')
        ->where('idpostulante', $idAlumno)
        ->update(['id_malla' => $this->mallaNueva]);

    // 🧩 MATRÍCULA POR CICLO (FG, FP, FEL + FE pendientes)
    foreach ($this->cursosConvalidablesPorCiclo as $idCiclo => $cursos) {
        // ✅ Obtener semestre académico desde matrícula original
        $semestreId = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $idAlumno)
            ->where('ciclo_matricula', $idCiclo)
            ->where('idmalla', $this->mallaAnterior)
            ->value('idsemestre_academico');

        if (! $semestreId) {
            Log::warning("⛔ Ciclo sin semestre registrado: {$idCiclo}");
            continue;
        }

        // 📚 Agregar cursos FE pendientes del mismo ciclo
       $cursosPendientesDelCiclo = $this->cursosPendientesPorCiclo[$idCiclo] ?? collect();
$todosLosCursos = collect($cursos)->merge($cursosPendientesDelCiclo);

        $totalCreditos = $todosLosCursos->sum('credito');

        $idMatricula = DB::connection('mysql_segunda')->table('matricula')->insertGetId([
            'id_alumno'            => $idAlumno,
            'idsemestre_academico' => $semestreId,
            'idtipo_matricula'     => 1,
            'idestado_matricula'   => 1,
            'fecha_matricula'      => $fecha,
            'total_credito'        => $totalCreditos,
            'credito_alumno'       => $totalCreditos,
            'ciclo_matricula'      => $idCiclo,
            'id_turno'             => $turno,
            'idseccion'            => 1,
            'idmalla'              => $this->mallaNueva,
            'codigo_boleta' => $this->codigoBoleta,
            'id_reporte_matricula' => 4,
        ]);

        foreach ($todosLosCursos as $curso) {
            // 🔍 Buscar docente
            $docente = DB::connection('mysql_segunda')
                ->table('docente_curso')
                ->where('idcursos', $curso->idcursos)
                ->where('idsemestre_academico', $semestreId)
                ->first();

            if (! $docente) {
                Log::warning("⛔ Curso sin docente: {$curso->nombre_curso}");
                continue;
            }

             $yaExiste = DB::connection('mysql_segunda')
        ->table('incripcion_curso')
        ->where('idmatricula', $idMatricula)
        ->where('id_docente_curso', $docente->iddocente_curso)
        ->exists();

    if ($yaExiste) {
        Log::warning("⚠️ Curso duplicado evitado: {$curso->nombre_curso}");
        continue;
    }


            // 🧠 Si es curso FE, insertar con nota 0
          // 🧠 Si el curso está aprobado, recuperar notas reales
if ($curso->estado_nota === 1) {
    $notas = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $this->mallaAnterior)
        ->where('c.nombre_curso', $curso->nombre_curso)
        ->select('ic.idCalificaciones1', 'ic.idCalificaciones2', 'ic.idCalificaciones3', 'ic.total', 'ic.estado_nota')
        ->first();

    DB::connection('mysql_segunda')->table('incripcion_curso')->insert([
        'idmatricula'        => $idMatricula,
        'id_docente_curso'   => $docente->iddocente_curso,
        'credito'            => $curso->credito,
        'idCalificaciones1'  => $notas->idCalificaciones1 ?? 0,
        'idCalificaciones2'  => $notas->idCalificaciones2 ?? 0,
        'idCalificaciones3'  => $notas->idCalificaciones3 ?? 0,
        'total'              => $notas->total ?? null,
        'estado_nota'        => 1,
    ]);
    continue;
}

            // 🧠 Si es FG, FP, FEL, insertar con nota real
            if (!in_array($curso->estado_nota, [0, 1])) continue;

            $notas = DB::connection('mysql_segunda')
                ->table('incripcion_curso as ic')
                ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
                ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
                ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
                ->where('m.id_alumno', $idAlumno)
                ->where('m.idmalla', $this->mallaAnterior)
                ->where('c.nombre_curso', $curso->nombre_curso)
                ->select('ic.idCalificaciones1', 'ic.idCalificaciones2', 'ic.idCalificaciones3', 'ic.total', 'ic.estado_nota')
                ->first();

            DB::connection('mysql_segunda')->table('incripcion_curso')->insert([
                'idmatricula'        => $idMatricula,
                'id_docente_curso'   => $docente->iddocente_curso,
                'credito'            => $curso->credito,
                'idCalificaciones1'  => $notas->idCalificaciones1 ?? 0,
                'idCalificaciones2'  => $notas->idCalificaciones2 ?? 0,
                'idCalificaciones3'  => $notas->idCalificaciones3 ?? 0,
                'total'              => $notas->total ?? null,
                'estado_nota'        => 0,
            ]);
        }
    }

    // 🚀 MATRÍCULA DEL CICLO SIGUIENTE
    $cicloSiguiente = $this->cicloActual + 1;
    $idSemestre = $this->semestreActivo->idsemestre_academico;

    $cursosDelSiguienteCiclo = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $this->mallaNueva)
        ->where('plan.idciclos', $cicloSiguiente)
        ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas')
        ->get();

    if ($cursosDelSiguienteCiclo->isNotEmpty()) {
        $totalCreditos = $cursosDelSiguienteCiclo->sum('credito');

        $idMatricula = DB::connection('mysql_segunda')->table('matricula')->insertGetId([
            'id_alumno'            => $idAlumno,
            'idsemestre_academico' => $idSemestre,
            'idtipo_matricula'     => 1,
            'idestado_matricula'   => 1,
            'fecha_matricula'      => $fecha,
            'total_credito'        => $totalCreditos,
            'credito_alumno'       => $totalCreditos,
            'ciclo_matricula'      => $cicloSiguiente,
            'id_turno'             => $turno,
            'idseccion'            => 1,
            'codigo_boleta' => $this->codigoBoleta,
            'idmalla'              => $this->mallaNueva,
            'id_reporte_matricula' => 4,
        ]);

        foreach ($cursosDelSiguienteCiclo as $curso) {
            $docente = DB::connection('mysql_segunda')
                ->table('docente_curso')
                ->where('idcursos', $curso->idcursos)
                ->where('idsemestre_academico', $idSemestre)
                ->first();

            if (! $docente) {
                Log::warning("⛔ Curso sin docente en ciclo siguiente: {$curso->nombre_curso}");
                continue;
            }

            DB::connection('mysql_segunda')->table('incripcion_curso')->insert([
                'idmatricula'        => $idMatricula,
                'id_docente_curso'   => $docente->iddocente_curso,
                'credito'            => $curso->credito,
                'idCalificaciones1'  => 0,
                'idCalificaciones2'  => 0,
                'idCalificaciones3'  => 0,
                'total'              => null,
                'estado_nota'        => 2,
            ]);
        }
    }

    session()->flash('success', '✅ Matrículas por traslado y ciclo siguiente registradas correctamente.');
    Log::info("📘 Matrículas completas registradas para alumno {$idAlumno}");

    $this->reset([
        'dni',
        'alumno',
        'mallaAnterior',
        'mallaNueva',
        'cicloActual',
        'cursosConvalidables',
        'cursosConvalidablesPorCiclo',
        'cursosFEPendientes',
    ]);
}


    public function render()
    {
        return view('livewire.matricula-traslado-interno');
    }
}
