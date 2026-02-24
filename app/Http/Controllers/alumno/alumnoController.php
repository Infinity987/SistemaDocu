<?php

namespace App\Http\Controllers\alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class alumnoController extends Controller
{
    public function index()
    {
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();
        $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
        $nom_carre = $nom_usuC->nombre_de_carrera;
        return view('alumno.index', compact('nom_usu', 'nom_carre'));
    }
    //matri actual
    public function matriActual()
    {
        // ape, nom y carre del usuario;
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();

        $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
        $nom_carre = $nom_usuC->nombre_de_carrera;

        //matri actu
        $cursosactu = DB::connection('mysql_segunda')->select('SELECT ic.total, ic.idincripcion_curso, tm.nombre_tipo_matricula,
        em.nombre_estado, ic.idmatricula, ic.id_docente_curso, ic.credito, ic.idCalificaciones1, ic.recomendacion_nota1,
        ic.idCalificaciones2, ic.recomendacion_nota2, ic.idCalificaciones3, ic.recomendacion_nota3, ma.idsemestre_academico,
        ma.fecha_matricula, ma.ciclo_matricula, sa.año, sa.periodo, dc.tipodocente_curso, cur.nombre_curso, cur.credito,
        cur.idtipo_curso, ic.idcompetencia1, ic.idcompetencia2, ic.idcompetencia3
            FROM incripcion_curso AS ic
            INNER JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
            INNER JOIN tipo_matricula AS tm ON ma.idtipo_matricula = tm.idtipo_matricula
            INNER JOIN estado_matricula AS em ON ma.idestado_matricula = em.idestado_matricula
            INNER JOIN semestre_academico AS sa ON ma.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS cur ON dc.idcursos = cur.idcursos
            WHERE sa.estado = ? AND ma.id_alumno = ?', [1, Auth::user()->dni]);

        //califi
        $califi = DB::connection('mysql_segunda')->table('calificaciones')->get();

        return view('alumno.matriActual', compact('nom_usu', 'nom_carre', 'cursosactu', 'califi'));
    }

    //matri por curricula
    public function matriPorCurri()
    {
        // ape, nom y carre del usuario;
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera', 'p.id_malla')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();
        $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
        $nom_carre = $nom_usuC->nombre_de_carrera;
        $malla_postu = $nom_usuC->id_malla;

        //malla curricular
        $malla_notas = DB::connection('mysql_segunda')->select('SELECT cur.idcursos, ci.idciclos, ci.nombre_ciclo, cur.nombre_curso, cur.credito,
        tic.nombre_tipo_curso, ntc.total, ntc.estado_nota
                FROM plan_de_estudio AS pe
                INNER JOIN cursos AS cur ON pe.idcursos = cur.idcursos
                INNER JOIN tipo_curso AS tic ON cur.idtipo_curso = tic.idtipo_curso
                INNER JOIN ciclos AS ci ON pe.idciclos = ci.idciclos
                LEFT JOIN (
                    SELECT c.idcursos, MAX(ic.total) as total, max(ic.estado_nota) as estado_nota
                    FROM incripcion_curso AS ic
                    INNER JOIN matricula AS m ON ic.idmatricula = m.idmatricula
                    INNER JOIN gamnielb_admision.postulante AS p ON m.id_alumno = p.idpostulante
                    INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
                    INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
                    INNER JOIN tipo_curso AS tc ON c.idtipo_curso = tc.idtipo_curso
                    WHERE p.idpostulante = ? GROUP BY c.idcursos) AS ntc ON pe.idcursos = ntc.idcursos
                WHERE pe.malla_curricular_idmalla_curricular = ? ORDER BY cur.idcursos;', [Auth::user()->dni, $malla_postu]);

        return view('alumno.matriPorCurri', compact('nom_usu', 'nom_carre', 'malla_notas'));
    }

    public function matriReali()
    {
        // ape, nom y carre del usuario;
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();
        $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
        $nom_carre = $nom_usuC->nombre_de_carrera;

        //matriculas realizadas
        $matriRealiz = DB::connection('mysql_segunda')->select("SELECT
                CONCAT(sa.año, ' ', sa.periodo) AS semestre_academico,
                    sa.idsemestre_academico,
                    m.idmalla AS malla_curricular_idmalla_curricular,
                    c.idcursos,
                    c.nombre_curso,
                    ic.total AS nota_final,
                    m.id_turno,
                    pde.idciclos,
                    ci.nombre_ciclo,
                    c.credito
                FROM matricula m
                INNER JOIN semestre_academico sa
                    ON m.idsemestre_academico = sa.idsemestre_academico
                INNER JOIN incripcion_curso ic
                    ON m.idmatricula = ic.idmatricula
                INNER JOIN docente_curso dc
                    ON ic.id_docente_curso = dc.iddocente_curso
                INNER JOIN cursos c
                    ON dc.idcursos = c.idcursos
                INNER JOIN plan_de_estudio pde
                    ON pde.idcursos = c.idcursos
                    AND pde.malla_curricular_idmalla_curricular = m.idmalla
                INNER JOIN ciclos ci
                    ON pde.idciclos = ci.idciclos
                WHERE m.id_alumno = ?
                ORDER BY sa.año DESC, sa.periodo DESC, pde.idciclos, c.idcursos;", [Auth::user()->dni]);
        return view('alumno.matriReali', compact('nom_usu', 'nom_carre', 'matriRealiz'));
    }

    public function horarioAlumno()
    {
        //datos estudiante
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();
        $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
        $nom_carre = $nom_usuC->nombre_de_carrera;

        //datos regular
        $datosR = DB::connection('mysql_segunda')->select('SELECT m.idtipo_matricula, m.total_credito, mc.nombre_malla_curricular, ci.       nombre_ciclo FROM matricula m
            INNER JOIN malla_curricular mc ON m.idmalla = mc.idmalla_curricular
            INNER JOIN ciclos ci ON m.ciclo_matricula = ci.idciclos
            INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
            WHERE m.id_alumno = ? AND sa.estado = ? AND m.idtipo_matricula = ?;
        ', [Auth::user()->dni, 1, 1]);

        //datos subsana
        $datosS = DB::connection('mysql_segunda')->select('SELECT m.idtipo_matricula, m.total_credito, mc.nombre_malla_curricular, ci.       nombre_ciclo FROM matricula m
            INNER JOIN malla_curricular mc ON m.idmalla = mc.idmalla_curricular
            INNER JOIN ciclos ci ON m.ciclo_matricula = ci.idciclos
            INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
            WHERE m.id_alumno = ? AND sa.estado = ? AND m.idtipo_matricula = ?;
        ', [Auth::user()->dni, 1, 2]);

        //cursos regu
        $queryRegu = DB::connection('mysql_segunda')->select('SELECT up.nombre, c.nombre_curso, c.credito, c.horas FROM incripcion_curso ic
            INNER JOIN matricula m ON ic.idmatricula = m.idmatricula
            INNER JOIN docente_curso dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN docente d ON dc.id_docente = d.iddocente
            INNER JOIN userprofile up ON d.id_users = up.id_users
            INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN cursos c ON dc.idcursos = c.idcursos
            WHERE m.id_alumno = ? AND sa.estado = ? AND m.idtipo_matricula = ?;', [Auth::user()->dni, 1, 1]);

        //cursos subsa
        $querySubsa = DB::connection('mysql_segunda')->select('SELECT up.nombre, c.nombre_curso, c.credito, c.horas FROM incripcion_curso ic
            INNER JOIN matricula m ON ic.idmatricula = m.idmatricula
            INNER JOIN docente_curso dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN docente d ON dc.id_docente = d.iddocente
            INNER JOIN userprofile up ON d.id_users = up.id_users
            INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN cursos c ON dc.idcursos = c.idcursos
            WHERE m.id_alumno = ? AND sa.estado = ? AND m.idtipo_matricula = ?;', [Auth::user()->dni, 1, 2]);


        $query = DB::connection('mysql_segunda')->select('SELECT dc.tipodocente_curso, ma.idmalla_curricular, doc.id_users, plae.malla_curricular_idmalla_curricular, plae.idcursos, plae.idciclos,
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
            INNER JOIN incripcion_curso ic ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN matricula m ON ic.idmatricula = m.idmatricula
            WHERE m.id_alumno = ? AND semaca.estado = ?;', [Auth::user()->dni, 1]);

        $iddocenteCurso = [];

        foreach ($query as $que) {
            $iddocenteCurso[] = $que->iddocente_curso;
        }

        $vall = implode(',', array_fill(0, count($iddocenteCurso), '?'));

        //deveulve los horarios por horas basado en el id_docente_cursoooo
        $quer = "SELECT * FROM horario AS ho
            RIGHT JOIN docente_curso AS dc ON ho.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS cur ON dc.idcursos = cur.idcursos
            INNER JOIN docente AS doce ON dc.id_docente = doce.iddocente
            INNER JOIN semestre_academico AS sa ON dc.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN hora AS ha ON ho.idhora = ha.idhora
            INNER JOIN aula AS au ON ho.idaula = au.idaula
            WHERE sa.estado = ? AND dc.tipodocente_curso = ? AND id_docente_curso IN ($vall)";

        $bindingsRegu = array_merge([1, 1], $iddocenteCurso);
        $bindingsSubsa = array_merge([1, 2], $iddocenteCurso);


        $queryHorariR = DB::connection('mysql_segunda')->select($quer, $bindingsRegu);
        $queryHorariS = DB::connection('mysql_segunda')->select($quer, $bindingsSubsa);

        /////////////////////////////
        //para saber si es regular o subsa
        $contMañaRe = 0;
        $contTardRe = 0;
        $contMañasub = 0;
        $contTardsub = 0;

        if ($queryHorariR) {
            foreach ($queryHorariR as $tip) {
                if ($tip->tipodocente_curso === 1 && $tip->idturno === 1) {
                    $contMañaRe++;
                } elseif ($tip->tipodocente_curso === 1 && $tip->idturno === 2) {
                    $contTardRe++;
                }
            }
        }

        if ($queryHorariS) {
            foreach ($queryHorariS as $tips) {
                if ($tips->tipodocente_curso === 2 && $tips->idturno === 1) {
                    $contMañasub++;
                } elseif ($tips->tipodocente_curso === 2 && $tips->idturno === 2) {
                    $contTardsub++;
                }
            }
        }

        $dias = DB::connection('mysql_segunda')->table('dias')->get();
        $horas = DB::connection('mysql_segunda')->table('hora')->get();
        // return view('docente.horario.verHorario', compact('nom_usu', 'queryHorari', 'dias', 'horas', 'contMaña', 'contTard', 'año', 'periodo', 'nombre_de_carrera', 'nombre_ciclo', 'nombre_turno', 'codigo_aula', 'aula_nombre', 'año_de_inicio', 'nom_seccion'));
        return view('alumno.horario', compact('nom_usu', 'nom_carre', 'queryHorariR', 'queryHorariS', 'dias', 'horas', 'contMañaRe', 'contTardRe', 'contMañasub', 'contTardsub', 'datosR', 'datosS', 'queryRegu', 'querySubsa'));
    }
}
