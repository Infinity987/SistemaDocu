<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

use function Laravel\Prompts\select;

class ReporteActaEvaluacion extends Component
{

    public $carreras = [];
    public $años = [];
    public $ciclos = [];
    public $cursos = [];
    public $semestroAcade = [];
    public $docentes = [];
    public $curYaAsig = [];

    public $idmalla;
    public $semesAca;
    public $ciclo;

    public $selectCarrera = null;
    public $selectAnioMallaCu = null;
    public $selectCiclo = null;
    public $selecdSemesAca = null; ///

    public $selectDocente = null; ///
    public $asignaciones = []; ///

    public $bandera = 0;
    public $cursoos = [];

    public $actualizar = false;
    public $nombreTipoDocenCurso;
    public $tipodocente_curso;
    public $selecTurno = []; //es para ver si es tarde o mañana
    public $selectTurno = null; //para q en la blade, al principio sea nulo
    public $selecTipo = []; //es para ver si es regular o subsanacion
    public $selectTipo = null; //para q en la blade, al principio sea nulo
    public $tipo;

    protected function rules()
    {
        return [
            'selecdSemesAca' => [
                'required',
                Rule::exists('mysql_segunda.semestre_academico', 'idsemestre_academico'),
            ],
        ];
    }

    protected function messages()
    {
        return [
            'selecdSemesAca.required' => 'Seleccione un semestre ...',
            'selecdSemesAca.exists' => 'Semestre seleccionado no esta registrado ...',
        ];
    }

    public function mount()
    {
        $this->carreras = DB::table('carreras')->select('codigo_de_carrera', 'nombre_de_carrera')->get();
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

            // \Log::info('seelc semestre academico id ' . $this->semesAca);

            $this->ciclos = DB::connection('mysql_segunda')->table('ciclos')->select('idciclos', 'nombre_ciclo')->get();
        }
    }

    public function traerTurno($selectCiclo)
    {
        if ($selectCiclo == 0) {
            $this->selectCiclo = null;
            $this->selectTurno = null;
            $this->selectTipo = null;
        } else {
            $this->selectCiclo  = $selectCiclo;
            $this->selectTurno = null;   // ✔ reset turno
            $this->selectTipo = null;    // ✔ reset tipo
            // \Log::info('seelc ciclo ' . $this->selectCiclo);

            $this->selecTurno = DB::connection('mysql_segunda')->table('turno')->select('idturno', 'nombre_turno')->get();
        }
    }

    public function traerTipo($selectTurno)
    {
        if ($selectTurno == 0) {
            $this->selectTurno = null;
            $this->selectTipo = null;
        } else {
            $this->selectTurno = $selectTurno;   // ✔ asignar directamente
            $this->selectTipo = null;            // ✔ limpiar solo tipo
            // \Log::info('seelc turno ' . $this->selectTurno);

            $tipoSemestreAcade = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $this->semesAca)->value('tipo_ciclo');

            if ($tipoSemestreAcade == 3) {
                $this->selecTipo = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->where('idtipo_matricula', 2)->limit(1)->get();
            } else {
                $this->selecTipo = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->orderBy('idtipo_matricula', 'asc')->limit(2)->get();
            }
        }
    }

    public function traerCursos($selectTipo)
    {
        // \Log::info('selec tipo ' . $selectTipo);
        // \Log::info('---- ciclo ' . $this->selectCiclo);

        if ($selectTipo == 0) {
            $this->selectTipo = null;
        } else {
            $this->asignaciones = [];
            $this->cursoos = [];
            $this->docentes = [];

            $this->nombreTipoDocenCurso = '';
            $this->tipodocente_curso = '';


            $this->cursoos = DB::connection('mysql_segunda')->select('SELECT DISTINCT
                        pe.idplan_de_estudio,
                        c.idcursos,
                        c.nombre_curso,
                        dc.iddocente_curso,
                        dc.id_docente AS dc_iddoce,
                        up.nombre
                    FROM plan_de_estudio pe

                    INNER JOIN cursos c
                        ON pe.idcursos = c.idcursos

                    INNER JOIN docente_curso dc
                        ON dc.idcursos = c.idcursos
                        AND dc.idsemestre_academico = ?

                    INNER JOIN docente d
                        ON dc.id_docente = d.iddocente
                    INNER JOIN userprofile up
                        ON d.id_users = up.id_users

                    INNER JOIN incripcion_curso ic
                        ON ic.id_docente_curso = dc.iddocente_curso

                    INNER JOIN matricula m
                        ON ic.idmatricula = m.idmatricula
                        AND m.id_turno = ?
                        AND m.idtipo_matricula = ?
                        AND m.ciclo_matricula = ?

                    INNER JOIN malla_curricular mc
                        ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
                        AND mc.idmalla_curricular = ?

                    WHERE pe.malla_curricular_idmalla_curricular = ?
                    ORDER BY pe.idplan_de_estudio ASC;', [
                        $this->semesAca,
                        $this->selectTurno,
                        $selectTipo,
                        $this->selectCiclo,
                        $this->idmalla,
                        $this->idmalla,
            ]);



            //trae la lista de cursos por ciclos y a que docente esta asignado cada curso
            // $this->cursoos = DB::connection('mysql_segunda')->select('SELECT
            //         pe.idplan_de_estudio,
            //         c.idcursos,
            //         c.nombre_curso,
            //         nom_doce.dc_iddoce,
            //         nom_doce.nombre,
            //         iddocentecurso
            //     FROM
            //         plan_de_estudio AS pe
            //     LEFT JOIN
            //         cursos AS c ON pe.idcursos = c.idcursos
            //     LEFT JOIN (
            //         SELECT docente_curso.iddocente_curso, curss.carrera_malla, curss.año_de_inicio, docente_curso.iddocente_curso as iddocentecurso, docente_curso.idsemestre_academico, curss.idciclos, docente_curso.id_docente as dc_iddoce, docente_curso.idcursos as idcur, curss.idmalla_curricular, userprofile.nombre
            //             FROM docente_curso
            //             LEFT JOIN
            //                 (SELECT malla_curricular.idmalla_curricular, malla_curricular.nombre_malla_curricular, malla_curricular.carrera_malla, malla_curricular.año_de_inicio, plan_de_estudio.idcursos, plan_de_estudio.idciclos, cursos.idcursos as id_curs , cursos.nombre_curso
            //                     FROM plan_de_estudio
            //                     INNER JOIN malla_curricular ON plan_de_estudio.malla_curricular_idmalla_curricular = malla_curricular.idmalla_curricular
            //                     INNER JOIN cursos ON plan_de_estudio.idcursos = cursos.idcursos) as curss ON docente_curso.idcursos = curss.id_curs
            //                 LEFT JOIN docente ON docente_curso.id_docente = docente.iddocente
            //                 LEFT JOIN userprofile ON docente.id_users = userprofile.id_users
            //                 WHERE curss.carrera_malla = ? AND curss.idmalla_curricular = ? AND docente_curso.idsemestre_academico = ? AND curss.idciclos = ? AND docente_curso.	tipodocente_curso = ?) AS nom_doce ON c.idcursos = nom_doce.idcur
            //     WHERE
            //         pe.malla_curricular_idmalla_curricular = ? AND pe.idciclos = ?', [
            //     $this->selectCarrera,
            //     $this->idmalla,
            //     $this->semesAca,
            //     $this->selectCiclo,
            //     $selectTipo,
            //     $this->idmalla,
            //     $this->selectCiclo,
            // ]);

            foreach ($this->cursoos as $curso) {
                if (!is_null($curso->dc_iddoce)) {
                    $this->asignaciones[$curso->idcursos] = $curso->dc_iddoce;
                }
            }

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

            $this->actualizar = !$this->actualizar;
            $this->dispatch('cursos-actualizados');
        }
    }


    public function traerDocentes($selectCiclo)
    {

        $this->selectDocente = null;
        $this->docentes = DB::connection('mysql_segunda')->table('docente')
            ->join('userProfile', 'docente.id_users', '=', 'userProfile.id_users')->select('docente.iddocente', 'userProfile.nombre')->get();
    }

    #[\Livewire\Attributes\On('iddocente_curso')]
    public function iddocente_curso($cursoId, $tipo)
    {
        // $query = DB::connection('mysql_segunda')->table('docente_curso')
        //     ->select('iddocente_curso')
        //     ->where('idcursos', $cursoId)
        //     ->where('idsemestre_academico', $this->semesAca)
        //     ->first();

        $url = route('pdf.pdfActaEvalu', [$cursoId, $tipo]);
        $this->dispatch('abrirPdf', url: $url);
    }

    public function render()
    {
        return view('livewire.Admin.reporte-acta-evaluacion');
    }
}
