<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Livewire\Component;

class EliminarCompetencias extends Component
{
    public $mallaId;
    public $dominios = [];
    public $competenciaSeleccionada;
    public $dominioSeleccionado;

    public function updatedMallaId()
    {
        // Obtiene dominios y sus competencias de forma manual usando INNER JOIN
        $dominiosRaw = DB::connection('mysql_segunda')->table('dominio_competencia')->get();

        $this->dominios = [];

        foreach ($dominiosRaw as $dominio) {
            $competencias = DB::connection('mysql_segunda')->table('competencias')
                ->where('idmalla_curricular', $this->mallaId)
                ->where('iddominio_competencia', $dominio->iddominio_competencia)
                ->get();

            if ($competencias->isNotEmpty()) {
                $this->dominios[] = [
                    'dominio' => $dominio,
                    'competencias' => $competencias,
                ];
            }
        }
    }

    public function eliminarCompetencia()
    {
        DB::connection('mysql_segunda')->table('competencias')->where('idcompetencias', $this->competenciaSeleccionada)->delete();
        $this->updatedMallaId(); // Refrescar vista
    }

    public function eliminarDominioConCompetencias()
    {
       DB::connection('mysql_segunda')->table('competencias')
            ->where('iddominio_competencia', $this->dominioSeleccionado)
            ->where('idmalla_curricular', $this->mallaId)
            ->delete();

       DB::connection('mysql_segunda')->table('dominio_competencia')->where('iddominio_competencia', $this->dominioSeleccionado)->delete();

        $this->updatedMallaId(); // Refrescar vista
    }

   public function loadCompetencias()
{
    $dominiosRaw = DB::connection('mysql_segunda')->table('dominio_competencia')->get();

    $this->dominios = [];

    foreach ($dominiosRaw as $dominio) {
        $competencias = DB::connection('mysql_segunda')->table('competencias')
            ->where('idmalla_curricular', $this->mallaId)
            ->where('iddominio_competencia', $dominio->iddominio_competencia)
            ->get();

        if ($competencias->isNotEmpty()) {
            $this->dominios[] = [
                'dominio' => $dominio,
                'competencias' => $competencias,
            ];
        }
    }
}
protected $listeners = [
    'eliminarCompetenciaDirecto' => 'eliminarCompetenciaPorId',
    'eliminarDominioDirecto' => 'eliminarDominioPorId',
];

public function eliminarCompetenciaPorId($id)
{
    Log::info('Método eliminarCompetenciaPorId llamado con ID:', ['id' => $id]);

    DB::connection('mysql_segunda')->table('competencias')
        ->where('idcompetencias', $id)->delete();

    $this->updatedMallaId(); // Refresca
}


public function eliminarDominioPorId($dominioId)
{
    try {
        // Obtenemos todas las competencias del dominio
        $competencias = DB::connection('mysql_segunda')->table('competencias')
            ->where('iddominio_competencia', $dominioId)
            ->where('idmalla_curricular', $this->mallaId)
            ->get();

        foreach ($competencias as $comp) {
            // 1. Eliminar cursos_compe asociados a cada competencia
            DB::connection('mysql_segunda')->table('cursos_compe')
                ->where('idcompetencias', $comp->idcompetencias)
                ->delete();

            // 2. Eliminar la competencia
            DB::connection('mysql_segunda')->table('competencias')
                ->where('idcompetencias', $comp->idcompetencias)
                ->delete();
        }

        // 3. Finalmente eliminar el dominio
        DB::connection('mysql_segunda')->table('dominio_competencia')
            ->where('iddominio_competencia', $dominioId)
            ->delete();

        session()->flash('success', 'Dominio y sus competencias eliminados correctamente.');
        $this->loadCompetencias(); // Para refrescar la vista

    } catch (\Exception $e) {
        Log::error('❌ Error al eliminar dominio: ' . $e->getMessage());
        session()->flash('error', 'No se pudo eliminar el dominio.');
    }
}

    public function render()
    {
        $mallas = DB::connection('mysql_segunda')->table('malla_curricular')->get();

        return view('livewire.eliminar-competencias', compact('mallas'));
    }

    
}
