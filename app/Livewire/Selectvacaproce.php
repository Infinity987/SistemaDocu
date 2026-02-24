<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VacantesExport;


class Selectvacaproce extends Component
{
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;
    public $idproceso = null;


    protected $listeners = ['eliminarVacantesConfirmado' => 'eliminarVacantesPorModalidad'];

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
    \Log::info("Modalidad seleccionada: {$modalidad}");
    
    $this->carreras = DB::table('vacantes')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
        ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
        ->select(
            'modalidad.nombre_modalidad',
            'procesos.nombre_proceso',
            'carreras.nombre_de_carrera',
            'vacantes.cantidad_vacantes',
            'vacantes.idvacantes'
        )
        ->where('vacantes.idmodalidad', $modalidad) // Cambiado a idmodalidad
         ->where('vacantes.idprocesos',$this->idproceso)
        ->distinct()
        ->get();
    
    \Log::info("Carreras obtenidas: " . json_encode($this->carreras));
    $this->selectedCarrera = null;
}   

public function handleCarreraChange($carrera)
{
    \Log::info("Carrera seleccionada: {$carrera}");
    $this->selectedCarrera = $carrera;  
}   

public function eliminarVacantesPorModalidad()
{
    if (!$this->selectedModalidad) return;

    DB::table('vacantes')
        ->where('idmodalidad', $this->selectedModalidad)
        ->delete();

    \Log::info("Vacantes eliminadas para modalidad: {$this->selectedModalidad}");

    // Limpieza visual
    $this->carreras = [];
    $this->selectedCarrera = null;

    session()->flash('message', 'Vacantes eliminadas correctamente.');
}

public function exportarExcel()
{
    return Excel::download(new VacantesExport($this->carreras), 'vacantes_reporte.xlsx');
}



public function render()
{
    return view('livewire.selectvacaproce', [
        'procesos' => $this->procesos,
        'carreras' => $this->carreras
    ]);
}

}
