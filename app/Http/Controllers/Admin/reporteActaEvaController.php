<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class reporteActaEvaController extends Controller
{
    public function index()
    {
        return view('admin.reportesa.index');
    }

    public function pdfActaEvalu($iddocente_curso, $tipo)
    {
        // dd($tipo);
        $query1 = DB::connection('mysql_segunda')->select('SELECT mc.nombre_malla_curricular, c.nombre_curso, ci.nombre_ciclo,
            sa.año, sa.periodo, up.nombre, s.nom_seccion FROM plan_de_estudio pe
            INNER JOIN malla_curricular mc ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
            INNER JOIN cursos c ON pe.idcursos = c.idcursos
            INNER JOIN ciclos ci ON pe.idciclos = ci.idciclos
            INNER JOIN docente_curso dc ON dc.idcursos = c.idcursos
            INNER JOIN semestre_academico sa ON dc.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN docente d ON dc.id_docente = d.iddocente
            INNER JOIN userprofile up ON d.id_users = up.id_users
            INNER JOIN horario h ON h.id_docente_curso = dc.iddocente_curso
            INNER JOIN seccion s ON h.idseccion = s.idseccion WHERE dc.iddocente_curso = ?', [$iddocente_curso]);

        // dd($query1);
        $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ma.id_alumno, CONCAT(postu.apellidos_pater_postulante, " ",
        postu.apellidos_mater_postulante, " ", postu.nombres_postulante) AS ape_nom, ic.total, c.credito, ic.recomendacion_nota3,
        ma.idestado_matricula, ic.estado_nota
            FROM incripcion_curso as ic
            INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
            INNER JOIN plan_de_estudio AS pe ON pe.idcursos = c.idcursos
            INNER JOIN malla_curricular AS mc ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
            INNER JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
            INNER JOIN semestre_academico as sa ON ma.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN gamnielb_admision.postulante as postu ON ma.id_alumno = postu.idpostulante
            WHERE dc.iddocente_curso = ? AND ma.idtipo_matricula = ?
            ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, $tipo]);
        // dd($listAlumnos);
        $encargados = DB::connection('mysql_segunda')->select('SELECT upd.nombre AS direc, e.reso_direc, ups.nombre AS secre, e.logo FROM encargados e
            INNER JOIN userprofile upd ON e.iduserProfile_direc = upd.iduserProfile
            INNER JOIN userprofile ups ON e.iduserProfile_secre = ups.iduserProfile
            WHERE e.estado = 1;');

        $pdf = PDF::loadView('admin.reportesa.pdf.pdfActaEvaluacion', ['query1' => $query1, 'listAlumnos' => $listAlumnos, 'encargados' => $encargados])->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    public function califiCursoindex()
    {
        return view('admin.reportesa.califiCursoIndex');
    }

    public function pdfcalifiCurso($id_alumno, $idmatricula)
    {
        // dump($id_alumno);
        // dump($idsemestre_academico);
        //datos alum
        $queryDa = DB::connection('mysql_segunda')->select("SELECT
                    CONCAT(sa.año,' - ',sa.periodo) AS aperi,
                    mc.nombre_malla_curricular,
                    CONCAT(ci.nombre_ciclo, ' - ', se.nom_seccion) AS cs,
                    CONCAT(UPPER(p.apellidos_pater_postulante),' ',UPPER(p.apellidos_mater_postulante), ' ', UPPER(p.nombres_postulante)) AS ape_nom,
                    m.id_turno, m.id_alumno, p.foto_postulante
                    FROM matricula m
                    INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
                    INNER JOIN malla_curricular mc ON m.idmalla	= mc.idmalla_curricular
                    INNER JOIN ciclos ci ON m.ciclo_matricula = ci.idciclos
                    INNER JOIN seccion se ON m.idseccion = se.idseccion
                    INNER JOIN gamnielb_admision.postulante p ON m.id_alumno = p.idpostulante
                    WHERE m.id_alumno = ? AND m.idmatricula = ?;", [$id_alumno, $idmatricula]);



        //notas alumn
        $query = DB::connection('mysql_segunda')->select("SELECT CONCAT(sa.año, ' ', sa.periodo) AS semestre_academico,
                    sa.idsemestre_academico,
                    m.idmalla AS malla_curricular_idmalla_curricular,
                    c.idcursos,
                    c.nombre_curso,
                    cali1.nom_califi AS cal1, cali2.nom_califi AS cal2, cali3.nom_califi AS cal3,
                    com1.competencia AS com1, com2.competencia AS com2, com3.competencia AS com3,
                    ic.recomendacion_nota1, ic.recomendacion_nota2, ic.recomendacion_nota3,
                    ic.total AS nota_final,
                    m.id_turno,
                    pde.idciclos,
                    ci.nombre_ciclo,
                    c.credito,
                    ic.total
                    FROM matricula m
                    INNER JOIN semestre_academico sa ON m.idsemestre_academico = sa.idsemestre_academico
                    INNER JOIN incripcion_curso ic ON m.idmatricula = ic.idmatricula
                    INNER JOIN calificaciones cali1 ON ic.idCalificaciones1 = cali1.idCalificaciones
                    INNER JOIN calificaciones cali2 ON ic.idCalificaciones2 = cali2.idCalificaciones
                    INNER JOIN calificaciones cali3 ON ic.idCalificaciones3 = cali3.idCalificaciones

                    LEFT JOIN competencias com1 ON ic.idcompetencia1 = com1.idcompetencias
                    LEFT JOIN competencias com2 ON ic.idcompetencia2 = com2.idcompetencias
                    LEFT JOIN competencias com3 ON ic.idcompetencia3 = com3.idcompetencias

                    INNER JOIN docente_curso dc ON ic.id_docente_curso = dc.iddocente_curso
                    INNER JOIN cursos c ON dc.idcursos = c.idcursos
                    INNER JOIN plan_de_estudio pde  ON pde.idcursos = c.idcursos AND pde.malla_curricular_idmalla_curricular = m.idmalla
                    INNER JOIN ciclos ci ON pde.idciclos = ci.idciclos
                    WHERE m.id_alumno = ? AND m.idmatricula = ?
                    ORDER BY sa.año DESC, sa.periodo DESC, pde.idciclos, c.idcursos;", [$id_alumno, $idmatricula]);

        $nullsCursos = [];
        $contNull = 0;
        foreach ($query as $index => $quer) {
            if (is_null($quer->com1)) {
                $contNull++;
            }
            if (is_null($quer->com2)) {
                $contNull++;
            }
            if (is_null($quer->com3)) {
                $contNull++;
            }
            $nullsCursos[] = $contNull;
            $contNull = 0;
        }
        // dump($nullsCursos);
        // dump($queryDa);
        // dump($query);
        // dd($idsemestre_academico);

        $encargados = DB::connection('mysql_segunda')->select('SELECT upd.nombre AS direc, e.reso_direc, ups.nombre AS secre, e.logo FROM encargados e
            INNER JOIN userprofile upd ON e.iduserProfile_direc = upd.iduserProfile
            INNER JOIN userprofile ups ON e.iduserProfile_secre = ups.iduserProfile
            WHERE e.estado = 1;');
        $pdf = PDF::loadView('admin.reportesa.pdf.pdfCalifiCurso', ['queryDa' => $queryDa, 'query' => $query, 'nullsCursos' => $nullsCursos, 'encargados' => $encargados])->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    public function indexnotageneral()
    {
        return view('admin.reportesa.indexreportegeneral');
    }


}
