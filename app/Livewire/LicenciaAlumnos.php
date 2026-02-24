<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LicenciaAlumnos extends Component
{
    public $semestre = [];
    public $ciclo = [];

    public $alumno = [];
    public $search = '';
    public $resolucionLicencia = '';

    public $selectedAlumno = null;
    public $selectedSemestre = null;
    public $selectedCiclo = null;
    public $matricula = null;
    public $cursosAlumno = [];
    public $semestreFinLicencia = null;
    public $motivoLicencia;
    public $semestreActivo;
    public $semestreReincorporacion = null;

    public $cantidadSemestres = 1;

    private function obtenerPeriodoDesdeTipoCiclo($tipoCiclo)
    {
        return $tipoCiclo == 1 ? 'I' : ($tipoCiclo == 2 ? 'II' : '—');
    }

    private function mapearSemestresSimuladosAIds($simulados)
    {
        $ids = [];

        foreach ($simulados as $sem) {
            $registro = DB::connection('mysql_segunda')
                ->table('semestre_academico')
                ->where('año', $sem['año'])
                ->where('tipo_ciclo', $sem['tipo_ciclo'])
                ->orderBy('idsemestre_academico')
                ->first();

            if ($registro) {
                $ids[] = $registro;
            }
        }

        return collect($ids);
    }
        public function mount()
    {
        $this->semestreActivo = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('estado_matricula', 1)
            ->first();

        $this->semestre = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->select('idsemestre_academico', 'año', 'periodo')
            ->get();

        $this->ciclo = DB::connection('mysql_segunda')->table('ciclos')
            ->select('idciclos', 'nombre_ciclo')
            ->distinct()
            ->get();
    }

    public function updatedCantidadSemestres($value)
    {
        $this->calcularSemestreFin();
    }

    public function updatedSelectedSemestre($value)
    {
        $this->calcularSemestreFin();

        $this->alumno = DB::connection('mysql')
            ->table('postulante')
            ->join('users', 'postulante.idpostulante', '=', 'users.dni')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 4)
            ->limit(10)
            ->get();
    }

    public function updatedSearch($value)
    {
        $this->alumno = DB::connection('mysql')
            ->table('postulante')
            ->join('users', 'postulante.idpostulante', '=', 'users.dni')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 4)
            ->where(function ($query) {
                $query->whereRaw("CAST(postulante.idpostulante AS CHAR) LIKE ?", ["%{$this->search}%"])
                    ->orWhereRaw("LOWER(postulante.apellidos_pater_postulante) LIKE ?", ["%" . strtolower($this->search) . "%"])
                    ->orWhereRaw("LOWER(postulante.apellidos_mater_postulante) LIKE ?", ["%" . strtolower($this->search) . "%"])
                    ->orWhereRaw("LOWER(postulante.nombres_postulante) LIKE ?", ["%" . strtolower($this->search) . "%"]);
            })
            ->limit(10)
            ->get();
    }
        public function seleccionarAlumno($id)
    {
        $this->selectedAlumno = $id;
        $this->semestreFinLicencia = null;
        $this->semestreReincorporacion = null;

        $this->matricula = DB::connection('mysql_segunda')
            ->table('matricula')
            ->where('id_alumno', $this->selectedAlumno)
            ->where('idsemestre_academico', $this->selectedSemestre)
            ->where('ciclo_matricula', $this->selectedCiclo)
            ->first();

        if ($this->matricula) {
            $idMatricula = $this->matricula->idmatricula;

            $this->cursosAlumno = DB::connection('mysql_segunda')
                ->table('matricula')
                ->join('incripcion_curso', 'matricula.idmatricula', '=', 'incripcion_curso.idmatricula')
                ->join('docente_curso', 'incripcion_curso.id_docente_curso', '=', 'docente_curso.iddocente_curso')
                ->join('cursos', 'docente_curso.idcursos', '=', 'cursos.idcursos')
                ->where('incripcion_curso.idmatricula', $idMatricula)
                ->select('cursos.nombre_curso')
                ->get();
        }
    }

        public function guardarLicencia()
{
    if (!$this->matricula || empty($this->resolucionLicencia) || !$this->selectedSemestre || !$this->cantidadSemestres) {
        session()->flash('mensaje', 'Debe seleccionar un alumno, semestre y escribir la resolución.');
        return;
    }

    $idMatricula = $this->matricula->idmatricula;

    // Obtener datos base del semestre seleccionado
    $semestreBase = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', $this->selectedSemestre)
        ->select('año', 'tipo_ciclo')
        ->first();

    if (!$semestreBase) {
        session()->flash('mensaje', 'No se encontró el semestre base.');
        return;
    }

    $año = intval($semestreBase->año);
    $tipo = intval($semestreBase->tipo_ciclo);

    // Simular avance de semestres regulares (saltando tipo 3)
    $simulados = [];
    $contador = 0;

    while ($contador < $this->cantidadSemestres) {
        if (in_array($tipo, [1, 2])) {
            $simulados[] = ['año' => $año, 'tipo_ciclo' => $tipo];
            $contador++;
        }

        // Avanzar al siguiente ciclo
        if ($tipo == 1) {
            $tipo = 2;
        } elseif ($tipo == 2) {
            $tipo = 3;
        } else {
            $tipo = 1;
            $año++;
        }
    }

    // Último semestre simulado
    $ultimo = end($simulados);
    $añoFin = $ultimo['año'];
    $tipoFin = $ultimo['tipo_ciclo'];
    $nombreSemestreFin = "{$añoFin}-{$this->obtenerPeriodoDesdeTipoCiclo($tipoFin)}";

    // Simular ID del semestre fin (cada ciclo regular avanza 2 IDs)
    $idSemestreInicio = $this->selectedSemestre;
    $idSemestreFin = $idSemestreInicio + ($this->cantidadSemestres - 1) * 2;

    // Registrar licencia
    DB::connection('mysql_segunda')->table('licencia')->insert([
        'resolucion_licencia' => $this->resolucionLicencia,
        'motivo_licencia' => $this->motivoLicencia,
        'idsemestre_inicio' => $idSemestreInicio,
        'idsemestre_fin' => $idSemestreFin,
        'Nombre_semestre_fin' => $nombreSemestreFin,
        'cantidad_semestres' => $this->cantidadSemestres,
        'idmatricula' => $idMatricula,
    ]);

    // Actualizar matrícula y cursos
    DB::connection('mysql_segunda')->table('matricula')
        ->where('idmatricula', $idMatricula)
        ->update(['idestado_matricula' => 2]);

    DB::connection('mysql_segunda')->table('incripcion_curso')
        ->where('idmatricula', $idMatricula)
        ->update(['estado_nota' => 3]);

    // Actualizar predicción visual
    $this->semestreFinLicencia = (object)[
        'año' => $añoFin,
        'periodo' => $this->obtenerPeriodoDesdeTipoCiclo($tipoFin)
    ];

    session()->flash('mensaje', 'Licencia registrada correctamente con predicción simulada.');
}

    public function calcularSemestreFin()
{
    $this->semestreFinLicencia = null;
    $this->semestreReincorporacion = null;

    if (!$this->selectedSemestre || !$this->cantidadSemestres || $this->cantidadSemestres < 1) {
        session()->flash('mensaje', 'Debe seleccionar semestre y cantidad válida de semestres.');
        return;
    }

    // Obtener semestre base
    $semestreBase = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', $this->selectedSemestre)
        ->select('año', 'tipo_ciclo')
        ->first();

    if (!$semestreBase) {
        session()->flash('mensaje', 'No se encontró el semestre base.');
        return;
    }

    $año = intval($semestreBase->año);
    $tipo = intval($semestreBase->tipo_ciclo);

    // Simular semestres futuros
    $simulados = [];
    for ($i = 0; $i < $this->cantidadSemestres; $i++) {
        $simulados[] = [
            'año' => $año,
            'tipo_ciclo' => $tipo
        ];

        // Avanzar al siguiente semestre
        if ($tipo == 1) {
            $tipo = 2;
        } else {
            $tipo = 1;
            $año++;
        }
    }

    // Último semestre de licencia
    $ultimo = end($simulados);
    $this->semestreFinLicencia = (object)[
        'año' => $ultimo['año'],
        'periodo' => $this->obtenerPeriodoDesdeTipoCiclo($ultimo['tipo_ciclo'])
    ];

    // Reincorporación: siguiente semestre
    $tipoReinc = $ultimo['tipo_ciclo'] == 1 ? 2 : 1;
    $añoReinc = $ultimo['tipo_ciclo'] == 1 ? $ultimo['año'] : $ultimo['año'] + 1;

    $this->semestreReincorporacion = (object)[
        'año' => $añoReinc,
        'periodo' => $this->obtenerPeriodoDesdeTipoCiclo($tipoReinc)
    ];
}

    public function render()
    {
        return view('livewire.licencia-alumnos');
    }
}