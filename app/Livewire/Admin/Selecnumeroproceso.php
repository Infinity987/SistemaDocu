<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\PostulantesPorCarreraExport;
use Maatwebsite\Excel\Facades\Excel;



class Selecnumeroproceso extends Component
{
    use WithPagination;

    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarreraId = null;
    public $idproceso = null;

    protected $paginationTheme = 'bootstrap';

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
        $this->modalidad = DB::table('vacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
            ->select('vacantes.idmodalidad', 'modalidad.nombre_modalidad')
            ->where('vacantes.idprocesos', $proceso)
            ->distinct()
            ->get();

        $this->selectedModalidad = null;
        $this->idproceso = $proceso;
        $this->selectedCarreraId = null;
        $this->carreras = [];
    }

    public function handleModalidadChange($modalidad)
    {
        $this->selectedModalidad = $modalidad;

        $inscritos = DB::table('inscripcion')
            ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->select(
                'vacantes.idvacantes',
                'carreras.nombre_de_carrera'
            )
            ->where('inscripcion.proceso_distin', $this->idproceso)
            ->where('inscripcion.modalidad_distin', $modalidad)
            ->get();

        $this->carreras = $inscritos->groupBy('idvacantes')->map(function ($items, $idvacantes) {
            return (object)[
                'idvacantes' => $idvacantes,
                'nombre_de_carrera' => $items->first()->nombre_de_carrera,
                'Numero_de_Inscritos' => $items->count()
            ];
        })->values();

        $this->selectedCarreraId = null;
    }

    public function getPostulantesProperty()
{
    logger('Carrera seleccionada: ' . $this->selectedCarreraId);

    if (!$this->selectedCarreraId) return collect();

    return DB::table('inscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->where('inscripcion.idvacantes', $this->selectedCarreraId)
        ->select(
            'postulante.idpostulante',
            'postulante.nombres_postulante',
            'postulante.apellidos_pater_postulante',
            'postulante.apellidos_mater_postulante',
            'postulante.edad_postulante'
        )
        ->paginate(10);
}
public function exportarExcel()
{
    if (!$this->selectedCarreraId) return;

    return Excel::download(new PostulantesPorCarreraExport($this->selectedCarreraId), 'postulantes.xlsx');
}

    public function render()
{
    return view('livewire.Admin.selecnumeroproceso', [
        'postulantes' => $this->getPostulantesProperty()
    ]);
}
}
