<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Selectcole extends Component
{
    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];
    public $colegio = [];

    public $selectedDepartamento = null;
    public $selectedProvincia = null;
    public $selectedDistrito = null;
    public $selectedColegio = null;

    // Nueva propiedad para los datos del colegio seleccionado
    public $colegioSeleccionado = null;

    public function mount()
    {
        $this->departamentos = DB::table('colegio')
            ->select('Departamento')
            ->distinct()
            ->get();
    }
    
    public function handleDepartamentoChange($departamento)
    {
        \Log::info("Departamento seleccionado: {$departamento}");
        $this->provincias = DB::table('colegio')
            ->select('Provincia')
            ->where('Departamento', $departamento)
            ->distinct()
            ->get();
        
        $this->selectedProvincia = null;
        $this->distritos = [];
        $this->selectedDistrito = null;
        $this->colegio = [];
        $this->selectedColegio = null;
        $this->colegioSeleccionado = null;
    }
    
    public function handleProvinciaChange($provincia)
    {
        \Log::info("Provincia seleccionada: {$provincia}");
        $this->distritos = DB::table('ubigeo')
            ->select('Ubigeo', 'Distrito')
            ->where('Provincia', $provincia)
            ->distinct()
            ->get();

        \Log::info("Distritos obtenidos: " . $this->distritos->toJson());
        $this->selectedDistrito = null;
        $this->colegio = [];
        $this->selectedColegio = null;
        $this->colegioSeleccionado = null;
    }
    
    public function handleDistritoChange($distrito)
    {
        \Log::info("Distrito seleccionado: {$distrito}");
        $this->colegio = DB::table('colegio')
            ->select('Codigo_Modular', 'Nombre_ie')
            ->where('Distrito', $distrito)
            ->distinct()
            ->get();

        $this->selectedColegio = null;
        $this->colegioSeleccionado = null;
    }
    
    public function handleColegioChange($codigoModular)
    {
        \Log::info("Colegio seleccionado: {$codigoModular}");
        $this->colegioSeleccionado = DB::table('colegio')
            ->select('Codigo_Modular', 'Direccion', 'Ubigeo','Nombre_ie')
            ->where('Codigo_Modular', $codigoModular)
            ->first();
    }
    
    public function render()
    {
        return view('livewire.selectcole');
    }
}
