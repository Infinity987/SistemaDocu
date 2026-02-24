<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VerLicenciaAlumnos extends Component
{
    public $carrera = [];
    public $malla = [];
    public $semestre = [];
    public $licencias = [];

    public $selectedCarrera = null;
    public $selectedMalla  = null;
    public $selectedSemestre = null;

    public $modalEditar = false;

    // Campos edición
    public $licenciaId;
    public $resolucionLicencia;
    public $motivoLicencia;
    public $idsemestre_fin;
    public $cantidadSemestres;
    public $semestreFinLicencia;
    public $anio_inicio;
    public $periodo_inicio;
    //capos para reincorporacion
    public $modalReincorporacion = false;
public $licenciaIdReincorporacion;
public $resolucionReincorporacion;
public $semestreActualId;
public $semestreActualNombre;

    public function mount()
    {
        $this->carrera = DB::connection('mysql')
            ->table("carreras")
            ->select('idcarreras','nombre_de_carrera')
            ->distinct()
            ->get();

  $semestre = DB::connection('mysql_segunda')
    ->table('semestre_academico')
    ->where('estado_matricula', 1)
    ->first();

$this->semestreActualId = $semestre ? $semestre->idsemestre_academico : null;
$this->semestreActualNombre = $semestre ? "{$semestre->año} - {$semestre->periodo}" : 'No definido';
    }

    public function handleCarreraChange($carreraSelect)
    {
        $this->malla = DB::connection('mysql_segunda')
            ->table('malla_curricular')
            ->select('idmalla_curricular', 'nombre_malla_curricular')
            ->where('carrera_malla', $carreraSelect)
            ->distinct()
            ->get();
        $this->selectedMalla = null;
    }

    public function handleMallaChange($selectedMalla)
    {
        $this->semestre = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->select('idsemestre_academico', 'periodo', 'año')
            ->distinct()
            ->get();
        $this->selectedSemestre  = null;
    }

    public function buscarLicencias()
    {
        // Licencias + matrícula
        $licenciasDB = DB::connection('mysql_segunda')
    ->table('licencia')
    ->leftJoin('matricula', 'licencia.idmatricula', '=', 'matricula.idmatricula')
    ->leftJoin('semestre_academico as inicio', 'licencia.idsemestre_inicio', '=', 'inicio.idsemestre_academico')
    ->leftJoin('semestre_academico as fin', 'licencia.idsemestre_fin', '=', 'fin.idsemestre_academico')
    ->select(
        'licencia.idlicencia',
        'licencia.idmatricula',
        'matricula.id_alumno',
        'licencia.resolucion_licencia',
        'licencia.motivo_licencia',
        'inicio.año as anio_inicio',
        'inicio.periodo as periodo_inicio',
        'fin.año as anio_fin',
        'fin.periodo as periodo_fin',
        'licencia.Nombre_semestre_fin',
        'licencia.cantidad_semestres'
    )
    ->when($this->selectedSemestre, function ($query) {
        $query->where('licencia.idsemestre_inicio', $this->selectedSemestre);
    })
    ->when($this->selectedMalla, function ($query) {
        $query->where('matricula.idmalla', $this->selectedMalla);
    })
    ->get();

            $reincorporaciones = DB::connection('mysql_segunda')
    ->table('reincorporacion')
    ->select('licencia_idlicencia')
    ->get()
    ->pluck('licencia_idlicencia')
    ->toArray();

        // Obtener postulantes
        $idsPostulantes = $licenciasDB->pluck('id_alumno')->toArray();

        $postulantes = DB::connection('mysql')
            ->table('postulante')
            ->whereIn('idpostulante', $idsPostulantes)
            ->select(
                'idpostulante',
                'apellidos_pater_postulante',
                'apellidos_mater_postulante',
                'nombres_postulante'
            )
            ->get()
            ->keyBy('idpostulante');

        // Combinar datos
       $this->licencias = $licenciasDB->map(function ($licencia) use ($postulantes, $reincorporaciones) {
    $postulante = $postulantes[$licencia->id_alumno] ?? null;
    $licencia->apellidos = $postulante
        ? $postulante->apellidos_pater_postulante . ' ' . $postulante->apellidos_mater_postulante
        : '';
    $licencia->nombres = $postulante->nombres_postulante ?? '';
    $licencia->reincorporado = in_array($licencia->idlicencia, $reincorporaciones);
    return $licencia;
});
    }

    public function calcularSemestreFin()
{
    if (!$this->anio_inicio || !$this->periodo_inicio || !$this->cantidadSemestres) {
        session()->flash('mensaje', 'Faltan datos para calcular el semestre de reincorporación.');
        return;
    }

    // Traer todos los semestres válidos (sin extraordinarios)
    $semestres = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->whereIn('tipo_ciclo', [1, 2])
        ->orderBy('año')
        ->orderBy('periodo')
        ->get();

    // Buscar el índice del semestre de inicio
    $indiceInicio = $semestres->search(function ($sem) {
        return $sem->año == $this->anio_inicio && $sem->periodo == $this->periodo_inicio;
    });

    if ($indiceInicio === false) {
        session()->flash('mensaje', 'No se encontró el semestre de inicio en la base de datos.');
        return;
    }

    // Calcular índice final
    $indiceFin = $indiceInicio + $this->cantidadSemestres;

    if (!isset($semestres[$indiceFin])) {
        session()->flash('mensaje', 'No hay suficientes semestres en la base de datos para calcular.');
        return;
    }

    // Guardar el resultado
    $this->semestreFinLicencia = $semestres[$indiceFin];
    $this->idsemestre_fin = $semestres[$indiceFin]->idsemestre_academico;
}

    public function eliminarLicencia($idLicencia)
    {
        $licencia = DB::connection('mysql_segunda')->table('licencia')->where('idlicencia', $idLicencia)->first();

        if ($licencia) {
            DB::connection('mysql_segunda')->table('matricula')
                ->where('idmatricula', $licencia->idmatricula)
                ->update(['idestado_matricula' => 1]);

            DB::connection('mysql_segunda')->table('incripcion_curso')
                ->where('idmatricula', $licencia->idmatricula)
                ->update(['estado_nota' => 2]);

            DB::connection('mysql_segunda')->table('licencia')->where('idlicencia', $idLicencia)->delete();
        }

        $this->buscarLicencias();
        session()->flash('mensaje', 'Licencia eliminada y matrícula restaurada.');
    }

    public function abrirModalEditar($id)
    {
        $licencia = DB::connection('mysql_segunda')
            ->table('licencia')
            ->join('semestre_academico as inicio', 'licencia.idsemestre_inicio', '=', 'inicio.idsemestre_academico')
            ->where('licencia.idlicencia', $id)
            ->select('licencia.*', 'inicio.año as anio_inicio', 'inicio.periodo as periodo_inicio')
            ->first();

        if ($licencia) {
            $this->licenciaId = $licencia->idlicencia;
            $this->resolucionLicencia = $licencia->resolucion_licencia;
            $this->motivoLicencia = $licencia->motivo_licencia;
            $this->idsemestre_fin = $licencia->idsemestre_fin;
            $this->cantidadSemestres = $licencia->cantidad_semestres;
            $this->anio_inicio = $licencia->anio_inicio;
            $this->periodo_inicio = $licencia->periodo_inicio;

            $this->dispatch('abrir-modal-editar');
        }
    }

    public function actualizarLicencia()
{
    $datos = [];

    if (!empty($this->resolucionLicencia)) {
        $datos['resolucion_licencia'] = $this->resolucionLicencia;
    }
    if (!empty($this->motivoLicencia)) {
        $datos['motivo_licencia'] = $this->motivoLicencia;
    }
    if (!empty($this->cantidadSemestres)) {
        $datos['cantidad_semestres'] = $this->cantidadSemestres;
    }
    if (!empty($this->idsemestre_fin)) {
        $datos['idsemestre_fin'] = $this->idsemestre_fin;
    }

    if (!empty($datos)) {
        DB::connection('mysql_segunda')
            ->table('licencia')
            ->where('idlicencia', $this->licenciaId)
            ->update($datos);

        session()->flash('mensaje', 'Licencia actualizada correctamente.');
        $this->dispatch('cerrar-modal-editar');
    } else {
        session()->flash('mensaje', 'No hay datos para actualizar.');
    }
}

public function abrirModalReincorporacion($idLicencia)
{
    $this->licenciaIdReincorporacion = $idLicencia;
    $this->resolucionReincorporacion = '';
    $this->modalReincorporacion = true;
    $this->dispatch('abrir-modal-reincorporacion');
}
public function guardarReincorporacion()
{
    $this->validate([
        'resolucionReincorporacion' => 'required|string|max:100',
        'licenciaIdReincorporacion' => 'required|integer',
        'semestreActualId' => 'required|integer',
    ]);

    // Verificar si ya existe una reincorporación para esta licencia
    $existe = DB::connection('mysql_segunda')
        ->table('reincorporacion')
        ->where('licencia_idlicencia', $this->licenciaIdReincorporacion)
        ->exists();

    if ($existe) {
        session()->flash('mensaje', '⚠️ Esta licencia ya tiene una reincorporación registrada.');
        return;
    }

    DB::connection('mysql_segunda')->table('reincorporacion')->insert([
        'resolucion_reincorporacion' => $this->resolucionReincorporacion,
        'semestre_reincorporacion' => $this->semestreActualId,
        'licencia_idlicencia' => $this->licenciaIdReincorporacion,
        'idmatricula' => null,
    ]);

    $this->modalReincorporacion = false;
    $this->dispatch('cerrar-modal-reincorporacion');
    session()->flash('mensaje', '✅ Reincorporación registrada correctamente.');
    $this->buscarLicencias();
}

public function generarPDF($idLicencia)
{
    // Obtener la licencia con joins seguros
    $licencia = DB::connection('mysql_segunda')
        ->table('licencia')
        ->leftJoin('matricula', 'licencia.idmatricula', '=', 'matricula.idmatricula')
        ->leftJoin('semestre_academico as inicio', 'licencia.idsemestre_inicio', '=', 'inicio.idsemestre_academico')
        ->leftJoin('semestre_academico as fin', 'licencia.idsemestre_fin', '=', 'fin.idsemestre_academico')
        ->where('licencia.idlicencia', $idLicencia)
        ->select(
            'licencia.*',
            'inicio.año as anio_inicio',
            'inicio.periodo as periodo_inicio',
            'fin.año as anio_fin',
            'fin.periodo as periodo_fin',
            'matricula.id_alumno'
        )
        ->first();

    // Validar existencia de la licencia
    if (!$licencia) {
        return response()->json(['error' => 'Licencia no encontrada'], 404);
    }

    // Buscar postulante solo si hay id_alumno
    $postulante = null;
    if (!empty($licencia->id_alumno)) {
        $postulante = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $licencia->id_alumno)
            ->select('apellidos_pater_postulante', 'apellidos_mater_postulante', 'nombres_postulante', 'idpostulante')
            ->first();
    }

    // Preparar nombre completo del alumno
    $nombreCompleto = $postulante
        ? "{$postulante->apellidos_pater_postulante} {$postulante->apellidos_mater_postulante}, {$postulante->nombres_postulante}"
        : 'Alumno no vinculado';

    // Preparar semestre fin (oficial o estimado)
    $semestreFin = ($licencia->anio_fin && $licencia->periodo_fin)
        ? "{$licencia->anio_fin} - {$licencia->periodo_fin}"
        : ($licencia->Nombre_semestre_fin ?? 'No definido');

    // Preparar datos para la vista PDF
    $data = [
        'licencia' => $licencia,
        'postulante' => $postulante,
        'nombreCompleto' => $nombreCompleto,
        'institucion' => 'EESP "Gamaniel Blanco Murillo"',
        'resolucion' => $licencia->resolucion_licencia,
        'semestre_inicio' => "{$licencia->anio_inicio} - {$licencia->periodo_inicio}",
        'semestre_fin' => $semestreFin,
        'semestre_reincorporacion' => $licencia->Nombre_semestre_fin,
    ];

    // Generar y retornar el PDF
    $pdf = Pdf::loadView('pdf.licencia', $data);
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, 'licencia_'.$licencia->idlicencia.'.pdf');
}

    public function render()
    {
        return view('livewire.Admin.ver-licencia-alumnos');
    }
}
