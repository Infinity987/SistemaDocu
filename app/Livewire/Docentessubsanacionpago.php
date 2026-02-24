<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Docentessubsanacionpago extends Component
{
    
    public $semestreActivo;
    public $docentes = [];
    public $docenteSeleccionado = null;
    public $alumnosDelDocente = [];

    public $tituloMemorando;
public $referenciaMemorando;

    public function mount()
    {
        $this->semestreActivo = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('estado_matricula', 1)
            ->first();

        if ($this->semestreActivo) {
            $this->cargarDocentesSubsanacion();
        }
    }

    public function cargarDocentesSubsanacion()
    {
        $this->docentes = DB::connection('mysql_segunda')
            ->table('docente_curso as dc')
            ->join('docente as d', 'dc.id_docente', '=', 'd.iddocente')
            ->join('userprofile as u', 'd.id_users', '=', 'u.id_users')
            ->where('dc.idsemestre_academico', $this->semestreActivo->idsemestre_academico)
            ->where('dc.tipodocente_curso', 2)
            ->select('d.iddocente', 'u.nombre')
            ->distinct()
            ->get();
    }

   public function updatedDocenteSeleccionado($idDocente)
{
    $registros = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('malla_curricular as mc', 'm.idmalla', '=', 'mc.idmalla_curricular')
        ->where('dc.id_docente', $idDocente)
        ->where('dc.tipodocente_curso', 2)
        ->where('dc.idsemestre_academico', $this->semestreActivo->idsemestre_academico)
        ->select(
            'm.id_alumno',
            'm.idmalla',
            'mc.carrera_malla',
            'c.nombre_curso',
            'm.ciclo_matricula'
        )
        ->get();

    $this->alumnosDelDocente = $registros->map(function ($r) {
        $alumno = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $r->id_alumno)
            ->select('nombres_postulante', 'apellidos_pater_postulante', 'apellidos_mater_postulante')
            ->first();

        $carrera = DB::connection('mysql')
            ->table('carreras')
            ->where('idcarreras', $r->carrera_malla)
            ->select('nombre_de_carrera')
            ->first();

        return (object) [
            'idpostulante' => $r->id_alumno,
            'nombres_postulante' => $alumno->nombres_postulante ?? '—',
            'apellidos_pater_postulante' => $alumno->apellidos_pater_postulante ?? '—',
            'apellidos_mater_postulante' => $alumno->apellidos_mater_postulante ?? '—',
            'nombre_curso' => $r->nombre_curso,
            'ciclo_matricula' => $r->ciclo_matricula,
            'nombre_de_carrera' => $carrera->nombre_de_carrera ?? '—',
        ];
    });
}

    public function cargarAlumnosDelDocente()
{
    if (! $this->docenteSeleccionado || ! $this->semestreActivo) return;

    $this->updatedDocenteSeleccionado($this->docenteSeleccionado);
$this->dispatch('mostrar-modal-pdf');

}

public function obtenerAlumnosDelDocente()
{
    $registros = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('malla_curricular as mc', 'm.idmalla', '=', 'mc.idmalla_curricular')
        ->where('dc.id_docente', $this->docenteSeleccionado)
        ->where('dc.tipodocente_curso', 2)
        ->where('dc.idsemestre_academico', $this->semestreActivo->idsemestre_academico)
        ->select(
            'm.id_alumno',
            'c.nombre_curso',
            'm.ciclo_matricula',
            'mc.carrera_malla'
        )
        ->get();

    return $registros->map(function ($r) {
        $alumno = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $r->id_alumno)
            ->select('nombres_postulante', 'apellidos_pater_postulante', 'apellidos_mater_postulante')
            ->first();

        $carrera = DB::connection('mysql')
            ->table('carreras')
            ->where('idcarreras', $r->carrera_malla)
            ->select('nombre_de_carrera')
            ->first();

        return (object) [
            'nombre' => "{$alumno->apellidos_pater_postulante} {$alumno->apellidos_mater_postulante}, {$alumno->nombres_postulante}",
            'curso' => $r->nombre_curso,
            'ciclo' => $r->ciclo_matricula,
            'carrera' => $carrera->nombre_de_carrera ?? '—',
        ];
    });
}

public function getDocenteSeleccionadoNombreProperty()
{
    $docente = DB::connection('mysql_segunda')
        ->table('docente as d')
        ->join('userprofile as u', 'd.id_users', '=', 'u.id_users')
        ->where('d.iddocente', $this->docenteSeleccionado)
        ->select('u.nombre')
        ->first();

    return $docente->nombre ?? '—';
}

public function getPeriodoCompletoProperty()
{
    $texto = match ((int) $this->semestreActivo->tipo_ciclo) {
        1 => 'abril a julio',
        2 => 'agosto a noviembre',
        3 => 'enero a marzo',
        default => '—',
    };

    return "{$texto} de {$this->semestreActivo->año} - {$this->semestreActivo->periodo}";
}

public function generarPdfSubsanacion()
{
    if (! $this->docenteSeleccionado || ! $this->semestreActivo) {
        session()->flash('error', '❌ Debes seleccionar un docente válido.');
        return;
    }

    $docente = DB::connection('mysql_segunda')
        ->table('docente as d')
        ->join('userprofile as u', 'd.id_users', '=', 'u.id_users')
        ->where('d.iddocente', $this->docenteSeleccionado)
        ->select('u.nombre')
        ->first();

    $periodoTexto = match ((int) $this->semestreActivo->tipo_ciclo) {
        1 => 'abril a julio',
        2 => 'agosto a noviembre',
        3 => 'enero a marzo',
        default => '—',
    };

    $periodoCompleto = "{$periodoTexto} de {$this->semestreActivo->año} - {$this->semestreActivo->periodo}";

    $alumnos = $this->obtenerAlumnosDelDocente(); // reutiliza tu lógica actual

    // Aquí rediriges a una ruta que genera el PDF
    return redirect()->route('pdf.subsanacionmemorando', [
        'titulo' => $this->tituloMemorando,
        'referencia' => $this->referenciaMemorando,
        'docente' => $docente->nombre,
        'periodo' => $periodoCompleto,
        'idDocente' => $this->docenteSeleccionado,
    ]);
}



    public function render()
    {
        return view('livewire.docentessubsanacionpago');
    }
}