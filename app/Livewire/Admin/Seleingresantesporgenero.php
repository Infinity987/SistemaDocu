<?php

namespace App\Livewire\Admin;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Seleingresantesporgenero extends Component
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

    $this->carreras = DB::table('inscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->join('resultados', 'inscripcion.idinscripcion', '=', 'resultados.idinscripcion')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->select(
            DB::raw('COUNT(inscripcion.idinscripcion) AS Numero_de_Inscritos'),
            DB::raw('SUM(CASE WHEN resultados.estado_ingreso = "Alcanzó vacante" THEN 1 ELSE 0 END) AS Numero_de_Ingresantes'),
            DB::raw('SUM(CASE WHEN resultados.estado_ingreso = "Alcanzó vacante" AND postulante.genero_postulante = "Hombre" THEN 1 ELSE 0 END) AS Ingresantes_Hombres'),
            DB::raw('SUM(CASE WHEN resultados.estado_ingreso = "Alcanzó vacante" AND postulante.genero_postulante = "Mujer" THEN 1 ELSE 0 END) AS Ingresantes_Mujeres'),
            'carreras.nombre_de_carrera'
        )
        ->where('modalidad_distin', $modalidad)
        ->where('vacantes.idprocesos',$this->idproceso)
        ->groupBy('carreras.nombre_de_carrera') // Agrupar por el nombre de la carrera
        ->get();

    $this->selectedCarrera = null;
}


    public function handleCarreraChange($carrera)
    {
        \Log::info("Carrera seleccionada: {$carrera}");
    }

    public function getAllNotasTotalesPresentProperty()
    {
        // Si $this->carreras contiene objetos
        return collect($this->carreras)->every(function ($carrera) {
            return !is_null($carrera->nota_total); // Cambia 'nota_total' si es un atributo diferente
        });
    }


    public function render()
    {
        return view('livewire.Admin.seleingresantesporgenero');
    }
}
