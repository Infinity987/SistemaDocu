<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteMatriculaGeneralExport;


class VerResumenMatriculaPorMalla extends Component
{
    public $id_malla;
    public $mallas = [];
    public $semestreActivo;
    public $carrera = [];

    public $resumen = [];
    public $alumnosMatriculados = [];
    public $cursosPorAlumno = [];
    public $resumenPorTipo = [];
    public $docentesPorCurso = [];
    public $cursosAgrupados = [];

    public $ciclosDisponibles = [];
    public $turnosDisponibles = [];
    public $tiposMatriculaDisponibles = [];

    public $alumnoSeleccionado = null;

    public $filtroCiclo = null;
    public $filtroTurno = null;
    public $filtroTipoMatricula = null;
    public $selectedCarrera = null;
    public $codigoBoletaEditar;
    public $tipoMatriculaEditar = 1; 
    public $nombreAlumnoModal = null;
    public $cicloCambioTurno = null;
public $turnoActual = null;
public $turnoNuevo = null;
public $todosLosTurnos = [];
public $turnoEditar = null;
public $tipoMatriculaCambio = null;


public function handleCarreraChange($idCarrera)
{
    Log::info("Carrera seleccionada manualmente: {$idCarrera}");

    $this->selectedCarrera = $idCarrera;

    $this->mallas = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->select('idmalla_curricular as id', 'nombre_malla_curricular as nombre')
        ->where('carrera_malla', $idCarrera)
        ->get();

    $this->id_malla = null;
}

    public function mount()
    {
        $this->semestreActivo = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('estado_matricula', 1)
            ->first();

            $this->todosLosTurnos = DB::connection('mysql_segunda')
    ->table('turno')
    ->select('idturno', 'nombre_turno')
    ->get();

       $this->mallas = collect(); 

            $this->carrera = DB::connection('mysql')
    ->table('carreras')
    ->select('idcarreras', 'nombre_de_carrera')
    ->get();
    }

    public function cargarResumen()
{
    if (! $this->id_malla || ! $this->semestreActivo) {
        $this->resumen = [];
        $this->alumnosMatriculados = [];
        $this->cursosPorAlumno = [];
        $this->resumenPorTipo = [];
        $this->docentesPorCurso = [];
        return;
    }

    $idSemestre = $this->semestreActivo->idsemestre_academico;

    // 🔎 Filtros disponibles
    $this->ciclosDisponibles = DB::connection('mysql_segunda')
        ->table('matricula as m')
        ->join('ciclos as c', 'm.ciclo_matricula', '=', 'c.idciclos')
        ->select('c.idciclos', 'c.nombre_ciclo')
        ->where('m.idmalla', $this->id_malla)
        ->where('m.idsemestre_academico', $idSemestre)
        ->groupBy('c.idciclos', 'c.nombre_ciclo')
        ->get();

    $this->turnosDisponibles = DB::connection('mysql_segunda')
        ->table('matricula as m')
        ->join('turno as t', 'm.id_turno', '=', 't.idturno')
        ->select('t.idturno', 't.nombre_turno')
        ->where('m.idmalla', $this->id_malla)
        ->where('m.idsemestre_academico', $idSemestre)
        ->groupBy('t.idturno', 't.nombre_turno')
        ->get();

    $this->tiposMatriculaDisponibles = DB::connection('mysql_segunda')
        ->table('matricula as m')
        ->join('tipo_matricula as tm', 'm.idtipo_matricula', '=', 'tm.idtipo_matricula')
        ->select('tm.idtipo_matricula', 'tm.nombre_tipo_matricula')
        ->where('m.idmalla', $this->id_malla)
        ->where('m.idsemestre_academico', $idSemestre)
        ->groupBy('tm.idtipo_matricula', 'tm.nombre_tipo_matricula')
        ->get();

    // 📊 Resumen por ciclo/sección/turno
   $this->resumen = DB::connection('mysql_segunda')
    ->table('matricula as m')
    ->join('ciclos as c', 'm.ciclo_matricula', '=', 'c.idciclos')
    ->join('turno as t', 'm.id_turno', '=', 't.idturno')
    ->join('seccion as s', 'm.idseccion', '=', 's.idseccion') // ✅ nueva unión
    ->select(
        'c.nombre_ciclo',
        'm.idtipo_matricula',
        's.nom_seccion as nombre_seccion', // ✅ nombre de sección
        't.nombre_turno',
        DB::raw('COUNT(*) as cantidad')
    )
    ->where('m.idmalla', $this->id_malla)
    ->where('m.idsemestre_academico', $idSemestre)
    ->groupBy('c.nombre_ciclo', 'm.idtipo_matricula', 's.nom_seccion', 't.nombre_turno')
    ->get();

    // 🎯 Aplicar filtros al resumen
    $this->resumen = $this->resumen->filter(function ($r) {
        return (!$this->filtroCiclo || $r->nombre_ciclo == $this->ciclosDisponibles->firstWhere('idciclos', $this->filtroCiclo)?->nombre_ciclo)
            && (!$this->filtroTurno || $r->nombre_turno == $this->turnosDisponibles->firstWhere('idturno', $this->filtroTurno)?->nombre_turno)
            && (!$this->filtroTipoMatricula || $r->idtipo_matricula == $this->filtroTipoMatricula);
    });

    // 🧑‍🎓 Alumnos matriculados
  $matriculas = DB::connection('mysql_segunda')
    ->table('matricula as m')
    ->join('turno as t', 'm.id_turno', '=', 't.idturno')
    ->join('seccion as s', 'm.idseccion', '=', 's.idseccion')
    ->select(
        'm.*',
        't.nombre_turno',
        's.nom_seccion as nombre_seccion' 
    )
    ->where('m.idmalla', $this->id_malla)
    ->where('m.idsemestre_academico', $idSemestre)
    ->get();

    $this->alumnosMatriculados = $matriculas->map(function ($m) {
        $postulante = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $m->id_alumno)
            ->first();

        return (object) [
            'idpostulante' => $m->id_alumno,
            'idmatricula' => $m->idmatricula,
            'nombres_postulante' => $postulante->nombres_postulante ?? '—',
            'apellidos_pater_postulante' => $postulante->apellidos_pater_postulante ?? '',
            'apellidos_mater_postulante' => $postulante->apellidos_mater_postulante ?? '',
            'ciclo_matricula' => $m->ciclo_matricula,
            'idseccion' => $m->idseccion,
            'id_turno' => $m->id_turno,
            'nombre_seccion' => $m->nombre_seccion,
             'nombre_turno' => $m->nombre_turno,
             'idtipo_matricula' => $m->idtipo_matricula,
             
        ];
    });

    $this->alumnosMatriculados = $this->alumnosMatriculados
    ->sortBy(function ($a) {
        return strtolower($a->apellidos_pater_postulante . ' ' . $a->apellidos_mater_postulante);
    })
    ->values();

$this->alumnosMatriculados = $this->alumnosMatriculados->filter(function ($a) {
    return (!$this->filtroCiclo || $a->ciclo_matricula == $this->filtroCiclo)
        && (!$this->filtroTurno || $a->id_turno == $this->filtroTurno)
        && (!$this->filtroTipoMatricula || $a->idtipo_matricula == $this->filtroTipoMatricula);
});

        // 📘 Cursos por alumno
    $cursosRaw = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->select(
            'm.id_alumno',
            'm.idmatricula',
            'c.nombre_curso',
            'c.credito',
            'c.horas',
            'm.ciclo_matricula',
            'm.idseccion',
            'm.id_turno'
        )
        ->where('m.idmalla', $this->id_malla)
        ->where('m.idsemestre_academico', $idSemestre)
        ->get();

        Log::info('📊 CursosRaw obtenidos', [
    'total' => $cursosRaw->count(),
    'ids_matricula' => $cursosRaw->pluck('idmatricula')->unique()->toArray(),
    'ejemplo' => $cursosRaw->where('m.id_alumno', 71227383)->toArray(),
]);


    $this->cursosPorAlumno = $cursosRaw->map(function ($item) {
        $postulante = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $item->id_alumno)
            ->first();

        return (object) [
            'idpostulante' => $item->id_alumno,
            'idmatricula' => $item->idmatricula,
            'nombres_postulante' => $postulante->nombres_postulante ?? '—',
            'apellidos_pater_postulante' => $postulante->apellidos_pater_postulante ?? '',
            'apellidos_mater_postulante' => $postulante->apellidos_mater_postulante ?? '',
            'nombre_curso' => $item->nombre_curso,
            'credito' => $item->credito,
            'horas' => $item->horas,
            'ciclo_matricula' => $item->ciclo_matricula,
            'idseccion' => $item->idseccion,
            'id_turno' => $item->id_turno,
        ];
    });

    $this->cursosPorAlumno = $this->cursosPorAlumno->filter(function ($c) {
        return (!$this->filtroCiclo || $c->ciclo_matricula == $this->filtroCiclo)
            && (!$this->filtroTurno || $c->id_turno == $this->filtroTurno);
    });

    $this->cursosAgrupados = $this->cursosPorAlumno->groupBy(function ($item) {
    return (string) $item->idmatricula;
});

    // 📊 Totales por tipo de matrícula
    $this->resumenPorTipo = DB::connection('mysql_segunda')
        ->table('matricula')
        ->select('idtipo_matricula', DB::raw('COUNT(*) as cantidad'))
        ->where('idmalla', $this->id_malla)
        ->where('idsemestre_academico', $idSemestre)
        ->groupBy('idtipo_matricula')
        ->get();

    // 🧑‍🏫 Docentes asignados por curso (sin nombre de docente)
    $this->docentesPorCurso = DB::connection('mysql_segunda')
        ->table('docente_curso as dc')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->select('c.nombre_curso', 'dc.idsemestre_academico')
        ->where('dc.idsemestre_academico', $idSemestre)
        ->get();
}

  public function abrirModal($idPostulante)
{
     Log::info('🧑‍🎓 abrirModal llamado', [
        'idPostulante' => $idPostulante,
        'alumnoSeleccionado' => $this->alumnoSeleccionado,
    ]);

    $idPostulante = (string) $idPostulante;
    

    $matricula = DB::connection('mysql_segunda')
    ->table('matricula')
    ->where('id_alumno', $idPostulante)
    ->where('idsemestre_academico', $this->semestreActivo->idsemestre_academico)
    ->when($this->filtroCiclo, fn($q) => $q->where('ciclo_matricula', $this->filtroCiclo))
    ->when($this->filtroTurno, fn($q) => $q->where('id_turno', $this->filtroTurno))
    ->when($this->filtroTipoMatricula, fn($q) => $q->where('idtipo_matricula', $this->filtroTipoMatricula))
    ->first();

        $this->turnoEditar = $matricula->id_turno;

    if (!$matricula) {
        session()->flash('error', '❌ No se encontró matrícula para este alumno.');
        return;
    }

    $postulante = DB::connection('mysql')
        ->table('postulante')
        ->where('idpostulante', $idPostulante)
        ->first();

    $this->nombreAlumnoModal = trim(($postulante->apellidos_pater_postulante ?? '') . ' ' . ($postulante->apellidos_mater_postulante ?? '') . ', ' . ($postulante->nombres_postulante ?? '—'));

    $this->codigoBoletaEditar = $matricula->codigo_boleta ?? '';
    $this->alumnoSeleccionado = (string) $matricula->idmatricula;

    $this->dispatch('mostrar-modal-cursos');
}

   public function actualizarBoleta()
{
    DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('idmatricula', $this->alumnoSeleccionado)
        ->update([
            'codigo_boleta' => $this->codigoBoletaEditar,
            'id_turno' => $this->turnoEditar,
        ]);

    session()->flash('success', '✅ Datos actualizados correctamente.');
    $this->dispatch('cerrar-modal-cursos');
    $this->cargarResumen();
}

   public function exportarExcel()
{
    return Excel::download(
        new ReporteMatriculaGeneralExport($this->semestreActivo->idsemestre_academico),
        'Reporte_Matricula_General.xlsx'
    );
}

public function prepararCambioTurno()
{
    $this->cargarResumen(); // asegura que los filtros estén cargados
    $this->dispatch('abrir-modal-cambio-turno');
}

public function cambiarTurnoMasivo()
{
    if (! $this->id_malla || ! $this->semestreActivo || ! $this->cicloCambioTurno || ! $this->turnoActual || ! $this->turnoNuevo) {
        session()->flash('error', '⚠️ Completa todos los campos para realizar el cambio.');
        return;
    }

    $idSemestre = $this->semestreActivo->idsemestre_academico;

    // Construimos la consulta con filtros
    $query = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('idmalla', $this->id_malla)
        ->where('idsemestre_academico', $idSemestre)
        ->where('ciclo_matricula', $this->cicloCambioTurno)
        ->where('id_turno', $this->turnoActual);

    // ✅ Nuevo filtro por tipo de matrícula si está seleccionado
    if ($this->tipoMatriculaCambio) {
        $query->where('idtipo_matricula', $this->tipoMatriculaCambio);
    }

    $afectados = $query->count();

    if ($afectados === 0) {
        session()->flash('error', '⚠️ No se encontraron alumnos con esos criterios.');
        return;
    }

    // Actualizamos el turno
    $query->update(['id_turno' => $this->turnoNuevo]);

    session()->flash('success', "✅ Se actualizó el turno de {$afectados} alumnos.");
    $this->dispatch('cerrar-modal-cambio-turno');
    $this->cargarResumen();
}

    public function render()
    {
        return view('livewire.ver-resumen-matricula-por-malla');
    }
}