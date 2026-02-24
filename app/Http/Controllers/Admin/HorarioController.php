<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

class HorarioController extends Controller
{
    public function index()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('admin.horario.index', compact('nom_usu'));
    }

    public function agreindex(Request $request, $estado = null)
    {
        // dd($request);
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $activaHorario = $request->activaHorario;
        $editar = $request->editar;

        $selectCarrera = $request->selectCarrera;
        $selectAnioMallaCu = $request->selectAnioMallaCu; //
        $idmalla = $request->idmalla;
        $semestre_acad = $request->semestre_acad;
        $selectCiclo = $request->selectCiclo;

        $nomAño = $request->nomAño;
        $nomCarrera = $request->nomCarrera;
        $nomSemestre = $request->nomSemestre;

        $nombreTipoDocenCurso = $request->nombreTipoDocenCurso;
        $tipodocente_curso = $request->tipodocente_curso;
        $tipoReguSubsa = $request->tipoo; //tipo semes aca si es regular o subsanacion

        $horasTurno = '';
        $mensaje = '';
        $asignacionesMap = '';

        //cursos
        $query = DB::connection('mysql_segunda')->select('SELECT
                    pe.idplan_de_estudio,
                    c.idcursos,
                    c.nombre_curso,
                    c.horas,
                    nom_doce.dc_iddoce,
                    nom_doce.nombre,
                    dc_iddocen_curso,
                    tipodocente_curso
                FROM
                    plan_de_estudio AS pe
                LEFT JOIN
                    cursos AS c ON pe.idcursos = c.idcursos
                LEFT JOIN (
                    SELECT docente_curso.iddocente_curso as dc_iddocen_curso, curss.carrera_malla, curss.año_de_inicio, docente_curso.idsemestre_academico, curss.idciclos, docente_curso.id_docente as dc_iddoce, docente_curso.idcursos as idcur, curss.idmalla_curricular, userprofile.nombre, docente_curso.tipodocente_curso as tipodocente_curso
                        FROM docente_curso
                        LEFT JOIN
                            (SELECT malla_curricular.idmalla_curricular, malla_curricular.nombre_malla_curricular, malla_curricular.carrera_malla, malla_curricular.año_de_inicio, plan_de_estudio.idcursos, plan_de_estudio.idciclos, cursos.idcursos as id_curs , cursos.nombre_curso
                                FROM plan_de_estudio
                                INNER JOIN malla_curricular ON plan_de_estudio.malla_curricular_idmalla_curricular = malla_curricular.idmalla_curricular
                                INNER JOIN cursos ON plan_de_estudio.idcursos = cursos.idcursos) as curss ON docente_curso.idcursos = curss.id_curs
                            LEFT JOIN docente ON docente_curso.id_docente = docente.iddocente
                            LEFT JOIN userprofile ON docente.id_users = userprofile.id_users
                            WHERE curss.carrera_malla = ? AND curss.idmalla_curricular = ? AND docente_curso.idsemestre_academico = ? AND curss.idciclos = ? AND docente_curso.tipodocente_curso = ?) AS nom_doce ON c.idcursos = nom_doce.idcur
                WHERE
                    pe.malla_curricular_idmalla_curricular = ? AND pe.idciclos = ?', [
            $request->selectCarrera,
            $request->idmalla,
            $request->semestre_acad,
            $request->selectCiclo,
            $tipoReguSubsa,
            $request->idmalla,
            $request->selectCiclo,
        ]);

        //tipo regular, subsanacion
        $tipoSemestreAcade = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $tipoReguSubsa)->value('tipo_ciclo');
        if ($tipoSemestreAcade == 3) {
            $listReguSupsa = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->where('idtipo_matricula', 2)->limit(1)->get();
        } else {
            $listReguSupsa = DB::connection('mysql_segunda')->table('tipo_matricula')->select('idtipo_matricula', 'nombre_tipo_matricula')->orderBy('idtipo_matricula', 'asc')->limit(2)->get();
        }


        $ciclos = DB::connection('mysql_segunda')->table('ciclos')->select('idciclos', 'nombre_ciclo')->get();

        $tipo_semesParOImpar = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $semestre_acad)->select('tipo_ciclo')->first();
        if ($tipo_semesParOImpar->tipo_ciclo == 1) {
            if ($selectCiclo % 2 == 0) {
                $nombreTipoDocenCurso = 'Cursos de subsanación pares';
                $tipodocente_curso = 2;
            } else {
                $tipodocente_curso = 1;
            }
        } elseif ($tipo_semesParOImpar->tipo_ciclo == 2) {
            if ($selectCiclo % 2 != 0) {
                $nombreTipoDocenCurso = 'Cursos de subsanación impares';
                $tipodocente_curso = 2;
            } else {
                $tipodocente_curso = 1;
            }
        } elseif ($tipo_semesParOImpar->tipo_ciclo == 3) {
            $nombreTipoDocenCurso = 'Cursos de subsanación verano';
            $tipodocente_curso = 2;
        }

        $turnos = DB::connection('mysql_segunda')->table('turno')->select('idturno', 'nombre_turno')->get();
        $aulas = DB::connection('mysql_segunda')->table('aula')->select('idaula', 'aula_nombre', 'codigo_aula')->get();
        $dias = DB::connection('mysql_segunda')->table('dias')->select('iddias', 'nom_dia')->get();
        $secciones = DB::connection('mysql_segunda')->table('seccion')->get();

        if (!is_null($activaHorario)) {
            // dd($request);
            $activaHorario = 1;
            $turn = $request->turno;
            $aul = $request->aula;
            $tipo = $request->tipoo;

            $horasTurno = DB::connection('mysql_segunda')
                ->table('hora')
                ->select('idhora', 'nom_hora')
                ->join('turno', 'hora.idturno', '=', 'turno.idturno')
                ->where('hora.idturno', '=', $turn)
                ->get();

            //para traer los registros asiganados
            $asigExistentes = DB::connection('mysql_segunda')->select('SELECT hdc.idHorario, hdc.id_docente_curso, hdc.idhora, hdc.iddias, hdc.idaula, hdc.tipodocente_curso, hdc.idturno
                    FROM (
                        SELECT plan_de_estudio.malla_curricular_idmalla_curricular, plan_de_estudio.idcursos as idcur, plan_de_estudio.idciclos, cursos.idcursos, cursos.nombre_curso
                        FROM cursos
                        INNER JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos) as pc
                        INNER JOIN (
                            SELECT horario.idHorario, horario.id_docente_curso,horario.idhora, horario.iddias, horario.idaula, docente_curso.iddocente_curso, docente_curso.idcursos, docente_curso.id_docente, docente_curso.idsemestre_academico, docente_curso.tipodocente_curso, turno.idturno
                            FROM horario
                            INNER JOIN docente_curso ON horario.id_docente_curso = docente_curso.iddocente_curso
                        	LEFT JOIN hora ON horario.idhora = hora.idhora
                            LEFT JOIN turno ON hora.idturno = turno.idturno
                        ) as hdc
                                ON pc.idcur = hdc.idcursos
                    WHERE pc.malla_curricular_idmalla_curricular = ? AND hdc.idsemestre_academico = ? AND pc.idciclos = ? AND hdc.tipodocente_curso = ? AND hdc.idturno = ? AND hdc.idaula = ?;', [
                $request->idmalla,
                $request->semestre_acad,
                $request->selectCiclo,
                $tipoReguSubsa,
                $turn,
                $aul,
            ]);
            // dump($tipoReguSubsa);
            // dd($asigExistentes);

            $asignacionesMap = [];

            foreach ($asigExistentes as $fila) {
                // dump($asigExistentes);
                $clave = $fila->idhora . '-' . $fila->iddias;

                $asignacionesMap[$clave] = [
                    'id_docente_curso' => $fila->id_docente_curso,
                    'idHorario' => $fila->idHorario,
                    'idhora' => $fila->idhora,
                    'iddias' => $fila->iddias,
                    'idaula' => $fila->idaula,
                ];
            }
        } else {
            $activaHorario = 0;
            $turn = 0;
            $aul = 0;
        }

        //para las secciones
        $secc = 0;
        $vermalla = DB::connection('mysql_segunda')->table('malla_curricular')->where('carrera_malla', $selectCarrera)->orderBy('año_de_inicio', 'desc')->pluck('año_de_inicio');
        foreach ($vermalla as $index => $malla) {
            if ($malla == $nomAño) {
                $secc = $index;
            }
        }

        $secc++;

        if ($estado === 'ok') {
            $mensaje = ['tipo' => 'success', 'texto' => 'Guardado con éxito'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else if ($estado === 'vacio') {
            $mensaje = ['tipo' => 'danger', 'texto' => 'No asigno cursos debe asignar cursos ...'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else if ($estado === 'Alguns_cursos_no_asig') {
            $mensaje = ['tipo' => 'info', 'texto' => 'Algunos cursos require que se asigne un docente, por eso no fueron registrados en el horario ...'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else if ($estado === 'turno y aula') {
            $mensaje = ['tipo' => 'success', 'texto' => 'Cambiado TURNO y AULA, y/o asignados algunos cursos'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else if ($estado === 'aula') {
            $mensaje = ['tipo' => 'success', 'texto' => 'Cambiado AULA, y/o asignados algunos cursos'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else if ($estado === 'turno') {
            $mensaje = ['tipo' => 'success', 'texto' => 'Cambiado TURNO, y/o asignados algunos cursos'];
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        } else {
            return view('admin.horario.agreindex', compact('query', 'selectCarrera', 'selectAnioMallaCu', 'idmalla', 'semestre_acad', 'selectCiclo', 'nomCarrera', 'nomSemestre', 'ciclos', 'turnos', 'aulas', 'dias', 'activaHorario', 'turn', 'aul', 'nombreTipoDocenCurso', 'tipodocente_curso', 'horasTurno', 'mensaje', 'asignacionesMap', 'editar', 'nom_usu', 'nomAño', 'secciones', 'secc', 'listReguSupsa', 'tipoReguSubsa'));
        }
    }

    public function guardarHorario(Request $request)
    {

        $aula = $request->aulaa;
        $siesnull = empty(array_filter($request->asignacion, fn($v) => !is_null($v))); //false si hay datos y true si no hay datos
        $contCursosSinIddocencurso = 0;

        if (!$siesnull) {

            // dump('aquidiedieidmmmmmmmmmmmm');
            // dd($request);
            foreach ($request->asignacion as $item) {
                if ($item != 0) {
                    $partes = explode('-', $item);
                    $valor1 = $partes[0]; //idhora
                    $valor2 = $partes[1]; //iddias
                    $valor3 = $partes[2]; //idcurso_docente

                    if ($valor3 === '') {
                        $contCursosSinIddocencurso++;
                    } else {
                        $existe = DB::connection('mysql_segunda')->table('horario')
                            ->where('id_docente_curso', $valor3)
                            ->where('idhora', $valor1)
                            ->where('iddias', $valor2)
                            ->where('idaula', $aula)
                            ->exists();

                        if (!$existe) {
                            $insert = DB::connection('mysql_segunda')->table('horario')->insert([
                                'id_docente_curso' => $valor3,
                                'idhora' => $valor1,
                                'iddias' => $valor2,
                                'idaula' => $aula,
                                'idseccion' => $request->secc,
                            ]);
                        }
                    }
                }
            }
            if ($contCursosSinIddocencurso != 0) {
                $estado = 'Alguns_cursos_no_asig';
            } else {
                $estado = 'ok';
            }
        } else {
            $estado = 'vacio';
        }

        $arrayHora = [];

        // para traer los registros asiganados
        $asigExistentes = DB::connection('mysql_segunda')->select('SELECT hdc.idHorario, hdc.id_docente_curso, hdc.idhora, hdc.iddias, hdc.idaula, hdc.tipodocente_curso, hdc.idturno
                    FROM (
                        SELECT plan_de_estudio.malla_curricular_idmalla_curricular, plan_de_estudio.idcursos as idcur, plan_de_estudio.idciclos, cursos.idcursos, cursos.nombre_curso
                        FROM cursos
                        INNER JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos) as pc
                        INNER JOIN (
                            SELECT horario.idHorario, horario.id_docente_curso,horario.idhora, horario.iddias, horario.idaula, docente_curso.iddocente_curso, docente_curso.idcursos, docente_curso.id_docente, docente_curso.idsemestre_academico, docente_curso.tipodocente_curso, turno.idturno
                            FROM horario
                            INNER JOIN docente_curso ON horario.id_docente_curso = docente_curso.iddocente_curso
                        	LEFT JOIN hora ON horario.idhora = hora.idhora
                            LEFT JOIN turno ON hora.idturno = turno.idturno
                        ) as hdc
                                ON pc.idcur = hdc.idcursos
                    WHERE pc.malla_curricular_idmalla_curricular = ? AND hdc.idsemestre_academico = ? AND pc.idciclos = ? AND hdc.tipodocente_curso = ? AND hdc.idturno = ? AND hdc.idaula = ?;', [
            $request->idmalla,
            $request->semestre_acad,
            $request->selectCiclo,
            $request->tipoo,
            $request->turnoPrime,
            $request->aulaPrime,
        ]);



        if (!empty($asigExistentes)) {
            // dd($asigExistentes);
            if (($request->turnoPrime != $request->turnoo) && ($request->aulaPrime != $request->aulaa)) {
                // para actualizar turno y aula / tarde a dia / 2 a 1 /
                if ($request->turnoPrime == 2) {
                    foreach ($asigExistentes as $asigHora) {
                        $arrayHora[] = $asigHora->idhora - 9;
                    }

                    foreach ($asigExistentes as $index => $actuHorario) {
                        $updateHorario = DB::connection('mysql_segunda')->table('horario')
                            ->where('idHorario', $actuHorario->idHorario)
                            ->update([
                                'idhora' => $arrayHora[$index],
                                'idaula' => $request->aulaa,
                            ]);
                    }

                    // para actualizar/ dia a tarde / 1 a 2 /
                } else if ($request->turnoPrime == 1) {
                    foreach ($asigExistentes as $asigHora) {
                        $arrayHora[] = $asigHora->idhora + 9;
                    }
                    // dd($arrayHora);

                    foreach ($asigExistentes as $index => $actuHorario) {
                        $updateHorario = DB::connection('mysql_segunda')->table('horario')
                            ->where('idHorario', $actuHorario->idHorario)
                            ->update([
                                'idhora' => $arrayHora[$index],
                                'idaula' => $request->aulaa,
                            ]);
                    }
                }

                $estado = 'turno y aula';
            } else if (($request->turnoPrime == $request->turnoo) && ($request->aulaPrime != $request->aulaa)) {
                //actualiza solo aula
                foreach ($asigExistentes as $actuAula) {
                    $updateAula = DB::connection('mysql_segunda')->table('horario')
                        ->where('idHorario', $actuAula->idHorario)
                        ->update([
                            'idaula' => $request->aulaa,
                        ]);
                }
                $estado = 'aula';
            } else if (($request->turnoPrime != $request->turnoo) && ($request->aulaPrime == $request->aulaa)) {
                // para actualizar turno / tarde a dia / 2 a 1 /
                if ($request->turnoPrime == 2) {
                    foreach ($asigExistentes as $asigHora) {
                        $arrayHora[] = $asigHora->idhora - 9;
                    }

                    foreach ($asigExistentes as $index => $actuHorario) {
                        $updateHorario = DB::connection('mysql_segunda')->table('horario')
                            ->where('idHorario', $actuHorario->idHorario)
                            ->update([
                                'idhora' => $arrayHora[$index],
                            ]);
                    }

                    // para actualizar/ dia a tarde / 1 a 2 /
                } else if ($request->turnoPrime == 1) {
                    foreach ($asigExistentes as $asigHora) {
                        $arrayHora[] = $asigHora->idhora + 9;
                    }
                    // dd($arrayHora);

                    foreach ($asigExistentes as $index => $actuHorario) {
                        $updateHorario = DB::connection('mysql_segunda')->table('horario')
                            ->where('idHorario', $actuHorario->idHorario)
                            ->update([
                                'idhora' => $arrayHora[$index],
                            ]);
                    }
                }
                $estado = 'turno';
            }
        } else {
            $estado = 'vacio';
        }

        $retorno = $this->agreindex($request, $estado);
        return $retorno;
    }

    public function eliminarPorId(Request $request)
    {
        DB::connection('mysql_segunda')
            ->table('horario')
            ->where('idHorario', $request->idHorario)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function listar()
    {
        $query = DB::connection('mysql_segunda')->select('select
                                sa.año,
                                sa.periodo,
                                mc.año_de_inicio,
                                carre.nombre_de_carrera as nomcarreraa,
                                ci.nombre_ciclo,
                                au.aula_nombre,
                                au.idaula,
                                au.codigo_aula,
                                turno.nombre_turno,
                                carre.idcarreras as selec_carre,
                                mc.idmalla_curricular as id_malla,
                                dc.idsemestre_academico,
                                ci.idciclos,
                                h.idHorario,
                                h.id_docente_curso,
                                h.idhora,
                                h.iddias,
                                dc.tipodocente_curso,
                                turno.idturno,
                                seccion.nom_seccion,
                                seccion.idseccion
                            from
                                horario h
                            inner join docente_curso dc on
                                h.id_docente_curso = dc.iddocente_curso
                            inner join semestre_academico sa on
                                dc.idsemestre_academico = sa.idsemestre_academico
                            inner join cursos c on
                                dc.idcursos = c.idcursos
                            inner join plan_de_estudio pde on
                                pde.idcursos = c.idcursos
                            inner join ciclos ci on
                                pde.idciclos = ci.idciclos
                            inner join malla_curricular mc on
                                pde.malla_curricular_idmalla_curricular = mc.idmalla_curricular
                            inner join docente d on
                                dc.id_docente = d.iddocente
                            inner join userprofile u on
                                u.id_users = d.id_users
                            inner join gamnielb_admision.carreras carre on
                                mc.carrera_malla = carre.idcarreras
                            inner join aula au on
                                h.idaula = au.idaula
                            inner join hora on
                                h.idhora = hora.idhora
                            inner join turno on
                                hora.idturno = turno.idturno
                            inner join seccion on
                                h.idseccion = seccion.idseccion
                            where
                                sa.estado = 1

                            order by
                                mc.idmalla_curricular, ci.idciclos ASC;');
        $registros = collect($query)->unique(function ($fila) {
            return $fila->año . '-' .
                $fila->periodo . '-' .
                $fila->año_de_inicio . '-' .
                $fila->nomcarreraa . '-' .
                $fila->nombre_ciclo . '-' .
                $fila->codigo_aula . '-' .
                $fila->nombre_turno . '-' .
                $fila->nom_seccion . '-' .
                $fila->tipodocente_curso;
        });

        return DataTables::of($registros)
            ->addColumn('acciones', function ($row) {
                return view('admin.horario.prueba', [
                    'selectCarrera' => $row->selec_carre,
                    'selectAnioMallaCu' => $row->id_malla,
                    'idmalla' => $row->id_malla,
                    'semestre_acad' => $row->idsemestre_academico,
                    'selectCiclo' => $row->idciclos,
                    'nomCarrera' => $row->nomcarreraa,
                    'nomSemestre' => $row->año . ' - ' . $row->periodo,
                    'nombreTipoDocenCurso' => $row->tipodocente_curso,
                    'tipodocente_curso' => $row->tipodocente_curso,
                    'nomAño' => $row->año_de_inicio,
                    'nomciclo' => $row->nombre_ciclo,
                    'activaHorario' => 1,
                    'turno' => $row->idturno,
                    'aula' => $row->idaula,
                    'idseccion' => $row->idseccion,
                    'tipoo' => $row->tipodocente_curso,
                ])->render();
            })
            ->rawColumns(['acciones']) // importante para que se renderice HTML
            ->make(true);
    }

    // public function listar(Request $request)
    // {
    //     // dd($request);
    //     $draw = intval($request->input('draw'));
    //     $start = intval($request->input('start'));
    //     $length = intval($request->input('length'));
    //     $search = $request->input('search.value', '');

    //     $listar = 'SELECT MAX(hdc.año) as año,
    //                 MAX(hdc.periodo) as periodo,
    //                 MAX(pc.año_de_inicio) as año_de_inicio,
    //                 MAX(pc.nomcarreraa) as nomcarreraa,
    //                 MAX(pc.nombre_ciclo) as nombre_ciclo,
    //                 MAX(hdc.aula_nombre) as aula_nombre,
    //                 max(hdc.idaula) as idaula,
    //                 MAX(hdc.codigo_aula) as codigo_aula,
    //                 MAX(hdc.nombre_turno) as nombre_turno,
    //                 MAX(pc.selectCarrera) as selec_carre,
    //                 MAX(pc.malla_curricular_idmalla_curricular) as id_malla,
    //                 max(hdc.idsemestre_academico) as idsemestre_academico,
    //                 max(pc.idciclos) as idciclos,
    //                 MAX(hdc.idHorario) as idHorario,
    //                 MAX(hdc.id_docente_curso) as id_docente_curso,
    //                 MAX(hdc.idhora) as idhora,
    //                 MAX(hdc.iddias) as iddias,
    //                 max(hdc.tipodocente_curso) as tipodocente_curso,
    //                 max(hdc.idturno) as idturno,
    //                 MAX(hdc.nom_seccion) as nom_seccion,
    //                 MAX(hdc.idseccion) as idseccion
    //                 FROM (
    //                     SELECT gamnielb_admision.carreras.idcarreras as selectCarrera, gamnielb_admision.carreras.nombre_de_carrera as nomcarreraa, ciclos.nombre_ciclo, plan_de_estudio.malla_curricular_idmalla_curricular, plan_de_estudio.idcursos as idcur, plan_de_estudio.idciclos, cursos.idcursos, cursos.nombre_curso, malla_curricular.año_de_inicio
    //                     FROM cursos
    //                     INNER JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos
    //                     INNER JOIN malla_curricular ON plan_de_estudio.malla_curricular_idmalla_curricular = malla_curricular.idmalla_curricular
    //                     INNER JOIN gamnielb_admision.carreras ON malla_curricular.carrera_malla = gamnielb_admision.carreras.idcarreras
    //                     INNER JOIN ciclos ON plan_de_estudio.idciclos = ciclos.idciclos) as pc
    //                 INNER JOIN (
    //                     SELECT semestre_academico.año, semestre_academico.periodo, aula.aula_nombre, aula.codigo_aula, turno.nombre_turno, horario.idHorario, horario.id_docente_curso,horario.idhora, horario.iddias, horario.idaula, docente_curso.iddocente_curso, docente_curso.idcursos, docente_curso.id_docente, docente_curso.idsemestre_academico, docente_curso.tipodocente_curso, turno.idturno, seccion.nom_seccion, seccion.idseccion
    //                     FROM horario
    //                     INNER JOIN aula ON horario.idaula = aula.idaula
    //                     inner JOIN hora ON horario.idhora = hora.idhora
    //                     inner JOIN turno ON hora.idturno = turno.idturno
    //                     INNER JOIN docente_curso ON horario.id_docente_curso = docente_curso.iddocente_curso
    //                     inner JOIN semestre_academico ON docente_curso.idsemestre_academico = semestre_academico.idsemestre_academico
    //                     INNER JOIN seccion ON horario.idseccion = seccion.idseccion
    //                     WHERE semestre_academico.estado = 1
    //                 ) as hdc
    //                             ON pc.idcur = hdc.idcursos
    //                 GROUP BY idaula, idturno, idciclos, tipodocente_curso, idsemestre_academico  ORDER BY idHorario DESC';

    //     $bindings = [];
    //     if (!empty($search)) {
    //         $listarb = "SELECT COUNT(*) as total FROM ($listar) as sub WHERE sub.año LIKE ? OR sub.nomcarreraa LIKE ? OR sub.nombre_ciclo LIKE ? OR sub.codigo_aula LIKE ? OR sub.nom_seccion LIKE ? OR sub.nombre_turno LIKE ? OR sub.año_de_inicio LIKE ?";

    //         $bindings = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
    //         $totRow = DB::connection('mysql_segunda')
    //             ->selectOne($listarb, $bindings);
    //         $que = "SELECT * FROM ($listar) as sub WHERE sub.año LIKE ? OR sub.nomcarreraa LIKE ? OR sub.nombre_ciclo LIKE ? OR sub.codigo_aula LIKE ? OR sub.nom_seccion LIKE ? OR sub.nombre_turno LIKE ? OR sub.año_de_inicio LIKE ?";
    //     } else {
    //         $totRow = DB::connection('mysql_segunda')
    //             ->selectOne("SELECT COUNT(*) as total FROM ($listar) as sub", $bindings);
    //     }

    //     $totListar = $totRow->total ?? 0;

    //     if (!empty($search)) {
    //         $que .= " LIMIT $start, $length";
    //         $registros = DB::connection('mysql_segunda')->select($que, $bindings);
    //     } else {
    //         $listar .= " LIMIT $start, $length";
    //         $registros = DB::connection('mysql_segunda')->select($listar, $bindings);
    //     }
    //     // dd($registros);
    //     $data = [];

    //     foreach ($registros as $fila) {
    //         $data[] = [
    //             'año' => $fila->año,
    //             'periodo' => $fila->periodo,
    //             'año_de_inicio' => $fila->año_de_inicio,
    //             'nomcarreraa' => $fila->nomcarreraa,
    //             'nombre_ciclo' => $fila->nombre_ciclo,
    //             'nom_seccion' => $fila->nom_seccion,
    //             'codigo_aula' => $fila->codigo_aula,
    //             'aula_nombre' => $fila->aula_nombre,
    //             'nombre_turno' => $fila->nombre_turno,
    //             'acciones' => view('admin.horario.prueba', [
    //                 'selectCarrera' => $fila->selec_carre,
    //                 'selectAnioMallaCu' => $fila->id_malla,
    //                 'idmalla' => $fila->id_malla,
    //                 'semestre_acad' => $fila->idsemestre_academico,
    //                 'selectCiclo' => $fila->idciclos,
    //                 'nomCarrera' => $fila->nomcarreraa,
    //                 'nomSemestre' => $fila->año . ' - ' . $fila->periodo,
    //                 'nombreTipoDocenCurso' => $fila->tipodocente_curso,
    //                 'tipodocente_curso' => $fila->tipodocente_curso,
    //                 'nomAño' => $fila->año_de_inicio,
    //                 'nomciclo' => $fila->nombre_ciclo,
    //                 'activaHorario' => 1,
    //                 'turno' => $fila->idturno,
    //                 'aula' => $fila->idaula,
    //                 'idseccion' => $fila->idseccion,
    //                 'tipoo' => $fila->tipodocente_curso,
    //             ])->render()
    //         ];
    //     }
    //     // dd($list)

    //     return response()->json([
    //         'draw' => $draw,
    //         'recordsTotal' => $totListar,
    //         'recordsFiltered' => $totListar,
    //         'data' => $data
    //     ]);
    // }

    public function deleteHorario(Request $request)
    {

        $asigExistentes = DB::connection('mysql_segunda')->select('SELECT hdc.idHorario, hdc.id_docente_curso, hdc.idhora, hdc.iddias, hdc.idaula, hdc.tipodocente_curso, hdc.idturno, hdc.idseccion
                    FROM (
                        SELECT plan_de_estudio.malla_curricular_idmalla_curricular, plan_de_estudio.idcursos as idcur, plan_de_estudio.idciclos, cursos.idcursos, cursos.nombre_curso
                        FROM cursos
                        INNER JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos) as pc
                        INNER JOIN (
                            SELECT horario.idHorario, horario.id_docente_curso,horario.idhora, horario.iddias, horario.idaula, docente_curso.iddocente_curso, docente_curso.idcursos, docente_curso.id_docente, docente_curso.idsemestre_academico, docente_curso.tipodocente_curso, turno.idturno, seccion.idseccion
                            FROM horario
                            INNER JOIN docente_curso ON horario.id_docente_curso = docente_curso.iddocente_curso
                        	LEFT JOIN hora ON horario.idhora = hora.idhora
                            LEFT JOIN turno ON hora.idturno = turno.idturno
                            INNER JOIN seccion ON horario.idseccion = seccion.idseccion
                        ) as hdc
                                ON pc.idcur = hdc.idcursos
                    WHERE pc.malla_curricular_idmalla_curricular = ? AND hdc.idsemestre_academico = ? AND pc.idciclos = ? AND hdc.tipodocente_curso = ? AND hdc.idturno = ? AND hdc.idaula = ? AND hdc.idseccion = ?;', [
            $request->idmalla,
            $request->semestre_acad,
            $request->selectCiclo,
            $request->tipodocente_curso,
            $request->turno,
            $request->aula,
            $request->idseccion,
        ]);

        foreach ($asigExistentes as $asigExistente) {
            $delete = DB::connection('mysql_segunda')
                ->table('horario')
                ->where('idHorario', '=', $asigExistente->idHorario)
                ->delete();
        }

        return redirect()->route('horario.index')->with('success', 'horario eliminado correctamente');
        // dd($asigExistentes);
    }
}
