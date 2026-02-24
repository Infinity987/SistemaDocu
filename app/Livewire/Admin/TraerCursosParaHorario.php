<?php

namespace App\Livewire\Admin;

use Livewire\Component;

use Illuminate\Support\Facades\DB;

class TraerCursosParaHorario extends Component
{
    public $carreras = [];
    public $años = [];
    public $semestroAcade = [];
    public $ciclos = [];

    public $idmalla;
    public $semesAca;
    public $ciclo;

    public $selectCarrera = null;

    public $selectAnioMallaCu = null;
    public $selecdSemesAca = null; ///
    public $selectCiclo = null;

    public $nomCarrera;
    public $nomAño;
    public $nomSemestre;
    public $nomciclo;

    public $nombreTipoDocenCurso;
    public $tipodocente_curso;

    public $selecTipo = []; //es para ver si es regular o subsanacion
    public $selectTipo = null; //para q en la blade, al principio sea nulo
    public $tipo;

    public function mount()
    {
        $this->carreras = DB::table('carreras')->select('idcarreras', 'nombre_de_carrera')->get();
    }

    public function traerAniosMallaPorCarrera($selectCarrera)
    {
        if ($selectCarrera == 0) {
            $this->selectAnioMallaCu = null;
            $this->selectCarrera = null;
            $this->selecdSemesAca  = null;
            $this->selectCiclo  = null;
            $this->selectTipo = null;
        } else {
            $this->selectAnioMallaCu = null;
            $this->selecdSemesAca  = null;
            $this->selectCiclo  = null;
            $this->selectTipo = null;
            $this->selectCarrera = $selectCarrera;
            $this->nomCarrera = DB::table('carreras')->select('nombre_de_carrera')->where('idcarreras', $selectCarrera)->first();
            $this->años = DB::connection('mysql_segunda')->table('malla_curricular')->select('idmalla_curricular', 'año_de_inicio')->where('carrera_malla', '=', $selectCarrera)->get();
        }
    }

    public function traerSemesAca($selectAnioMallaCu)
    {
        if ($selectAnioMallaCu == 0) {
            $this->selectAnioMallaCu = null;
            $this->selectCiclo = null;
            $this->selecdSemesAca  = null;
            $this->selectTipo = null;
        } else {
            $this->selecdSemesAca  = null;
            $this->selectCiclo  = null;
            $this->selectTipo = null;
            $this->idmalla = $selectAnioMallaCu;
            $this->nomAño = DB::connection('mysql_segunda')->table('malla_curricular')->select('año_de_inicio')->where('idmalla_curricular', $selectAnioMallaCu)->first();
            $this->semestroAcade = DB::connection('mysql_segunda')->table('semestre_academico')->select('idsemestre_academico', 'año', 'periodo')->where('estado', '1')->get();
        }
    }

    public function traerCiclos($selecdSemesAca)
    {
        if ($selecdSemesAca == 0) {
            $this->selecdSemesAca = null;
            $this->selectCiclo = null;
            $this->selectTipo = null;
        } else {
            $this->selectCiclo  = null;
            $this->selectTipo = null;
            $this->semesAca = $selecdSemesAca;

            $this->ciclos = DB::connection('mysql_segunda')->table('ciclos')->select('idciclos', 'nombre_ciclo')->get();
            $this->nomSemestre = DB::connection('mysql_segunda')->table('semestre_academico')->select('año', 'periodo')->where('idsemestre_academico', $selecdSemesAca)->first();
        }
    }

    public function traerTipo($selectCiclo)
    {
        if ($selectCiclo == 0) {
            $this->selectCiclo = null;
            $this->selectTipo = null;
        } else {
            $this->selectTipo = null;
            $this->selectCiclo  = $selectCiclo;
            // \Log::info('seelc ciclo ' . $this->selectCiclo);

            $tipoSemestreAcade = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $this->semesAca)->value('tipo_ciclo');

            if ($tipoSemestreAcade == 3) {
                $this->selecTipo = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->where('idtipo_matricula', 2)->limit(1)->get();
            } else {
                $this->selecTipo = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->orderBy('idtipo_matricula', 'asc')->limit(2)->get();
            }
        }
    }

    public function traerboton($selectTipo)
    {
        if ($selectTipo == 0) {
            $this->selectTipo = null;
        } else {
            $this->tipo = $selectTipo;

            $this->nombreTipoDocenCurso = '';
            $this->tipodocente_curso = '';

            $this->nomciclo = DB::connection('mysql_segunda')->table('ciclos')->select('nombre_ciclo')->where('idciclos', $this->selectCiclo)->first();

            $tipo_semesParOImpar = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $this->semesAca)->select('tipo_ciclo')->first();

            if ($tipo_semesParOImpar->tipo_ciclo == 1) {
                if ($this->selectCiclo % 2 == 0) {
                    $this->nombreTipoDocenCurso = 'Cursos de subsanación pares';
                    $this->tipodocente_curso = 2;
                } else {
                    $this->tipodocente_curso = 1;
                }
            } elseif ($tipo_semesParOImpar->tipo_ciclo == 2) {
                if ($this->selectCiclo % 2 != 0) {
                    $this->nombreTipoDocenCurso = 'Cursos de subsanación impares';
                    $this->tipodocente_curso = 2;
                } else {
                    $this->tipodocente_curso = 1;
                }
            } elseif ($tipo_semesParOImpar->tipo_ciclo == 3) {
                $this->nombreTipoDocenCurso = 'Cursos de subsanación verano';
                $this->tipodocente_curso = 2;
            }
        }
    }

    public function render()
    {
        return view('livewire.Admin.traer-cursos-para-horario');
    }
}
