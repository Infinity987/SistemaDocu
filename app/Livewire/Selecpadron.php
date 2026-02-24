<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Selecpadron extends Component
{
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;
     public $idproceso = null;
     public $numerodealumnosadividir;
public $numerodeaula;
public $distribucion = [];

public function calcularDistribucion()
{
    if (is_numeric($this->numerodealumnosadividir) && $this->numerodealumnosadividir > 0) {
        $total = $this->carreras[0]->total ?? 0;
        $this->numerodeaula = ceil($total / $this->numerodealumnosadividir);

        $this->distribucion = [];
        for ($i = 1; $i <= $this->numerodeaula; $i++) {
            $cantidad = ($i < $this->numerodeaula) ? $this->numerodealumnosadividir : $total - ($this->numerodealumnosadividir * ($this->numerodeaula - 1));
            $this->distribucion[] = ['aula' => $i, 'cantidad' => $cantidad];
        }
    } else {
        $this->numerodeaula = null;
        $this->distribucion = [];
    }
}

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
    $this->carreras = DB::table('inscripcion')            
        ->select(DB::raw('COUNT(idpostulante) AS total')) // Utiliza DB::raw para expresiones SQL
         ->where('modalidad_distin', $modalidad)
         ->where('inscripcion.proceso_distin',$this->idproceso)       
        ->get();

    $this->selectedCarrera = null;
}



    public function handleCarreraChange($carrera)
    {
        \Log::info("Carrera seleccionada: {$carrera}");
    }

  


    public function render()
    {
        return view('livewire.selecpadron');
    }
}
