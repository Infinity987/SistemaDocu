<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CambiarCarrera extends Component
{
    public $idInscripcion;
    public $carreras = [];
    public $proceso_distin;
    public $modalidad_distin;

    public function mount($idInscripcion)
    {
        $this->idInscripcion = $idInscripcion;

        // Obtener el proceso desde la tabla inscripcion
        $proceso = DB::table('inscripcion')
            ->where('idinscripcion', $this->idInscripcion)
            ->select('proceso_distin', 'modalidad_distin')
            ->first();

        $this->proceso_distin = $proceso->proceso_distin;
        $this->modalidad_distin = $proceso->modalidad_distin;

        // Si se encontró el proceso, filtrar las carreras
        if ($proceso) {
            $this->carreras = DB::table('vacantes')
                ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
                ->select('vacantes.idvacantes', 'carreras.nombre_de_carrera')
                ->where('vacantes.idprocesos', $this->proceso_distin)
                ->where('vacantes.idmodalidad', $this->modalidad_distin)
                ->where('vacantes.cantidad_vacantes', '>', 0)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.cambiar-carrera');
    }
}
