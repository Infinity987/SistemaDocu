<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Selectinscri extends Component
{
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;
  public $idproceso = null;
    public $hayResultados = false;

    public function mount()
    {
        $this->procesos = DB::table('vacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->select('vacantes.idprocesos', 'procesos.nombre_proceso')
             ->where('procesos.estado_proceso', 1) 
            ->distinct()
            ->get();
    }

    public function handleProcesoChange($proceso)
    {
        \Log::info("Proceso seleccionado: {$proceso}");
        $this->modalidad = DB::table('vacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
            ->select('vacantes.idmodalidad',
                     'modalidad.nombre_modalidad',)
            ->where('vacantes.idprocesos', $proceso)
            ->distinct()
            ->get();
        \Log::info("Modalidad seleccionada: " . json_encode($this->modalidad));
        $this->selectedModalidad  = null;
          $this->idproceso  = $proceso;
    }


    public function handleModalidadChange($modalidad)
    {

        $this->carreras = DB::table('inscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
        ->select('inscripcion.idinscripcion',
                    'modalidad.nombre_modalidad',
                 'procesos.nombre_proceso',
                 DB::raw("LPAD(inscripcion.idpostulante, 8, '0') AS idpostulante"),
                 'postulante.apellidos_pater_postulante',
                 'postulante.apellidos_mater_postulante',
                 'postulante.nombres_postulante',
                 'carreras.nombre_de_carrera')
        ->where('vacantes.idmodalidad', $modalidad)
        ->where('vacantes.idprocesos',$this->idproceso)
        ->orderby('carreras.nombre_de_carrera', 'DESC')
        ->orderby('postulante.apellidos_pater_postulante', 'ASC')
        ->distinct()
        ->get();
        $this->selectedCarrera = null;
        $this->hayResultados = $this->carreras->isNotEmpty();
        // dd($this->carreras);
        $this->dispatch('tablaActualizada');
    }

    public function render()
    {
        return view('livewire.selectinscri');
    }
}
