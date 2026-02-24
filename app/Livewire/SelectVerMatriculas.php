<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class SelectVerMatriculas extends Component
{
   public $dni                = '';
    public $alumno             = null;
    public $matriculas         = [];
    public $semestreActivo     = null;

    public $coursesModalOpen   = false;
    public $selectedCourses    = [];
    public $currentMatriculaId = null;
    public $alumnoCarrera = null;
    public $alumnoNombres = null;
public $alumnoApellidos = null;
public $calificaciones = [];


public $cicloActualNombre = null;


    public $cicloSugerido      = null;
    public $cursosSugeridos    = [];
    public $cursosSubsanacion  = [];
    public $matriculaAEliminar = null;
public $confirmarEliminacionModal = false;
public $datosMatriculaAEliminar = null;

    public function mount()
    {
        $this->semestreActivo = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('estado_matricula', 1)
            ->first();
    }

    
public function fetchMatriculas()
{
$idMalla = DB::connection('mysql')
    ->table('postulante')
    ->where('idpostulante', $this->dni)
    ->value('id_malla');

  $this->matriculas = DB::connection('mysql_segunda')
    ->table('matricula as m')
    ->join('semestre_academico as s', 'm.idsemestre_academico', '=', 's.idsemestre_academico')
    ->join('ciclos as c', 'm.ciclo_matricula', '=', 'c.idciclos')
    ->where('m.id_alumno', $this->dni)
    ->where('m.idmalla', $idMalla) // 👈 filtro por malla actual
    ->orderByDesc('m.idmatricula')
    ->select('m.*', 's.tipo_ciclo', 's.año', 's.periodo', 'c.nombre_ciclo')
    ->get();

        

    if ($this->matriculas->isEmpty()) {
        return $this->resetSuggestions();
    }

    // tomo el id_alumno de la primera matrícula
    $idAlumno = $this->matriculas->first()->id_alumno;


$idMalla  = $this->matriculas->first()->idmalla ?? null;

$carreraId = DB::connection('mysql_segunda')
    ->table('malla_curricular')
    ->where('idmalla_curricular', $idMalla)
    ->value('carrera_malla');

$carreraNombre = DB::connection('mysql')
    ->table('carreras')
    ->where('idcarreras', $carreraId)
    ->value('nombre_de_carrera');

$this->alumnoCarrera = $carreraNombre ?? '—';

$datosAlumno = DB::connection('mysql')
    ->table('postulante')
    ->where('idpostulante', $this->dni)
    ->select('nombres_postulante', 'apellidos_pater_postulante', 'apellidos_mater_postulante')
    ->first();

$this->alumnoNombres = $datosAlumno->nombres_postulante ?? '';
$this->alumnoApellidos = trim(($datosAlumno->apellidos_pater_postulante ?? '') . ' ' . ($datosAlumno->apellidos_mater_postulante ?? ''));

    $this->cicloSugerido     = $this->calcularCicloSugerido($idAlumno);
    $this->cursosSugeridos   = $this->obtenerCursosSugeridos($idAlumno);
    $this->cursosSubsanacion = $this->obtenerCursosSubsanacion($idAlumno);
}


protected function resetSuggestions()
{
    $this->cicloSugerido     = null;
    $this->cursosSugeridos   = collect();
    $this->cursosSubsanacion = collect();
}

    public function Vercursos($id)
    {
        $calificaciones = DB::connection('mysql_segunda')
    ->table('calificaciones')
    ->pluck('nom_califi', 'idCalificaciones')
    ->toArray();
         $this->calificaciones = $calificaciones;

         $cicloNombre = DB::connection('mysql_segunda')
    ->table('matricula as m')
    ->join('ciclos as c', 'm.ciclo_matricula', '=', 'c.idciclos')
    ->where('m.idmatricula', $id)
    ->value('c.nombre_ciclo');

$this->cicloActualNombre = $cicloNombre ?? '—';

        $this->currentMatriculaId = $id;
        $this->selectedCourses = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('ic.idmatricula', $id)
            ->select( 'c.nombre_curso',
    'c.credito',
    'ic.idCalificaciones1',
    'ic.recomendacion_nota1',
    'ic.idCalificaciones2',
    'ic.recomendacion_nota2',
    'ic.idCalificaciones3',
    'ic.recomendacion_nota3',
    'ic.total',
    'ic.estado_nota'
)
            ->get();

        $this->coursesModalOpen = true;
   
    }

    public function closeModal()
    {
        $this->coursesModalOpen = false;
    }



 

    private function calcularCicloSugerido(int $idAlumno): int
    {
        $tipoSemActivo = $this->semestreActivo->tipo_ciclo ?? 1;

        if ($tipoSemActivo === 3) {
            $matriculas = DB::connection('mysql_segunda')
                ->table('matricula as m')
                ->join('semestre_academico as s', 'm.idsemestre_academico', '=', 's.idsemestre_academico')
                ->where('m.id_alumno', $idAlumno)
                ->orderByDesc('m.idmatricula')
                ->select('m.*', 's.tipo_ciclo')
                ->get();

            $ultimaRecup = $matriculas->first(fn($m) => $m->tipo_ciclo === 3);
            if (! $ultimaRecup) {
                return 1;
            }

            $ultimaRegAnt = $matriculas->firstWhere(fn($m) => $m->tipo_ciclo !== 3);
            $cicloReg     = $ultimaRegAnt->ciclo_matricula ?? 1;

            $tienePendientes = DB::connection('mysql_segunda')
                ->table('incripcion_curso as ic1')
                ->join('docente_curso as dc1', 'ic1.id_docente_curso', '=', 'dc1.iddocente_curso')
                ->join('matricula as m1', 'ic1.idmatricula', '=', 'm1.idmatricula')
                ->where('m1.id_alumno', $idAlumno)
                ->where('m1.ciclo_matricula', $cicloReg)
                ->where('ic1.estado_nota', 0)
                ->whereNotExists(fn($q) =>
                    $q->select(DB::raw(1))
                      ->from('incripcion_curso as ic2')
                      ->join('docente_curso as dc2','ic2.id_docente_curso','=','dc2.iddocente_curso')
                      ->join('matricula as m2','ic2.idmatricula','=','m2.idmatricula')
                      ->whereColumn('dc2.idcursos','dc1.idcursos')
                      ->where('m2.id_alumno', $idAlumno)
                      ->where('ic2.estado_nota', 1)
                )
                ->exists();

            return $tienePendientes ? $cicloReg : $cicloReg + 1;
        }

        $matriculasReg = DB::connection('mysql_segunda')
            ->table('matricula as m')
            ->join('semestre_academico as s', 'm.idsemestre_academico', '=', 's.idsemestre_academico')
            ->where('m.id_alumno', $idAlumno)
            ->where('m.idtipo_matricula', 1)
            ->where('s.tipo_ciclo', '!=', 3)
            ->orderByDesc('m.idmatricula')
            ->select('m.*', 's.tipo_ciclo')
            ->get();

        if ($matriculasReg->isEmpty()) {
            return 1;
        }

        $ultimaReg = $matriculasReg->first();
        $ciclo     = $ultimaReg->ciclo_matricula ?? 1;

        if (! $this->cumple75PorcientoCreditos($ultimaReg)) {
            return $ciclo;
        }

        return $ciclo + 1;
    }

    private function cumple75PorcientoCreditos($ultimaMatricula)
    {
        if (! $ultimaMatricula || $ultimaMatricula->total_credito <= 0) {
            return false;
        }

        $creditosAprobados = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('ic.idmatricula', $ultimaMatricula->idmatricula)
            ->where('ic.estado_nota', 1)
            ->sum('c.credito');

        $minimosParaPasar = (int) round($ultimaMatricula->total_credito * 0.75);

        return $creditosAprobados >= $minimosParaPasar;
    }

    /**
 * @param  int  $idAlumno
 * @return \Illuminate\Support\Collection
 */
private function obtenerCursosSugeridos(int $idAlumno)
{
    // 1) Todos los cursos desaprobados
    $cursosDesaprobados = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic1')
        ->join('docente_curso as dc1', 'ic1.id_docente_curso', '=', 'dc1.iddocente_curso')
        ->join('cursos as c',        'dc1.idcursos',        '=', 'c.idcursos')
        ->join('matricula as m1',    'ic1.idmatricula',     '=', 'm1.idmatricula')
        ->where('m1.id_alumno', $idAlumno)
        ->where('ic1.estado_nota', 0)
        ->whereNotExists(function ($q) use ($idAlumno) {
            $q->select(DB::raw(1))
              ->from('incripcion_curso as ic2')
              ->join('docente_curso as dc2','ic2.id_docente_curso','=','dc2.iddocente_curso')
              ->join('matricula as m2',    'ic2.idmatricula',    '=', 'm2.idmatricula')
              ->whereColumn('dc2.idcursos','dc1.idcursos')
              ->where('m2.id_alumno', $idAlumno)
              ->where('ic2.estado_nota', 1);
        })
        ->select('c.idcursos','c.nombre_curso','m1.ciclo_matricula','c.credito','c.horas')
        ->get();

    // Si no hay registros anteriores, retorno solo los desaprobados
    $ultimaMat = DB::connection('mysql_segunda')
        ->table('matricula as m')
        ->join('semestre_academico as s','m.idsemestre_academico','=','s.idsemestre_academico')
        ->where('m.id_alumno', $idAlumno)
        ->where('s.tipo_ciclo','!=',3)
        ->orderByDesc('m.idmatricula')
        ->first();

    if ($ultimaMat && ! $this->cumple75PorcientoCreditos($ultimaMat)) {
        return $cursosDesaprobados;
    }

    // 2) Cursos de plan de estudio para este ciclo sugerido
    $cursosCiclo = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as curso','plan.idcursos','=','curso.idcursos')
        ->where('plan.idciclos', $this->cicloSugerido)
        ->select('curso.idcursos','curso.nombre_curso','curso.credito','curso.horas')
        ->get();

    // Unión y filtro final
    return collect($cursosCiclo)
        ->merge($cursosDesaprobados)
        ->unique('idcursos')
        ->values();
}


private function obtenerCursosSubsanacion(int $idAlumno)
{
    $idSem = $this->semestreActivo->idsemestre_academico;

    return DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc','ic.id_docente_curso','=','dc.iddocente_curso')
        ->join('cursos as c',        'dc.idcursos',            '=','c.idcursos')
        ->join('matricula as m',     'ic.idmatricula',         '=','m.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('ic.estado_nota', 0)
        ->whereNotExists(fn($q) =>
            $q->select(DB::raw(1))
              ->from('incripcion_curso as ic2')
              ->join('docente_curso as dc2','ic2.id_docente_curso','=','dc2.iddocente_curso')
              ->join('matricula as m2',   'ic2.idmatricula',    '=','m2.idmatricula')
              ->whereColumn('dc2.idcursos','dc.idcursos')
              ->where('m2.id_alumno', $idAlumno)
              ->where('ic2.estado_nota', 1)
        )
        ->whereExists(fn($q) =>
            $q->select(DB::raw(1))
              ->from('docente_curso')
              ->whereColumn('idcursos','dc.idcursos')
              ->where('tipodocente_curso',2)
              ->where('idsemestre_academico',$idSem)
        )
        ->select('c.idcursos','c.nombre_curso','c.credito','c.horas','m.ciclo_matricula')
        ->get();
}



public function generarPdfPorSemestre($idSemestre)
{
    $idAlumno = $this->matriculas->first()?->id_alumno;

    if (! $idAlumno) {
        session()->flash('error', 'Alumno no encontrado.');
        return;
    }

    $carrera = DB::connection('mysql_segunda')
    ->table('matricula as m')
    ->join('malla_curricular as mc', 'm.idmalla', '=', 'mc.idmalla_curricular')
    ->select('mc.nombre_malla_curricular')
    ->where('m.id_alumno', $idAlumno)
    ->orderByDesc('m.idmatricula')
    ->first();

    // Obtener datos del alumno desde la BD 'mysql'
    $alumno = DB::connection('mysql')
        ->table('postulante')     
        ->where('postulante.idpostulante', $this->dni)
        ->select(
            'postulante.apellidos_pater_postulante',
            'postulante.apellidos_mater_postulante',
            'postulante.nombres_postulante',            
        )
        ->first();
        
    // Matrículas del semestre seleccionado
    $matriculas = collect($this->matriculas)
        ->filter(fn($m) => $m->idsemestre_academico == $idSemestre);

    // Reunir cursos por matrícula
    $matriculasConCursos = $matriculas->map(function ($mat) {

      

        $cursos = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('ic.idmatricula', $mat->idmatricula)
            ->select('c.nombre_curso',
    'c.credito',
    'ic.idCalificaciones1',
    'ic.recomendacion_nota1',
    'ic.idCalificaciones2',
    'ic.recomendacion_nota2',
    'ic.idCalificaciones3',
    'ic.recomendacion_nota3',
    'ic.total',
    'ic.estado_nota'
)
            ->get();

        $mat->cursos = $cursos;
        return $mat;
    });

    $semestre = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', $idSemestre)
        ->first();

    $pdf = Pdf::loadView('pdf.constancia-matricula', [
        'alumno'     => $alumno,
        'matriculas' => $matriculasConCursos,
        'semestre'   => $semestre,
         'carrera'    => $carrera,
    ]);

   return $pdf->stream('Constancia_Matricula.pdf');

}

public function solicitarEliminacion($idMatricula)
{
    $this->matriculaAEliminar = $idMatricula;
    $this->confirmarEliminacionModal = true;

    $matricula = DB::connection('mysql_segunda')
        ->table('matricula as m')
        ->join('semestre_academico as s', 'm.idsemestre_academico', '=', 's.idsemestre_academico')
        ->join('ciclos as c', 'm.ciclo_matricula', '=', 'c.idciclos')
        ->select(
            'm.idmatricula',
            'm.fecha_matricula',
            'm.idtipo_matricula',
            's.año',
            's.periodo',
            'c.nombre_ciclo'
        )
        ->where('m.idmatricula', $idMatricula)
        ->first();

    $this->datosMatriculaAEliminar = $matricula;
}

public function eliminarMatricula()
{
    if (! $this->matriculaAEliminar) {
        session()->flash('error', 'No se ha seleccionado ninguna matrícula.');
        return;
    }

    DB::connection('mysql_segunda')
        ->table('incripcion_curso')
        ->where('idmatricula', $this->matriculaAEliminar)
        ->delete();

    DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('idmatricula', $this->matriculaAEliminar)
        ->delete();

    session()->flash('success', '✅ Matrícula eliminada correctamente.');
    $this->confirmarEliminacionModal = false;
    $this->matriculaAEliminar = null;
    $this->fetchMatriculas(); // refresca la vista
}

    public function render()
    {
        return view('livewire.select-ver-matriculas');
    }
}
