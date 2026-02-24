<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Selectdomi extends Component
{
    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $selectedDepartamento = null;
    public $selectedProvincia = null;
    public $selectedDistrito = null;

    public function mount()
    {
        $this->departamentos = DB::table('ubigeo')
            ->select('Departamento')
            ->distinct()
            ->get();
    }
    
    public function handleDepartamentoChange($departamento)
{
    \Log::info("Departamento seleccionado: {$departamento}");
    $this->provincias = DB::table('ubigeo')
            ->select('Provincia')
            ->where('Departamento', $departamento)
            ->distinct()
            ->get();
        
        $this->selectedProvincia = null;
        $this->distritos = [];
    
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
}


public function handleDistritoChange($distrito)
{
    \Log::info("distrito seleccionada: {$distrito}");
 
}

    public function render()
    {
        return view('livewire.selectdomi');
    }
}
