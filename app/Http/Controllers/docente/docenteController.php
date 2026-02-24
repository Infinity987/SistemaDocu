<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class docenteController extends Controller
{
    public function index()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('docente.index', compact('nom_usu'));
    }

    public function horario()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('docente.horario.index', compact('nom_usu'));
    }

    public function listarHorario()
    {
        $listCursos = DB::connection('mysql_segunda')->select(
            "SELECT dc.tipodocente_curso, ma.idmalla_curricular, sec.nom_seccion,
                    ma.año_de_inicio, doc.id_users, plae.malla_curricular_idmalla_curricular, plae.idcursos, plae.idciclos,
                    carre.nombre_de_carrera, cur.nombre_curso, cic.nombre_ciclo, dc.iddocente_curso, dc.id_docente, semaca.año,
                    semaca.periodo, semaca.estado, t.nombre_turno, a.aula_nombre, a.codigo_aula
                    FROM plan_de_estudio AS plae
                    INNER JOIN malla_curricular AS ma ON plae.malla_curricular_idmalla_curricular = ma.idmalla_curricular
                    INNER JOIN gamnielb_admision.carreras as carre ON ma.carrera_malla = carre.idcarreras
                    INNER JOIN cursos AS cur ON plae.idcursos = cur.idcursos
                    INNER JOIN ciclos as cic ON plae.idciclos = cic.idciclos
                    INNER JOIN docente_curso AS dc ON dc.idcursos = cur.idcursos
                    INNER JOIN docente as doc ON dc.id_docente = doc.iddocente
                    INNER JOIN semestre_academico AS semaca ON dc.idsemestre_academico = semaca.idsemestre_academico
                    LEFT JOIN horario AS h ON dc.iddocente_curso = h.id_docente_curso
                    LEFT JOIN hora AS hra ON h.idhora = hra.idhora
                    LEFT JOIN turno AS t ON hra.idturno = t.idturno
                    LEFT JOIN aula AS a ON h.idaula = a.idaula
                    LEFT JOIN seccion as sec ON h.idseccion = sec.idseccion
                    WHERE doc.id_users = ? AND semaca.estado = ?
                    GROUP BY cur.idcursos, doc.id_users, plae.malla_curricular_idmalla_curricular, plae.idcursos, plae.idciclos,
                    carre.nombre_de_carrera, cur.nombre_curso, cic.nombre_ciclo, dc.iddocente_curso, dc.id_docente, semaca.año,
                    semaca.periodo, semaca.estado, t.nombre_turno, a.aula_nombre, a.codigo_aula, ma.año_de_inicio, sec.nom_seccion,
                    ma.idmalla_curricular, dc.tipodocente_curso ORDER BY dc.iddocente_curso DESC;",
            [Auth::user()->id, 1]
        );

        $data = [];

        foreach ($listCursos as $listCurso) {
            $data[] = [
                'año' => $listCurso->año,
                'periodo' => $listCurso->periodo,
                'nombre_de_carrera' => $listCurso->nombre_de_carrera,
                'año_de_inicio' => $listCurso->año_de_inicio,
                'nombre_ciclo' => $listCurso->nombre_ciclo,
                'nombre_curso' => $listCurso->nombre_curso,
                'tipodocente_curso' => $listCurso->tipodocente_curso,
                'nombre_turno' => $listCurso->nombre_turno,
                'codigo_aula' => $listCurso->codigo_aula,
                'aula_nombre' => $listCurso->aula_nombre,
                'nom_seccion' => $listCurso->nom_seccion,
                'acciones' => view('docente.horario.btnHorario', [
                    'idmalla_curricular' => $listCurso->idmalla_curricular,
                    'año_de_inicio' => $listCurso->año_de_inicio,
                    'nom_seccion' => $listCurso->nom_seccion,
                    'iddocente_curso' => $listCurso->iddocente_curso,
                    'estado' => $listCurso->estado,
                    'idciclos' => $listCurso->idciclos,
                    'nombre_de_carrera' => $listCurso->nombre_de_carrera,
                    'nombre_ciclo' => $listCurso->nombre_ciclo,
                    'nombre_turno' => $listCurso->nombre_turno,
                    'codigo_aula' => $listCurso->codigo_aula,
                    'aula_nombre' => $listCurso->aula_nombre,
                    'año' => $listCurso->año,
                    'periodo' => $listCurso->periodo,
                    'tipodocente_curso' => $listCurso->tipodocente_curso,
                ])->render()
            ];
        }

        return response()->json([
            'data' => $data,
        ]);
    }

    public function verHorario(Request $request)
    {
        $año = $request->año;
        $periodo = $request->periodo;
        $nombre_de_carrera = $request->nombre_de_carrera;
        $nombre_ciclo = $request->nombre_ciclo;
        $nombre_turno = $request->nombre_turno;
        $codigo_aula = $request->codigo_aula;
        $aula_nombre = $request->aula_nombre;

        $año_de_inicio = $request->año_de_inicio;
        $nom_seccion = $request->nom_seccion;
        $idmalla_curricular = $request->idmalla_curricular;

        $tipodocente_curso = $request->tipodocente_curso;

        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $query = DB::connection('mysql_segunda')->select('
            SELECT dc.tipodocente_curso, ma.idmalla_curricular, doc.id_users, plae.malla_curricular_idmalla_curricular, plae.idcursos, plae.idciclos,
            carre.nombre_de_carrera, cur.nombre_curso, cic.nombre_ciclo, dc.iddocente_curso, dc.id_docente, semaca.año,
            semaca.periodo, semaca.estado
            FROM plan_de_estudio AS plae
            INNER JOIN malla_curricular AS ma ON plae.malla_curricular_idmalla_curricular = ma.idmalla_curricular
            INNER JOIN gamnielb_admision.carreras as carre ON ma.carrera_malla = carre.idcarreras
            INNER JOIN cursos AS cur ON plae.idcursos = cur.idcursos
            INNER JOIN ciclos as cic ON plae.idciclos = cic.idciclos
            INNER JOIN docente_curso AS dc ON dc.idcursos = cur.idcursos
            INNER JOIN docente as doc ON dc.id_docente = doc.iddocente
            INNER JOIN semestre_academico AS semaca ON dc.idsemestre_academico = semaca.idsemestre_academico
            WHERE doc.id_users = ? AND semaca.estado = ? AND plae.idciclos = ? AND ma.idmalla_curricular = ? AND dc.tipodocente_curso = ?', [Auth::user()->id, 1, $request->idciclos, $idmalla_curricular, $tipodocente_curso]);

        $iddocenteCurso = [];

        foreach ($query as $que) {
            $iddocenteCurso[] = $que->iddocente_curso;
        }

        $vall = implode(',', array_fill(0, count($iddocenteCurso), '?'));

        //deveulve los horarios por horas basado en el id_docente_cursoooo
        $quer = "SELECT ho.idHorario, ho.id_docente_curso, ho.idhora, ho.iddias, ho.idaula, ho.idseccion, cur.idcursos, doce.iddocente, cur.nombre_curso, ha.idturno FROM horario AS ho
            RIGHT JOIN docente_curso AS dc ON ho.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS cur ON dc.idcursos = cur.idcursos
            INNER JOIN docente AS doce ON dc.id_docente = doce.iddocente
            INNER JOIN semestre_academico AS sa ON dc.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN hora AS ha ON ho.idhora = ha.idhora
            INNER JOIN aula AS au ON ho.idaula = au.idaula
            WHERE doce.id_users = ? AND sa.estado = ? AND id_docente_curso IN ($vall)";

        $bindings = array_merge([Auth::user()->id, 1], $iddocenteCurso);

        $queryHorari = DB::connection('mysql_segunda')->select($quer, $bindings);

        //////////////////////////////
        //para saber si es mñn o tarde
        $contMaña = 0;
        $contTard = 0;
        foreach ($queryHorari as $tur) {
            if ($tur->idturno === 1) {
                $contMaña++;
            } elseif ($tur->idturno === 2) {
                $contTard++;
            }
        }

        $dias = DB::connection('mysql_segunda')->table('dias')->get();
        $horas = DB::connection('mysql_segunda')->table('hora')->get();
        return view('docente.horario.verHorario', compact('nom_usu', 'queryHorari', 'dias', 'horas', 'contMaña', 'contTard', 'año', 'periodo', 'nombre_de_carrera', 'nombre_ciclo', 'nombre_turno', 'codigo_aula', 'aula_nombre', 'año_de_inicio', 'nom_seccion'));
    }
}
