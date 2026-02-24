<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\MatriculaCerrada;

class SelectMatriculaProceso extends Component
{
    public $search = '';
public $alumnos = [];
public $semestreActivo = null;
public $cicloSugerido = null;
public $nombreCicloSugerido = null;
public $tipoCiclo = null;

public $cursosRegulares = [];
public $cursosSubsanacion = [];

public $cursosSeleccionadosRegulares = [];
public $cursosSeleccionadosSubsanacion = [];

public $codigoBoleta = null;
public $turno = null;

public $mensajeError = null;
public $matriculaCerrada = false;
public $diasRestantes = null;

public $modoReincorporacion = false;
public $licencia = null;

public $cursosYaMatriculados = [];
public array $historialPorCiclo = [];
public bool $bloqueadoPorLicencia = false;
public bool $tieneMatriculaRegular = false;
public $cursosDesaprobadosSinDocente = [];
public $cursosRegularesSinDocente= [];
public bool $aprobacionBaja = false;

public bool $licenciaActiva = false;
public ?object $semestreFinLicencia = null;
public ?object $reincorporacion = null;

public array $cursosPendientesPorLicencia = [];

public bool $esConvalidacionUbicacion = false;
public ?string $mensajeInfoCiclo = null;
public bool $mostrarMatriculaForzada = false;
public bool $ignorarLimiteCreditos = false;




public function mount()
{

  

    $this->semestreActivo = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('estado_matricula', 1)
        ->first();

    if ($this->semestreActivo) {
        $fin = \Carbon\Carbon::parse($this->semestreActivo->fecha_fin_matricula)->endOfDay();
        $ahora = now();

        $this->matriculaCerrada = $ahora->greaterThan($fin);
        $this->diasRestantes = $ahora->diffInDays($fin, false);
        $this->tipoCiclo = $this->semestreActivo->tipo_ciclo ?? 1;
    }
}

public function updatedMostrarMatriculaForzada($value)
{
    Log::debug('🧩 mostrarMatriculaForzada actualizado:', ['valor' => $value]);
}

public function resetVistaMatricula(): void
{
    $this->reset([
        'search',
        'alumnos',
        'cursosSeleccionadosRegulares',
        'cursosSeleccionadosSubsanacion',
        'codigoBoleta',
        'turno',
        'mensajeError',
        'modoReincorporacion',
        'licencia',
        'cursosYaMatriculados',
        'historialPorCiclo',
        'bloqueadoPorLicencia',
    ]);
}

public function buscarAlumno()
{
    $this->reset([
        'alumnos',
        'mensajeError',
        'modoReincorporacion',
        'licencia',
        'cursosRegulares',
        'cursosSubsanacion',
        'cursosSeleccionadosRegulares',
        'cursosSeleccionadosSubsanacion',
        'cursosYaMatriculados',
    ]);

    $dni = trim(preg_replace('/\s+/', '', $this->search));
  

    if (! is_numeric($dni)) {
        $this->mensajeError = '❌ Debes ingresar un DNI válido.';
      
        return;
    }

    $alumno = DB::connection('mysql')
        ->table('postulante')
        ->join('users', 'postulante.idpostulante', '=', 'users.dni')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('postulante.idpostulante', $dni)
        ->select(
            'postulante.*',
            'model_has_roles.role_id as rol_actual'
        )
        ->first();


    if (! $alumno) {
        $this->mensajeError = '⛔ No se encontró ningún alumno con ese DNI.';
       
        return;
    }

    if ((int) $alumno->rol_actual !== 4) {
        $this->mensajeError = '⛔ Este usuario no tiene condición de alumno activa.';
       
        return;
    }

    // 🔗 Obtener nombre de carrera
    $idAlumno = (int) $alumno->idpostulante;
$idMallaActual = $alumno->id_malla;

if (! $idMallaActual) {
  
    $alumno->nombre_de_carrera = '-';
} else {
    $malla = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->where('idmalla_curricular', $idMallaActual)
        ->first();

    $carrera = DB::connection('mysql')
        ->table('carreras')
        ->where('idcarreras', $malla->carrera_malla ?? null)
        ->select('nombre_de_carrera')
        ->first();

    $alumno->nombre_de_carrera = $carrera->nombre_de_carrera ?? '-';
}

    $this->alumnos = collect([$alumno]);

    // Verificar si tiene matrículas regulares
$matriculasRegulares = DB::connection('mysql_segunda')
    ->table('matricula')
    ->where('id_alumno', $idAlumno)
    ->where('idmalla', $idMallaActual)
    ->where('idtipo_matricula', 1)
    ->whereIn('idestado_matricula', [1, 2])
    ->count();

if ($matriculasRegulares === 0) {
    $this->mensajeInfoCiclo = 'ℹ️ Este alumno no tiene matrículas registradas. Se considera ingresante y se asignará el ciclo I por defecto.';
}

 
    
    $this->verificarLicencia($alumno->idpostulante);
    $this->cargarCursosYaMatriculados($alumno->idpostulante);
    $this->cargarCursos($alumno->idpostulante);
    $this->tieneMatriculaRegular = $this->yaTieneMatriculaRegular($idAlumno, $this->semestreActivo->idsemestre_academico);
    $this->cursosDesaprobadosSinDocente = $this->obtenerCursosDesaprobadosSinDocente($idAlumno);
    $this->cursosRegularesSinDocente = $this->obtenerCursosRegularesSinDocente($idAlumno);
    $this->aprobacionBaja = $this->aproboMenosDel75PorCiento();
    

   $ultimaMatricula = DB::connection('mysql_segunda')
    ->table('matricula')
    ->where('id_alumno', $idAlumno)
    ->where('idmalla', $idMallaActual)
     ->where('idtipo_matricula', 1)
    ->orderByDesc('fecha_matricula')
    ->first();

if ($ultimaMatricula) {
    $idMatricula = $ultimaMatricula->idmatricula;

    $licencia = DB::connection('mysql_segunda')
        ->table('licencia')
        ->where('idmatricula', $idMatricula)
        ->first();

        $this->licencia = $licencia;

    if ($licencia) {
        $semestreFin = $this->calcularSemestreFinLicencia($licencia->idsemestre_inicio, $licencia->cantidad_semestres);
        $this->licenciaActiva = $semestreFin && $this->semestreActivo->idsemestre_academico <= $semestreFin->idsemestre_academico;
        $this->semestreFinLicencia = $semestreFin;

         $this->verificarReincorporacion($licencia->idlicencia);
          $this->cursosPendientesPorLicencia = $this->obtenerCursosLicenciadosPorReincorporacion($licencia->idlicencia);
    }
   
}   
}

public function toggleMostrarMatricula(): void
{
    $this->mostrarMatriculaForzada = ! $this->mostrarMatriculaForzada;

    \Log::info('🔁 Cambio manual de visualización de matrícula regular', [
        'estado' => $this->mostrarMatriculaForzada ? 'mostrando' : 'ocultando',
        'id_alumno' => $this->alumnos[0]->idpostulante ?? null,
    ]);
}

public function toggleSeleccionarTodos()
{
    if (count($this->cursosSeleccionadosRegulares) === count($this->cursosRegulares)) {
        // Si ya están todos seleccionados, desmarcar todos
        $this->cursosSeleccionadosRegulares = [];
    } else {
        // Seleccionar todos
        $this->cursosSeleccionadosRegulares = collect($this->cursosRegulares)->pluck('idcursos')->toArray();
    }
}

private function obtenerCursosLicenciadosPorReincorporacion(int $idLicencia): array
{
    return DB::connection('mysql_segunda')
        ->table('reincorporacion')
        ->join('licencia', 'licencia.idlicencia', '=', 'reincorporacion.licencia_idlicencia')
        ->join('matricula', 'matricula.idmatricula', '=', 'licencia.idmatricula')
        ->join('incripcion_curso as ic', 'ic.idmatricula', '=', 'matricula.idmatricula')
        ->join('docente_curso as dc', 'dc.iddocente_curso', '=', 'ic.id_docente_curso')
        ->join('cursos as c', 'c.idcursos', '=', 'dc.idcursos')
        ->where('reincorporacion.licencia_idlicencia', $idLicencia)
        ->select(
            'c.idcursos',
            'c.nombre_curso',
            'c.credito',
            'c.horas',
            'matricula.ciclo_matricula'
        )
        ->get()
        ->toArray();
}

private function verificarLicencia(int $idAlumno): void
{
    $idMalla = $this->alumnos->first()?->id_malla ?? null;
    if (! $idMalla || ! $this->semestreActivo) return;

    $this->licencia = DB::connection('mysql_segunda')
        ->table('licencia')
        ->where('idmatricula', function ($query) use ($idAlumno, $idMalla) {
            $query->select('idmatricula')
                ->from('matricula')
                ->where('id_alumno', $idAlumno)
                ->where('idmalla', $idMalla)
                ->orderByDesc('fecha_matricula')
                ->limit(1);
        })
        ->first();

    if (! $this->licencia) return;

    $idSemestreActivo = $this->semestreActivo->idsemestre_academico;
    $idSemestreFinLicencia = $this->licencia->idsemestre_fin;

    // Si el semestre activo es posterior al fin de la licencia, ya no está vigente
    $this->modoReincorporacion = $idSemestreActivo > $idSemestreFinLicencia;

    $this->bloqueadoPorLicencia = $this->licencia !== null;
    Log::info('🔐 Licencia verificada', ['bloqueado' => $this->bloqueadoPorLicencia]);

}

private function cargarCursosYaMatriculados(int $idAlumno): void
{
    $idSemestre = $this->semestreActivo->idsemestre_academico ?? null;
    if (! $idSemestre) return;

    $this->cursosYaMatriculados = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idsemestre_academico', $idSemestre)
        ->pluck('dc.idcursos')
        ->toArray();
}

private function aproboMenosDel75PorCiento(): bool
{
    $idAlumno = $this->alumnos->first()?->idpostulante ?? null;
    $idMalla = $this->alumnos->first()?->id_malla ?? null;

    if (! $idAlumno || ! $idMalla) return false;

    // Buscar la última matrícula regular
    $ultimaMatricula = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $idAlumno)
        ->where('idmalla', $idMalla)
        ->where('idtipo_matricula', 1)
        ->orderByDesc('idmatricula')
        ->first();

    if (! $ultimaMatricula) return false;

    $idMatricula = $ultimaMatricula->idmatricula;

    $cursos = DB::connection('mysql_segunda')
        ->table('incripcion_curso')
        ->where('idmatricula', $idMatricula)
        ->whereIn('estado_nota', [0, 1]) // Solo cursos evaluados
        ->get();

    $totalCreditos = $cursos->sum('credito');
    $creditosAprobados = $cursos->where('estado_nota', 1)->sum('credito');

    if ($totalCreditos === 0) return false;

    $minimoRequerido = floor($totalCreditos * 0.75);

    return $creditosAprobados < $minimoRequerido;
}

private function cargarCursos(int $idAlumno): void
{
    $idMalla = $this->alumnos->first()?->id_malla ?? null;
    if (! $idMalla || ! $this->semestreActivo) return;

    $this->cicloSugerido = $this->calcularCicloSiguiente($idAlumno, $idMalla);
    $this->nombreCicloSugerido = "Ciclo {$this->cicloSugerido}";

    $this->cursosRegulares = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as curso', 'plan.idcursos', '=', 'curso.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $idMalla)
        ->where('plan.idciclos', $this->cicloSugerido)
        ->select('curso.idcursos', 'curso.nombre_curso', 'curso.credito', 'curso.horas')
        ->get()
        ->reject(fn($c) => in_array($c->idcursos, $this->cursosYaMatriculados))
        ->values();

    $this->cursosSubsanacion = $this->obtenerCursosSubsanacion($idAlumno);

    if ($this->tipoCiclo === 3) {
    $this->cursosRegulares = collect(); // vaciar cursos regulares
} else {
    $this->cursosRegulares = DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as curso', 'plan.idcursos', '=', 'curso.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $idMalla)
        ->where('plan.idciclos', $this->cicloSugerido)
        ->select('curso.idcursos', 'curso.nombre_curso', 'curso.credito', 'curso.horas')
        ->get()
        ->reject(fn($c) => in_array($c->idcursos, $this->cursosYaMatriculados))
        ->values();
}
}

public function updatedSearch($value)
{
    if (is_numeric($value) && strlen($value) >= 8) {
        $this->buscarAlumno();
    }
}

private function calcularCicloSiguiente(int $idAlumno, int $idMalla): int
{
    $ultimaMatricula = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $idAlumno)
        ->where('idmalla', $idMalla)
        ->where('idtipo_matricula', 1) // solo matrícula regular
        ->whereIn('idestado_matricula', [1, 2]) // activa o cerrada
        ->orderByDesc('idsemestre_academico')
        ->first();

    return $ultimaMatricula ? ($ultimaMatricula->ciclo_matricula + 1) : 1;
}

private function calcularSemestreFinLicencia(int $idSemestreInicio, int $cantidad): ?object
{
    $semestres = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', '>=', $idSemestreInicio)
        ->whereIn('tipo_ciclo', [1, 2])
        ->orderBy('idsemestre_academico')
        ->limit($cantidad)
        ->get();

    Log::info('📊 Semestres contados para licencia', [
        'inicio' => $idSemestreInicio,
        'cantidad' => $cantidad,
        'ids' => $semestres->pluck('idsemestre_academico')->toArray(),
        'nombres' => $semestres->map(fn($s) => $s->año . ' - ' . $s->periodo)->toArray(),
    ]);

    return $semestres->last();
}

private function obtenerCursosSubsanacion(int $idAlumno): \Illuminate\Support\Collection
{
    $idMalla = $this->alumnos->first()?->id_malla ?? null;
    $idSemestre = $this->semestreActivo->idsemestre_academico ?? null;

    if (! $idMalla || ! $idSemestre) return collect();

    $cursos = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $idMalla)
        ->whereIn('ic.estado_nota', [0])
        ->whereNotExists(function ($query) use ($idAlumno, $idMalla) {
            $query->select(DB::raw(1))
                ->from('incripcion_curso as ic2')
                ->join('docente_curso as dc2', 'ic2.id_docente_curso', '=', 'dc2.iddocente_curso')
                ->join('matricula as m2', 'ic2.idmatricula', '=', 'm2.idmatricula')
                ->whereColumn('dc2.idcursos', 'dc.idcursos')
                ->where('m2.id_alumno', $idAlumno)
                ->where('m2.idmalla', $idMalla)
                ->where('ic2.estado_nota', 1);
        })
        ->whereExists(function ($query) use ($idSemestre) {
            $query->select(DB::raw(1))
                ->from('docente_curso')
                ->whereColumn('docente_curso.idcursos', 'dc.idcursos')
                ->where('docente_curso.tipodocente_curso', 2)
                ->where('docente_curso.idsemestre_academico', $idSemestre);
        })
        ->select('c.idcursos', 'c.nombre_curso', 'c.credito', 'c.horas', 'm.ciclo_matricula')
        ->get();

    return $cursos
        ->sortByDesc('ciclo_matricula')
        ->unique('idcursos')
        ->values();
}

private function verificarReincorporacion(int $idLicencia): void
{
    $reincorporacion = DB::connection('mysql_segunda')
        ->table('reincorporacion')
        ->where('licencia_idlicencia', $idLicencia)
        ->where('semestre_reincorporacion', '<=', $this->semestreActivo->idsemestre_academico)
        ->orderByDesc('semestre_reincorporacion')
        ->first();

    $this->modoReincorporacion = $reincorporacion !== null;
    $this->reincorporacion = $reincorporacion;

    if ($reincorporacion) {
        $matriculaYaRegistrada = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('idmatricula', $reincorporacion->idmatricula)
            ->where('id_alumno', $this->alumnos->first()->idpostulante)
            ->where('idsemestre_academico', $this->semestreActivo->idsemestre_academico)
            ->exists();

        $this->modoReincorporacion = ! $matriculaYaRegistrada;
    }

}


private function obtenerCursosDesaprobadosSinDocente(int $idAlumno): \Illuminate\Support\Collection
{
    $idMalla = $this->alumnos->first()?->id_malla ?? null;
    $idSemestre = $this->semestreActivo->idsemestre_academico ?? null;

    if (! $idMalla || ! $idSemestre) return collect();

    return DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $idMalla)
        ->whereIn('ic.estado_nota', [0]) // desaprobados
        ->whereNotExists(function ($query) use ($idSemestre) {
            $query->select(DB::raw(1))
                ->from('docente_curso')
                ->whereColumn('docente_curso.idcursos', 'dc.idcursos')
                ->where('docente_curso.tipodocente_curso', 2)
                ->where('docente_curso.idsemestre_academico', $idSemestre);
        })
        ->select('c.nombre_curso', 'm.ciclo_matricula')
        ->get()
        ->unique('nombre_curso');
}

private function registrarMatriculaYCursos(array $datosMatricula, \Illuminate\Support\Collection $cursos, int $tipoDocente = 1): ?int
{
    Log::info('🧭 Entrando a registrarMatriculaYCursos', [
        'cursos_recibidos' => $cursos->pluck('idcursos')->all(),
        'datosMatricula' => $datosMatricula,
    ]);

    try {
        // 🔍 Ajuste: si es subsanación (tipoDocente = 2) y se activó ignorar límite → usar docente regular
        $tipoDocenteFinal = $tipoDocente;
        if ($tipoDocente === 2 && $this->ignorarLimiteCreditos) {
            $tipoDocenteFinal = 1;
        }

        // 🔍 Validar que todos los cursos tengan docente asignado
        $sinDocente = $cursos->filter(function ($curso) use ($datosMatricula, $tipoDocenteFinal) {
            return ! DB::connection('mysql_segunda')
                ->table('docente_curso')
                ->where('idcursos', (int) $curso->idcursos)
                ->where('idsemestre_academico', (int) $datosMatricula['idsemestre_academico'])
                ->where('tipodocente_curso', (int) $tipoDocenteFinal)
                ->exists();
        });

        if ($sinDocente->isNotEmpty()) {
            $nombres = $sinDocente->pluck('nombre_curso')->implode(', ');
            Log::warning('⚠️ Cursos sin docente asignado', [
                'id_alumno' => $datosMatricula['id_alumno'],
                'idsemestre_academico' => $datosMatricula['idsemestre_academico'],
                'tipoDocente' => $tipoDocenteFinal,
                'cursos_sin_docente' => $nombres,
            ]);
            session()->flash('error', "❌ No se puede matricular. Los siguientes cursos no tienen docente asignado: {$nombres}");
            return null;
        }

        Log::info('📝 Registrando matrícula en BD', [
            'datos' => $datosMatricula,
        ]);

        // 📝 Registrar matrícula
        $idMatricula = DB::connection('mysql_segunda')
            ->table('matricula')
            ->insertGetId($datosMatricula);

        // 📚 Registrar inscripción de cursos
        foreach ($cursos as $curso) {
            if ($this->cursoYaInscritoEnSemestre($datosMatricula['id_alumno'], $curso->idcursos, $datosMatricula['idsemestre_academico'])) {
                Log::info('⏭️ Curso ya inscrito previamente, se omite', [
                    'idcursos' => $curso->idcursos,
                ]);
                continue;
            }

            $docente = DB::connection('mysql_segunda')
                ->table('docente_curso')
                ->where('idcursos', (int) $curso->idcursos)
                ->where('idsemestre_academico', (int) $datosMatricula['idsemestre_academico'])
                ->where('tipodocente_curso', (int) $tipoDocenteFinal)
                ->first();

            Log::info('🔍 Buscando docente para curso', [
                'idcursos' => $curso->idcursos,
                'idsemestre_academico' => $datosMatricula['idsemestre_academico'],
                'tipodocente_curso' => $tipoDocenteFinal,
                'docente_encontrado' => $docente ? $docente->iddocente_curso : '❌ No encontrado',
            ]);

            if (! $docente) {
                Log::error('❌ Docente no encontrado al momento de inscribir curso', [
                    'curso' => $curso->idcursos,
                ]);
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
                'estado_nota'        => 2, // pendiente
            ]);

            Log::info('✅ Curso inscrito correctamente', [
                'idmatricula' => $idMatricula,
                'idcursos' => $curso->idcursos,
                'docente' => $docente->iddocente_curso,
            ]);
        }

        return $idMatricula;
    } catch (\Exception $e) {
        Log::error('❌ Error al registrar matrícula', [
            'exception' => $e->getMessage(),
        ]);
        session()->flash('error', '❌ Hubo un problema al registrar la matrícula.');
        return null;
    }
}

private function cursoYaInscritoEnSemestre(int $idAlumno, int $idCurso, int $idSemestre): bool
{
    return DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('dc.idcursos', $idCurso)
        ->where('m.idsemestre_academico', $idSemestre)
        ->exists();
}

public function confirmarMatricula(): void
{
    if (! in_array((string) $this->turno, ['1', '2'])) {
        session()->flash('error', '❌ Debes seleccionar un turno válido.');
        return;
    }

    if ($this->alumnos->isEmpty()) {
        session()->flash('error', '❌ No se ha seleccionado ningún alumno.');
        return;
    }

    $nombreTurno = $this->turno === '1' ? 'Turno mañana' : 'Turno tarde';
    $idAlumno = $this->alumnos->first()->idpostulante;

    $fuenteCursos = $this->modoReincorporacion ? $this->cursosPendientesPorLicencia : $this->cursosRegulares;

$regulares = collect($fuenteCursos)
    ->whereIn('idcursos', $this->cursosSeleccionadosRegulares)
    ->map(fn($c) => [
        'idcursos'     => $c->idcursos,
        'nombre_curso' => $c->nombre_curso,
        'credito'      => $c->credito,
        'horas'        => $c->horas,
    ])
    ->values();

    $subsanacion = collect($this->cursosSubsanacion)
        ->whereIn('idcursos', $this->cursosSeleccionadosSubsanacion)
        ->map(fn($c) => [
            'idcursos'     => $c->idcursos,
            'nombre_curso' => $c->nombre_curso,
            'credito'      => $c->credito,
            'horas'        => $c->horas,
        ])
        ->values();

    $this->dispatch('swal:confirmarMatricula', [
        'turno' => (string) $this->turno,
        'nombre_turno' => $nombreTurno,
        'regulares' => $regulares,
        'subsanacion' => $subsanacion,
    ]);
}


private function yaTieneMatriculaRegular(int $idAlumno, int $idSemestre): bool
{
    return DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $idAlumno)
        ->where('idsemestre_academico', $idSemestre)
        ->where('idtipo_matricula', 1) // Solo regular
        ->exists();
}

public function registrarMatriculaRegular(): void
{
    

    $alumno = $this->alumnos->first();
    $idAlumno = $alumno->idpostulante;
    $idSemestre = $this->semestreActivo->idsemestre_academico;

    if ($this->yaTieneMatriculaRegular($idAlumno, $idSemestre)) {
    session()->flash('error', '⚠️ El alumno ya tiene una matrícula regular registrada en este semestre.');
    return;
}
    $fecha = now()->format('Y-m-d');

    $cursos = collect($this->cursosRegulares)
    ->filter(fn($c) => in_array((string) $c->idcursos, $this->cursosSeleccionadosRegulares));

        Log::info('📦 Cursos seleccionados para matrícula regular', [
    'seleccionados' => $this->cursosSeleccionadosRegulares,
    'cursos_regulares_ids' => collect($this->cursosRegulares)->pluck('idcursos')->all(),
]);

    if ($cursos->isEmpty()) {
        session()->flash('error', '❌ Selecciona al menos un curso regular.');
        return;
    }

    $idMatricula = $this->registrarMatriculaYCursos([
        'id_alumno' => $idAlumno,
        'idsemestre_academico' => $idSemestre,
        'idtipo_matricula' => 1,
        'idestado_matricula' => 1,
        'fecha_matricula' => $fecha,
        'total_credito' => $cursos->sum('credito'),
        'credito_alumno' => $cursos->sum('credito'),
        'ciclo_matricula' => $this->cicloSugerido,
        'id_turno' => $this->turno,
        'idseccion' => 1,
        'idmalla' => $alumno->id_malla,
        'codigo_boleta' => $this->codigoBoleta,
        'id_reporte_matricula' => $this->esConvalidacionUbicacion ? 6 : 1,
    ], $cursos);

   if ($idMatricula) {
    session()->flash('success', '✅ Matrícula regular registrada correctamente.');
    $this->reset(['codigoBoleta', 'cursosSeleccionadosRegulares']);
    $this->dispatch('matriculaExitosa');
    $this->dispatch('cerrarModal');
}

}

private function obtenerCursosRegularesSinDocente(int $idAlumno): \Illuminate\Support\Collection
{
    $idMalla = $this->alumnos->first()?->id_malla ?? null;
    $idSemestre = $this->semestreActivo->idsemestre_academico ?? null;
    $ciclo = $this->cicloSugerido;

    if (! $idMalla || ! $idSemestre) return collect();

    return DB::connection('mysql_segunda')
        ->table('plan_de_estudio as plan')
        ->join('cursos as c', 'plan.idcursos', '=', 'c.idcursos')
        ->where('plan.malla_curricular_idmalla_curricular', $idMalla)
        ->where('plan.idciclos', $ciclo)
        ->whereNotExists(function ($query) use ($idSemestre) {
            $query->select(DB::raw(1))
                ->from('docente_curso')
                ->whereColumn('docente_curso.idcursos', 'plan.idcursos')
                ->where('docente_curso.tipodocente_curso', 1)
                ->where('docente_curso.idsemestre_academico', $idSemestre);
        })
        ->select('c.nombre_curso')
        ->get()
        ->unique('nombre_curso');
}


public function registrarMatriculaSubsanacion(): void
{
    $alumno = $this->alumnos->first();
    $idAlumno = $alumno->idpostulante;
    $idSemestre = $this->semestreActivo->idsemestre_academico;
    $fecha = now()->format('Y-m-d');

    $cursos = collect($this->cursosSubsanacion)
        ->whereIn('idcursos', $this->cursosSeleccionadosSubsanacion);

    if ($cursos->isEmpty()) {
        session()->flash('error', '❌ Selecciona al menos un curso de subsanación.');
        return;
    }

    $porCiclo = $cursos->groupBy('ciclo_matricula');

    foreach ($porCiclo as $cicloOrig => $grupo) {
        $total = $grupo->sum('credito');
        $limite = ($this->ignorarLimiteCreditos)
    ? 999 
    : $this->obtenerLimiteCreditosSubsanacion();

        if ($total > $limite) {
            session()->flash('error', "⚠️ Subsanación del ciclo {$cicloOrig} excede el máximo de {$limite} créditos.");
            return;
        }

        $idMatricula = $this->registrarMatriculaYCursos([
            'id_alumno' => $idAlumno,
            'idsemestre_academico' => $idSemestre,
            'idtipo_matricula' => 2,
            'idestado_matricula' => 1,
            'fecha_matricula' => $fecha,
            'total_credito' => $total,
            'credito_alumno' => $total,
            'ciclo_matricula' => $cicloOrig,
            'id_turno' => $this->turno,
            'idseccion' => 1,
            'idmalla' => $alumno->id_malla,
            'codigo_boleta' => $this->codigoBoleta,
            'id_reporte_matricula' => 2,
        ], $grupo, 2);
    }

    session()->flash('success', '✅ Matrícula de subsanación registrada correctamente.');
    $this->reset(['codigoBoleta', 'cursosSeleccionadosSubsanacion']);
    $this->dispatch('cerrarModal');
    $this->dispatch('matriculaExitosa');

}

private function obtenerLimiteCreditosSubsanacion(): int
{
    // Si el tipo de ciclo es intensivo (por ejemplo tipo 3), el límite es mayor
    return $this->tipoCiclo === 3 ? 16 : 8;
}

public function registrarMatricula(): void
{
    Log::info('🚀 Iniciando matrícula unificada');

    if ($this->modoReincorporacion) {
        $this->registrarMatriculaReincorporacion();
        return;
    }

    if (!empty($this->cursosSeleccionadosRegulares)) {
        $this->registrarMatriculaRegular();
    }

    if (!empty($this->cursosSeleccionadosSubsanacion)) {
        $this->registrarMatriculaSubsanacion();
    }
}

public function abrirDiagnosticoHistorial(): void
{
    $idAlumno = $this->alumnos->first()?->idpostulante ?? null;
    $idMalla = $this->alumnos->first()?->id_malla ?? null;

    if (! $idAlumno || ! $idMalla) {
        session()->flash('error', '❌ No se puede cargar el historial sin alumno válido.');
        return;
    }

    $cursos = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->where('m.id_alumno', $idAlumno)
        ->where('m.idmalla', $idMalla)
        ->select(
            'c.nombre_curso',
            'c.credito',
            'ic.total as nota',
            'ic.estado_nota',
            'm.ciclo_matricula'
        )
        ->get();

    $this->historialPorCiclo = $cursos
        ->groupBy('ciclo_matricula')
        ->map(function ($grupo) {
            return $grupo->map(function ($c) {
                return [
                    'nombre' => $c->nombre_curso,
                    'credito' => $c->credito,
                    'nota' => $c->nota,
                    'estado' => match ((int) $c->estado_nota) {
                        1 => 'Aprobado',
                        0 => 'Desaprobado',
                        default => 'Pendiente',
                    },
                ];
            });
        })
        ->toArray();

    $this->dispatch('abrirModalDiagnostico');
}

public function registrarMatriculaReincorporacion(): void
{
    $alumno = $this->alumnos->first();
    $idAlumno = $alumno->idpostulante;
    $idSemestre = $this->semestreActivo->idsemestre_academico;
    $fecha = now()->format('Y-m-d');

    $cursos = collect($this->cursosPendientesPorLicencia)
    ->whereIn('idcursos', $this->cursosSeleccionadosRegulares);

    if ($cursos->isEmpty()) {
        session()->flash('error', '❌ Selecciona al menos un curso para reincorporación.');
        return;
    }

    $idMatricula = $this->registrarMatriculaYCursos([
        'id_alumno' => $idAlumno,
        'idsemestre_academico' => $idSemestre,
        'idtipo_matricula' => 1, 
        'idestado_matricula' => 1,
        'fecha_matricula' => $fecha,
        'total_credito' => $cursos->sum('credito'),
        'credito_alumno' => $cursos->sum('credito'),
        'ciclo_matricula' => $cursos->first()->ciclo_matricula,
        'id_turno' => $this->turno,
        'idseccion' => 1,
        'idmalla' => $alumno->id_malla,
        'codigo_boleta' => $this->codigoBoleta,
        'id_reporte_matricula' => $this->esConvalidacionUbicacion ? 6 : 3,
    ], $cursos);

    if ($idMatricula) {
      DB::connection('mysql_segunda')
    ->table('reincorporacion')
    ->where('licencia_idlicencia', $this->licencia->idlicencia)
    ->update([
        'idmatricula' => $idMatricula,
    ]);


        session()->flash('success', '✅ Matrícula por reincorporación registrada correctamente.');
        $this->reset(['codigoBoleta', 'cursosSeleccionadosRegulares']);
        $this->dispatch('cerrarModal');
         $this->dispatch('matriculaExitosa');
    }
}

public function render()
{
    return view('livewire.select-matricula-proceso');
}


}