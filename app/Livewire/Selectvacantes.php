<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Selectvacantes extends Component
{
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;
    public $idproceso = null;

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
        \Log::info("Proceso seleccionado: {$modalidad}");
        $this->carreras = DB::table('vacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->select('vacantes.idvacantes', 'carreras.nombre_de_carrera')
            ->where('vacantes.idmodalidad', $modalidad)
            ->where('vacantes.cantidad_vacantes', '!=', 0)
            ->where('vacantes.idprocesos',$this->idproceso)
            ->get();

        $this->selectedCarrera = null;
    }

    public function handleCarreraChange($carrera)
    {
        \Log::info("Carrera seleccionada: {$carrera}");
    }

    public function render()
    {
        return view('livewire.selectvacantes');
    }
}

