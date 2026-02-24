<?php

namespace App\Livewire\Admin;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Selecconstancia extends Component
{
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;

    public function mount()
    {
        $this->procesos = DB::table('vacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->select('vacantes.idprocesos', 'procesos.nombre_proceso')
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
    }

    public function handleModalidadChange($modalidad)
    {
        \Log::info("Proceso seleccionado: {$modalidad}");
        $this->carreras = DB::table('inscripcion')
    ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
    ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
    ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
    ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
    ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
    ->leftjoin('resultados', 'resultados.idinscripcion', '=', 'inscripcion.idinscripcion')
    ->select('inscripcion.idinscripcion',
               'modalidad.nombre_modalidad',
            'procesos.nombre_proceso',
             'procesos.idprocesos',
             'inscripcion.idpostulante',
             'postulante.apellidos_pater_postulante',
             'postulante.apellidos_mater_postulante',
             'postulante.nombres_postulante',
             'carreras.nombre_de_carrera',
             'resultados.idresultados',
             'resultados.estado_apro_desa',
             'resultados.estado_ingreso',
             'resultados.nota_total')
    ->where('vacantes.idmodalidad', $modalidad)
    ->where('resultados.estado_ingreso', "Alcanzó vacante" )
    ->distinct()
    ->get()
    ->toArray(); // Convierte la colección en un array, si es necesario

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
        return view('livewire.Admin.selecconstancia');
    }
}
