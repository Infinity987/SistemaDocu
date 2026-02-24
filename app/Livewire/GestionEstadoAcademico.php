<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GestionEstadoAcademico extends Component
{
    public $procesos;
    public $modalidades = [];
    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $inscritos = [];
    public $procesoActivo;

    public function mount()
{
    $this->procesoActivo = DB::table('procesos')
        ->where('estado_proceso', '1') // o el campo que uses para marcar el proceso vigente
        ->first();

    if ($this->procesoActivo) {
        $this->selectedProceso = $this->procesoActivo->idprocesos;
        $this->modalidades = DB::table('vacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
            ->select('vacantes.idmodalidad', 'modalidad.nombre_modalidad')
            ->where('vacantes.idprocesos', $this->selectedProceso)
            ->distinct()
            ->get();
    }
}

public function cargarInscritos()
{
    if (!$this->selectedProceso || !$this->selectedModalidad) {
        session()->flash('error', 'Debe seleccionar proceso y modalidad.');
        return;
    }

    $this->inscritos = DB::table('inscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
        ->leftJoin('resultados', 'resultados.idinscripcion', '=', 'inscripcion.idinscripcion')
        ->select(
            'inscripcion.idinscripcion',
            'postulante.idpostulante',
            'postulante.nombres_postulante',
            'postulante.apellidos_pater_postulante',
            'postulante.apellidos_mater_postulante',
            'postulante.id_malla',
            'carreras.nombre_de_carrera',
            'resultados.estado_ingreso'
        )
        ->where('vacantes.idmodalidad', $this->selectedModalidad)
        ->where('vacantes.idprocesos', $this->selectedProceso)
        ->orderBy('carreras.nombre_de_carrera', 'DESC')
        ->orderBy('postulante.apellidos_pater_postulante', 'ASC')
        ->get();
}

    public function updatedSelectedProceso($id)
    {
        $this->modalidades = DB::table('vacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
            ->select('vacantes.idmodalidad', 'modalidad.nombre_modalidad')
            ->where('vacantes.idprocesos', $id)
            ->distinct()
            ->get();

        $this->selectedModalidad = null;
        $this->inscritos = [];
    }

    public function updatedSelectedModalidad($id)
    {
        $this->inscritos = DB::table('inscripcion')
            ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->leftJoin('resultados', 'inscripcion.idinscripcion', '=', 'resultados.idinscripcion')
            ->select(
                'inscripcion.idinscripcion',
                'postulante.idpostulante',
                'postulante.nombres_postulante',
                'postulante.apellidos_pater_postulante',
                'postulante.apellidos_mater_postulante',
                'postulante.id_malla',
                'carreras.nombre_de_carrera',
                'resultados.estado_ingreso'
            )
            ->where('vacantes.idprocesos', $this->selectedProceso)
            ->where('vacantes.idmodalidad', $id)
            ->get();
    }

    public function promoverAlumno($idinscripcion)
{
    // Verificar que el idinscripcion existe
    $inscripcion = DB::table('inscripcion')
        ->where('idinscripcion', $idinscripcion)
        ->first();

    if (!$inscripcion) {
        session()->flash('error', 'ID de inscripción no válido.');
        return;
    }

    // Verificar si ya tiene malla o rol
    $postulante = DB::table('postulante')->where('idpostulante', $inscripcion->idpostulante)->first();
    $yaEsAlumno = DB::table('model_has_roles')
        ->where('model_id', $postulante->idpostulante)
        ->where('role_id', 4)
        ->exists();

    if ($yaEsAlumno) {
        session()->flash('error', 'Este postulante ya tiene rol de alumno.');
        return;
    }

    // Actualizar estado_ingreso en resultados
    DB::table('resultados')->updateOrInsert(
        ['idinscripcion' => $idinscripcion],
        ['estado_ingreso' => 'Alcanzó vacante']
    );

    // Asignar malla curricular
    $idcarrera = DB::table('vacantes')->where('idvacantes', $inscripcion->idvacantes)->value('idcarreras');
    $malla = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->where('idcarreras', $idcarrera)
        ->orderByDesc('id_malla')
        ->first();

    if ($malla) {
        DB::table('postulante')->where('idpostulante', $postulante->idpostulante)->update([
            'id_malla' => $malla->id_malla
        ]);
    }

    // Asignar rol de alumno
    DB::table('model_has_roles')->insert([
        'role_id' => 4,
        'model_type' => 'App\\Models\\User',
        'model_id' => $postulante->idpostulante
    ]);

    session()->flash('success', '✅ Postulante promovido a alumno correctamente.');
    $this->updatedSelectedModalidad($this->selectedModalidad); // Refrescar tabla
}

public function retirarAlumno($idpostulante)
{
    // Verificar si tiene malla
    $postulante = DB::table('postulante')->where('idpostulante', $idpostulante)->first();

    if (!$postulante || !$postulante->id_malla) {
        session()->flash('error', 'Este postulante no tiene malla asignada.');
        return;
    }

    // Retirar malla
    DB::table('postulante')->where('idpostulante', $idpostulante)->update([
        'id_malla' => null
    ]);

    // Retirar rol de alumno
    DB::table('model_has_roles')
        ->where('model_id', $idpostulante)
        ->where('role_id', 4)
        ->delete();

    session()->flash('success', '❌ Malla y rol de alumno retirados correctamente.');
    $this->updatedSelectedModalidad($this->selectedModalidad); // Refrescar tabla
}

    public function render()
    {
        return view('livewire.gestion-estado-academico');
    }
}
