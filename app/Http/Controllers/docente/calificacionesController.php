<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Svg\Tag\Rect;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

class calificacionesController extends Controller
{
    public function calificaciones()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $listCursos = DB::connection('mysql_segunda')->select("SELECT dc.tipodocente_curso, sec.idseccion, sec.nom_seccion, ma.año_de_inicio, doc.id_users, plae.malla_curricular_idmalla_curricular,
                    plae.idcursos, plae.idciclos, carre.nombre_de_carrera, cur.nombre_curso, cic.nombre_ciclo, dc.iddocente_curso,
                    dc.id_docente, semaca.año, semaca.periodo, semaca.estado, t.nombre_turno, a.aula_nombre, a.codigo_aula, t.idturno
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
                    LEFT JOIN seccion AS sec ON h.idseccion = sec.idseccion
                    WHERE doc.id_users = ? AND semaca.estado = ?
                    GROUP BY cur.idcursos, doc.id_users, plae.malla_curricular_idmalla_curricular, plae.idcursos, plae.idciclos,
                    carre.nombre_de_carrera, cur.nombre_curso, cic.nombre_ciclo, dc.iddocente_curso, dc.id_docente, semaca.año,
                    semaca.periodo, semaca.estado, t.nombre_turno, a.aula_nombre, a.codigo_aula, ma.año_de_inicio, sec.idseccion,
                    sec.nom_seccion, dc.tipodocente_curso, t.idturno ORDER BY dc.tipodocente_curso, cur.idcursos ASC;", [Auth::user()->id, 1]);
        // dd($listCursos);
        return view('docente.calificaciones.index', compact('listCursos', 'nom_usu'));
    }

    public function verAlumnos(Request $request)
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $id_docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', Auth::user()->id)->pluck('iddocente')->first();
        // dump($id_docente);
        // dd($request);

        $cursoAsig = DB::connection('mysql_segunda')->table('docente_curso')
            ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
            ->where('idcursos', $request->idcursos)
            ->where('id_docente', $id_docente)
            ->where('iddocente_curso', $request->iddocente_curso)
            ->where('semestre_academico.estado', 1)
            ->exists();

        if (!$cursoAsig) {
            abort(403, 'No tienes acceso a este curso.');
        }

        /// iddocente_curso
        // $verfi_iddocente_curso = DB::connection('mysql_segunda')->table('docente_curso')
        //     ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
        //     ->where()



        $iddocente_curso = $request->iddocente_curso;
        $nombre_de_carrera = $request->nombre_de_carrera;
        $nombre_curso = $request->nombre_curso;
        $idciclos = $request->idciclos;
        $nombre_ciclo = $request->nombre_ciclo;
        $año = $request->año;
        $periodo = $request->periodo;

        $año_de_inicio = $request->año_de_inicio;
        $nom_seccion = $request->nom_seccion;
        $idcursos = $request->idcursos;
        $tipodocente_curso = $request->tipodocente_curso;
        $idturno = $request->idturno;
        // dd($idturno);

        ////////////////////////////////////////////////////////////////////////

        $cantCompetenciaPorCurso = DB::connection('mysql_segunda')->table('cursos')
            ->where('idcursos', $idcursos)
            ->pluck('num_competencias');

        $verLasCompetencias = DB::connection('mysql_segunda')->table('incripcion_curso')
            ->selectRaw('
                MIN(idcompetencia1) AS idcompetencia1,
                MIN(idcompetencia2) AS idcompetencia2,
                MIN(idcompetencia3) AS idcompetencia3,
                (CASE WHEN MIN(idcompetencia1) IS NULL THEN 1 ELSE 0 END) +
                (CASE WHEN MIN(idcompetencia2) IS NULL THEN 1 ELSE 0 END) +
                (CASE WHEN MIN(idcompetencia3) IS NULL THEN 1 ELSE 0 END) AS total_nulls')
            ->where('id_docente_curso', $iddocente_curso)
            ->first();
        $ContCompeNullNota = $verLasCompetencias->total_nulls;

        $verLasCompetenciasT = DB::connection('mysql_segunda')->table('incripcion_curso')
            ->selectRaw('
                MIN(idcompetencia1) AS idcompetencia1,
                MIN(c1.competencia) AS name1,
                MIN(idcompetencia2) AS idcompetencia2,
                MIN(c2.competencia) AS name2,
                MIN(idcompetencia3) AS idcompetencia3,
                MIN(c3.competencia) AS name3')
            ->leftJoin('competencias AS c1', 'incripcion_curso.idcompetencia1', '=', 'c1.idcompetencias')
            ->leftJoin('competencias AS c2', 'incripcion_curso.idcompetencia2', '=', 'c2.idcompetencias')
            ->leftJoin('competencias AS c3', 'incripcion_curso.idcompetencia3', '=', 'c3.idcompetencias')

            ->where('id_docente_curso', $iddocente_curso)
            ->get()
            ->toArray();


        $calificaciones = DB::connection('mysql_segunda')->table('calificaciones')->where('idCalificaciones', '<>', 0)->get();

        // dd($verLasCompetenciasT);
        $listAlumnos = DB::connection('mysql_segunda')->select('SELECT mc.idmalla_curricular, mc.año_de_inicio, pe.idciclos,
            ic.idincripcion_curso, ic.idmatricula, ic.id_docente_curso, ic.credito, ic.idCalificaciones1, ic.recomendacion_nota1,
            ic.idCalificaciones2, ic.recomendacion_nota2, ic.idCalificaciones3, ic.recomendacion_nota3, ic.total, ic.estado_nota,
            dc.iddocente_curso, dc.idcursos, dc.id_docente, dc.tipodocente_curso, ma.id_alumno, ma.idsemestre_academico, ma.idestado_matricula,
            ma.idtipo_matricula, postu.idpostulante, postu.apellidos_pater_postulante, postu.apellidos_mater_postulante,
            postu.nombres_postulante, sa.estado
                FROM incripcion_curso as ic
                INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
                INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
                INNER JOIN plan_de_estudio AS pe ON pe.idcursos = c.idcursos
                INNER JOIN malla_curricular AS mc ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
                INNER JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
                INNER JOIN semestre_academico as sa ON ma.idsemestre_academico = sa.idsemestre_academico
                INNER JOIN gamnielb_admision.postulante as postu ON ma.id_alumno = postu.idpostulante
                WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND dc.tipodocente_curso = ?
                ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, 1, 1, $tipodocente_curso]);

        // dd($listAlumnos);

        $competencias = DB::connection('mysql_segunda')->select("SELECT cc.idcursos_compe, cc.idcursos, c.competencia, c.descripcion,
                dc.Nombre_dominio, c.idcompetencias
                FROM cursos_compe AS cc
                    INNER JOIN competencias AS c ON cc.idcompetencias = c.idcompetencias
                    INNER JOIN dominio_competencia AS dc ON c.iddominio_competencia = dc.iddominio_competencia
                    WHERE cc.idcursos = ?", [$idcursos]);

        return view('docente.calificaciones.showalumnos', compact(
            'iddocente_curso',
            'nombre_de_carrera',
            'nombre_curso',
            'idciclos',
            'nombre_ciclo',
            'año',
            'periodo',
            'año_de_inicio',
            'nom_seccion',
            'idcursos',
            'tipodocente_curso',

            'cantCompetenciaPorCurso',
            'ContCompeNullNota',
            'verLasCompetenciasT',
            'calificaciones',
            'listAlumnos',
            'competencias',
            'nom_usu',
            'idturno'
        ));
    }

    public function guardarAlumnos(Request $request)
    {
        // dd($request);
        $ContCompeNullNota = $request->ContCompeNullNota;
        $idincripcion_curso = $request->idincripcion_curso;
        $nota1 = $request->input('nota1', null);
        $reco1 = $request->input('reco1', null);

        $nota2 = $request->input('nota2', null);
        $reco2 = $request->input('reco2', null);

        $nota3 = $request->input('nota3', null);
        $reco3 = $request->input('reco3', null);

        $tot = [];
        $cont = 0;

        //con 3 competencias
        if ($ContCompeNullNota == 0) {
            if (count($idincripcion_curso) === count($nota1) && count($reco1) === count($nota2) && count($reco2) === count($nota3) && count($reco3) === count($idincripcion_curso)) {
                foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                    if ($nota1[$index] != 0 && $nota2[$index] != 0 && $nota3[$index] != 0) {
                        $sumTot = $nota1[$index] + $nota2[$index] + $nota3[$index];
                        $notaCasif = round((19 / 12) * $sumTot - (15 / 4));
                        if ($notaCasif == 17) {
                            $tot[] = 16;
                        } else if ($notaCasif == 14) {
                            $tot[] = 13;
                        } else {
                            $tot[] = $notaCasif;
                        }
                    } else {
                        $tot[] = null;
                    }
                }

                try {
                    DB::connection('mysql_segunda')->beginTransaction();
                    foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                        $update = DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                            'idCalificaciones1' => $nota1[$index],
                            'recomendacion_nota1' => $reco1[$index],
                            'idCalificaciones2' => $nota2[$index],
                            'recomendacion_nota2' => $reco2[$index],
                            'idCalificaciones3' => $nota3[$index],
                            'recomendacion_nota3' => $reco3[$index],
                            'total' => $tot[$index],
                        ]);

                        if ($tot[$index] == null) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 2
                            ]);
                        } elseif ($tot[$index] > 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 1
                            ]);
                        } elseif ($tot[$index] <= 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 0
                            ]);
                        }

                        if ($update) {
                            $cont++;
                        }
                    }

                    //////////////////////////////////// actualizar competen ////////////////////////////////////
                    $updateCompetencias = DB::connection('mysql_segunda')->table('incripcion_curso')
                        ->where('id_docente_curso', $request->iddocente_curso)
                        ->update([
                            'idcompetencia1' => $request->input('idcompetencia1', null),
                            'idcompetencia2' => $request->input('idcompetencia2', null),
                            'idcompetencia3' => $request->input('idcompetencia3', null)
                        ]);
                    ////////////////////////////////////  //////////////////////////////////// ////////////////
                    DB::connection('mysql_segunda')->commit();
                    return response()->json([
                        'message' => 'guardadas correctamente...',
                        'actualizados' => $cont
                    ]);
                } catch (\Throwable $th) {
                    DB::connection('mysql_segunda')->rollBack();
                    return response()->json([
                        'message' => 'Error al registrar notas ...',
                        'actualizados' => 0
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'error',
                    'actualizados' => $cont
                ]);
            }
        }

        //con 2 competencias
        if ($ContCompeNullNota == 1) {
            if (count($idincripcion_curso) === count($nota1) && count($reco1) === count($nota2) && count($reco2) === count($idincripcion_curso)) {
                // dd('josefefefe');
                foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                    if ($nota1[$index] != 0 && $nota2[$index] != 0) {
                        $sumTot = $nota1[$index] + $nota2[$index];
                        $notaCasif = round((19 / 8) * $sumTot - (15 / 4));
                        if ($notaCasif == 17) {
                            $tot[] = 16;
                        } else if ($notaCasif == 14) {
                            $tot[] = 13;
                        } else {
                            $tot[] = $notaCasif;
                        }
                    } else {
                        $tot[] = null;
                    }
                }

                try {
                    DB::connection('mysql_segunda')->beginTransaction();
                    foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                        $update = DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                            'idCalificaciones1' => $nota1[$index],
                            'recomendacion_nota1' => $reco1[$index],
                            'idCalificaciones2' => $nota2[$index],
                            'recomendacion_nota2' => $reco2[$index],
                            'total' => $tot[$index],
                        ]);

                        if ($tot[$index] == null) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 2
                            ]);
                        } elseif ($tot[$index] > 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 1
                            ]);
                        } elseif ($tot[$index] <= 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 0
                            ]);
                        }

                        if ($update) {
                            $cont++;
                        }
                    }

                    //////////////////////////////////// actualizar competen ////////////////////////////////////
                    $updateCompetencias = DB::connection('mysql_segunda')->table('incripcion_curso')
                        ->where('id_docente_curso', $request->iddocente_curso)
                        ->update([
                            'idcompetencia1' => $request->input('idcompetencia1', null),
                            'idcompetencia2' => $request->input('idcompetencia2', null),
                            'idcompetencia3' => $request->input('idcompetencia3', null)
                        ]);
                    ////////////////////////////////////  //////////////////////////////////// ////////////////
                    DB::connection('mysql_segunda')->commit();
                    return response()->json([
                        'message' => 'guardadas correctamente...',
                        'actualizados' => $cont
                    ]);
                } catch (\Throwable $th) {
                    DB::connection('mysql_segunda')->rollBack();
                    return response()->json([
                        'message' => 'Error al registrar notas ...',
                        'actualizados' => 0
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'error',
                    'actualizados' => $cont
                ]);
            }
        }

        //con 1 competencias
        if ($ContCompeNullNota == 2) {
            if (count($idincripcion_curso) === count($nota1) && count($reco1) === count($idincripcion_curso)) {
                foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                    if ($nota1[$index] != 0) {
                        $sumTot = $nota1[$index];
                        $notaCasif = round((19 / 4) * $sumTot - (15 / 4));
                        if ($notaCasif == 17) {
                            $tot[] = 16;
                        } else if ($notaCasif == 14) {
                            $tot[] = 13;
                        } else {
                            $tot[] = $notaCasif;
                        }
                    } else {
                        $tot[] = null;
                    }
                }
                // dd($tot);
                try {
                    DB::connection('mysql_segunda')->beginTransaction();
                    foreach ($idincripcion_curso as $index => $idincripcion_cur) {
                        $update = DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                            'idCalificaciones1' => $nota1[$index],
                            'recomendacion_nota1' => $reco1[$index],
                            'total' => $tot[$index],
                        ]);

                        if ($tot[$index] == null) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 2
                            ]);
                        } elseif ($tot[$index] > 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 1
                            ]);
                        } elseif ($tot[$index] <= 10) {
                            DB::connection('mysql_segunda')->table('incripcion_curso')->where('idincripcion_curso', $idincripcion_cur)->update([
                                'estado_nota' => 0
                            ]);
                        }

                        if ($update) {
                            $cont++;
                        }
                    }

                    //////////////////////////////////// actualizar competen ////////////////////////////////////
                    $updateCompetencias = DB::connection('mysql_segunda')->table('incripcion_curso')
                        ->where('id_docente_curso', $request->iddocente_curso)
                        ->update([
                            'idcompetencia1' => $request->input('idcompetencia1', null),
                            'idcompetencia2' => $request->input('idcompetencia2', null),
                            'idcompetencia3' => $request->input('idcompetencia3', null)
                        ]);
                    ////////////////////////////////////  //////////////////////////////////// ////////////////
                    DB::connection('mysql_segunda')->commit();
                    return response()->json([
                        'message' => 'guardadas correctamente...',
                        'actualizados' => $cont
                    ]);
                } catch (\Throwable $th) {
                    DB::connection('mysql_segunda')->rollBack();
                    return response()->json([
                        'message' => 'Error al registrar notas ...',
                        'actualizados' => 0
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'error',
                    'actualizados' => $cont
                ]);
            }
        }
    }

    public function asignarCompetencias(Request $request)
    {
        // dd($request);
        $iddocente_curso = $request->iddocente_curso;
        $compe1 = $request->input('compe1', null);
        $compe2 = $request->input('compe2', null);
        $compe3 = $request->input('compe3', null);

        $updateCompetencias = DB::connection('mysql_segunda')->table('incripcion_curso')
            ->where('id_docente_curso', $iddocente_curso)
            ->update([
                'idcompetencia1' => $compe1,
                'idcompetencia2' => $compe2,
                'idcompetencia3' => $compe3
            ]);
    }

    public function pdfActaEvalu($iddocente_curso, $idturno)
    {
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

        $num_Competen = DB::connection('mysql_segunda')
            ->table('docente_curso as dc')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('dc.iddocente_curso', $iddocente_curso)
            ->first();
        // dd($num_Competen->num_competencias);

        // dd($query1);
        $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ma.id_alumno, CONCAT(postu.apellidos_pater_postulante, " ",
        postu.apellidos_mater_postulante, " ", postu.nombres_postulante) AS ape_nom, ic.total, c.credito, ic.recomendacion_nota3,
        ma.idestado_matricula, ic.estado_nota,

            ca1.nom_califi AS cal1,
            ca2.nom_califi AS cal2,
            ca3.nom_califi AS cal3
            FROM incripcion_curso as ic
            INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
            INNER JOIN plan_de_estudio AS pe ON pe.idcursos = c.idcursos
            INNER JOIN malla_curricular AS mc ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
            left JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
            INNER JOIN semestre_academico as sa ON ma.idsemestre_academico = sa.idsemestre_academico

            INNER JOIN calificaciones ca1 ON ic.idCalificaciones1 = ca1.idCalificaciones
            INNER JOIN calificaciones ca2 ON ic.idCalificaciones2 = ca2.idCalificaciones
            INNER JOIN calificaciones ca3 ON ic.idCalificaciones3 = ca3.idCalificaciones

            INNER JOIN gamnielb_admision.postulante as postu ON ma.id_alumno = postu.idpostulante
            WHERE ic.id_docente_curso = ? AND ma.id_turno = ?
            ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, $idturno]);

        // dd($iddocente_curso. ' '.$idturno);


        $encargados = DB::connection('mysql_segunda')->select('SELECT upd.nombre AS direc, e.reso_direc, ups.nombre AS secre, e.logo FROM encargados e
            INNER JOIN userprofile upd ON e.iduserProfile_direc = upd.iduserProfile
            INNER JOIN userprofile ups ON e.iduserProfile_secre = ups.iduserProfile
            WHERE e.estado = 1;');

        $fechaHora = now();
        // dd($listAlumnos);

        $pdf = PDF::loadView('admin.reportesa.pdf.pdfActaEvaluacionDoce', ['fechaHora' => $fechaHora, 'query1' => $query1, 'listAlumnos' => $listAlumnos, 'encargados' => $encargados, 'num_Competen' => $num_Competen])->setPaper('a4', 'landscape');
        return $pdf->stream();
    }
}
