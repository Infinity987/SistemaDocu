<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Nominadematriculareporte extends Component
{

      public $carrera = [];
    public $malla = [];
    public $semestre = [];
    public $ciclo = [];
    public $turno = [];
       public $tipomatricula = [];

    public $selectedCarrera = null;
    public $selectedMalla = null;
    public $selectedSemestre = null;
    public $selectedCiclo = null;
    public $selectedTurno = null;
       public $selectedTipomatricula = null;
public $semestreActivo = null;

       

       public function mount()
    {
        $this->semestreActivo = DB::connection('mysql_segunda')
    ->table('semestre_academico')
    ->where('estado_matricula', 1)
    ->first();

        $this->carrera = DB::connection('mysql')->table("carreras")
            ->select('idcarreras', 'nombre_de_carrera')
            ->distinct()
            ->get();
    }

    public function handleCarreraChange($carreraselect)
    {
        \Log::info("Carrera seleccionada: {$carreraselect}");
        $this->malla = DB::connection('mysql_segunda')->table('malla_curricular')
            ->select('idmalla_curricular', 'nombre_malla_curricular')
            ->where('carrera_malla', $carreraselect)
            ->distinct()
            ->get();
        $this->selectedMalla = null;
    }

    public function handleMallaChange($selectedMalla)
    {
        \Log::info("Malla seleccionada: {$selectedMalla}");
        $this->semestre = DB::connection('mysql_segunda')->table('semestre_academico')
            ->select('idsemestre_academico', 'periodo','año')
             ->where('estado_matricula', 1)
            
            ->get();
        $this->selectedSemestre = null;
    }

   public function handleSemestreChange($selectsemestre)
{
    \Log::info("Semestre seleccionado: {$selectsemestre}");

    $this->selectedSemestre = $selectsemestre;

    $this->ciclo = DB::connection('mysql_segunda')
        ->table('matricula')
        ->join('ciclos', 'matricula.ciclo_matricula', '=', 'ciclos.idciclos')
        ->select('ciclos.idciclos', 'ciclos.nombre_ciclo')
        ->where('matricula.idmalla', $this->selectedMalla)
        ->where('matricula.idsemestre_academico', $this->selectedSemestre)
        ->distinct()
        ->get();

    $this->selectedCiclo = null;
}

   public function handleCicloChange($selectciclo)
{


    $this->selectedCiclo = $selectciclo;

    $this->turno = DB::connection('mysql_segunda')
        ->table('matricula')
        ->join('turno', 'matricula.id_turno', '=', 'turno.idturno')
        ->select('turno.idturno', 'turno.nombre_turno')
        ->where('idmalla', $this->selectedMalla)
        ->where('idsemestre_academico', $this->selectedSemestre)
        ->where('ciclo_matricula', $this->selectedCiclo)
        ->distinct()
        ->get();

    $this->selectedTurno = null;
}

    
     public function handleTurnoChange($selectturno)
{
    \Log::info("Turno seleccionado: {$selectturno}");

    $this->selectedTurno = $selectturno;

    $this->tipomatricula = DB::connection('mysql_segunda')
        ->table('matricula')
        ->join('tipo_matricula', 'matricula.idtipo_matricula', '=', 'tipo_matricula.idtipo_matricula')
        ->select('tipo_matricula.idtipo_matricula', 'tipo_matricula.nombre_tipo_matricula')
        ->where('idmalla', $this->selectedMalla)
        ->where('idsemestre_academico', $this->selectedSemestre)
        ->where('ciclo_matricula', $this->selectedCiclo)
        ->where('id_turno', $this->selectedTurno)
        ->distinct()
        ->get();

    $this->selectedTipomatricula = null;
}

     public function handleMatriculaChange($selecttipomatricula)
    {
        \Log::info("selecttipomatricula seleccionada: {$selecttipomatricula}");
    }

    public function render()
    {
        return view('livewire.nominadematriculareporte');
    }
}
