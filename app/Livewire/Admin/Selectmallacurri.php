<?php

namespace App\Livewire\Admin;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class Selectmallacurri extends Component
{

    public $años = [];
    public $carreras = [];
    public $plan_estudios = [];
    public $precursoSeleccionados = [];
    public $cursoIdSeleccionado;
    public $cursosConPrecurso = []; // array con IDs de cursos que ya tienen prerequisitos
    public $cicloCursoSeleccionado = null;
    public $competenciasDisponibles = [];
public $competenciasSeleccionadas = [];
public $cursoIdParaCompetencias = null;
public $cursosConCompetencias = [];
public $cursoIdParaFormacion;
public $formacionSeleccionada;
public $cursosConFormacion = [];







    public $selectedAño= null;
    public $selectedCarreras= null;
    public $selectedPlan_estudio = null;

    public function mount()
    {
        $this->años = DB::table('gamnielb_sia.malla_curricular')
    ->select('año_de_inicio')
    ->distinct()
    ->get();
 $this->plan_estudios = collect(); // Esto previene errores como flatten() on array

    }

    public function handleAñoChange($año)
    {
        \Log::info("Año seleccionado: {$año}");
        $this->carreras = DB::table('gamnielb_sia.malla_curricular as malla')
            ->join('gamnielb_admision.carreras as carre', 'malla.carrera_malla', '=', 'carre.idcarreras')
            ->select('carre.nombre_de_carrera','malla.idmalla_curricular')
            ->where('malla.año_de_inicio', $año)
            ->distinct()
            ->get();
        \Log::info("carrera seleccionada: " . json_encode($this->carreras));
        $this->selectedCarreras  = null;
    }

    public function handleCarreraChange($carrera)
    {
        \Log::info("Año seleccionado: {$carrera}");
        $this->plan_estudios = DB::table('gamnielb_sia.plan_de_estudio as plan')
            ->join('gamnielb_sia.cursos as curso', 'plan.idcursos', '=', 'curso.idcursos')
            ->join('gamnielb_sia.ciclos as ciclo', 'plan.idciclos', '=', 'ciclo.idciclos')
            ->select('curso.idcursos','curso.nombre_curso','curso.credito','curso.horas','ciclo.nombre_ciclo','ciclo.idciclos')
            ->where('plan.malla_curricular_idmalla_curricular', $carrera)
            ->distinct()
            ->get();
        \Log::info("plan de estudio seleccionado: " . json_encode($this->plan_estudios));
        $this->plan_estudios = $this->plan_estudios->groupBy('nombre_ciclo');

        $this->selectedPlan_estudio  = null;

        $this->cursosConPrecurso = DB::connection('mysql_segunda')
    ->table('pre_curso')
    ->pluck('idcursos') // solo queremos los cursos con prerrequisitos
    ->toArray();

    $this->cursosConCompetencias = DB::connection('mysql_segunda')
    ->table('cursos_compe')
    ->pluck('idcursos')
    ->toArray();

    $this->cursosConFormacion = DB::connection('mysql_segunda')
        ->table('cursos')
        ->whereNotNull('Formacion')
        ->pluck('idcursos')
        ->toArray();


    }

        public function guardarPrecurso($idCurso)
{
    \Log::info('🟢 Guardando prerrequisitos para el curso: ' . $idCurso);

    // Eliminar los existentes
    DB::connection('mysql_segunda')->table('pre_curso')
        ->where('idcursos', $idCurso)
        ->delete();

    // Insertar nuevos
    foreach ($this->precursoSeleccionados as $idObligatorio) {
        DB::connection('mysql_segunda')->table('pre_curso')->insert([
            'idcursos' => $idCurso,
            'id_curso_obligatorio' => $idObligatorio,
        ]);
    }

    // 🔄 Recargar lista de cursos con prerrequisitos
    $this->cursosConPrecurso = DB::connection('mysql_segunda')
        ->table('pre_curso')
        ->pluck('idcursos')
        ->toArray();

    session()->flash('message', 'Prerrequisitos actualizados correctamente.');
    $this->dispatch('cerrar-modal-precurso');
}


#[On('abrirModalPrecurso')]
public function seleccionarCurso($idCurso)
{
    $this->cursoIdSeleccionado = $idCurso;

    $this->precursoSeleccionados = DB::connection('mysql_segunda')
        ->table('pre_curso')
        ->where('idcursos', $idCurso)
        ->pluck('id_curso_obligatorio')
        ->toArray();

    // Buscar el idciclo del curso seleccionado
    $cursoSeleccionado = collect($this->plan_estudios)->flatMap(function ($cursos) {
        return $cursos;
    })->firstWhere('idcursos', $idCurso);

    if ($cursoSeleccionado) {
        $this->cicloCursoSeleccionado = $cursoSeleccionado->idciclos; // ahora usamos el ID numérico
    } else {
        $this->cicloCursoSeleccionado = null;
    }

    // Abrir el modal después de cargar todo
    $this->dispatch('abrir-modal-precurso');
}
public function asignarCompetencias($idCurso)
{
    $this->cursoIdParaCompetencias = $idCurso;

    // Obtener el id de la malla curricular
    $idMalla = $this->selectedCarreras;

    // Traer competencias de esta malla
    $this->competenciasDisponibles = DB::connection('mysql_segunda')
    ->table('competencias as compe')
    ->join('dominio_competencia as dom', 'compe.iddominio_competencia', '=', 'dom.iddominio_competencia')
    ->where('compe.idmalla_curricular', $idMalla)
    ->select('compe.idcompetencias', 'compe.competencia', 'compe.descripcion', 'dom.Nombre_dominio', 'dom.iddominio_competencia')
    ->get()
    ->groupBy('Nombre_dominio');

    // Traer ya seleccionadas
    $this->competenciasSeleccionadas = DB::connection('mysql_segunda')
        ->table('cursos_compe')
        ->where('idcursos', $idCurso)
        ->pluck('idcompetencias')
        ->toArray();

    // Abrir modal en JS
    $this->dispatch('abrir-modal-competencias');
}

public function guardarCompetencias()
{
    DB::connection('mysql_segunda')
        ->table('cursos_compe')
        ->where('idcursos', $this->cursoIdParaCompetencias)
        ->delete();

    foreach ($this->competenciasSeleccionadas as $idCompetencia) {
        DB::connection('mysql_segunda')->table('cursos_compe')->insert([
            'idcursos' => $this->cursoIdParaCompetencias,
            'idcompetencias' => $idCompetencia
        ]);
    }

    // 🔄 Recargar lista de cursos con competencias
    $this->cursosConCompetencias = DB::connection('mysql_segunda')
        ->table('cursos_compe')
        ->pluck('idcursos')
        ->toArray();

    session()->flash('message', 'Competencias guardadas correctamente.');
    $this->dispatch('cerrar-modal-competencias');
}

public function asignarFormacion($idCurso)
{
    $this->cursoIdParaFormacion = $idCurso;

    // ✅ Usar conexión mysql_segunda
    $this->formacionSeleccionada = DB::connection('mysql_segunda')
        ->table('cursos')
        ->where('idcursos', $idCurso)
        ->value('Formacion');

    $this->dispatch('abrir-modal-formacion');
}

public function guardarFormacion()
{
    DB::connection('mysql_segunda')
        ->table('cursos')
        ->where('idcursos', $this->cursoIdParaFormacion)
        ->update(['Formacion' => $this->formacionSeleccionada]);

    // 🔄 Recargar lista de cursos con formación asignada
    $this->cursosConFormacion = DB::connection('mysql_segunda')
        ->table('cursos')
        ->whereNotNull('Formacion')
        ->pluck('idcursos')
        ->toArray();

    session()->flash('message', 'Formación actualizada correctamente.');
    $this->dispatch('cerrar-modal-formacion');
}



    public function render()
    {
        return view('livewire.Admin.selectmallacurri');
    }
}
