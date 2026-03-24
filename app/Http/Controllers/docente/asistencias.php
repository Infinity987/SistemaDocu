<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Svg\Tag\Rect;

class asistencias extends Controller
{
    // public function index($fecha, $idcursos, $iddocente_curso, $nombre_de_carrera, $nombre_curso, $idciclos, $nombre_ciclo, $año, $periodo, $año_de_inicio, $nom_seccion, $tipodocente_curso)
    public function index(Request $request)
    {
        // dd($request);
        $fecha = $request->query('fecha');
        $fechaCarbon = Carbon::parse($request->query('fecha'));

        $idcursos = $request->query('idcursos');
        $iddocente_curso = $request->query('iddocente_curso');
        $nombre_de_carrera = urldecode($request->query('nombre_de_carrera'));
        $nombre_curso = urldecode($request->query('nombre_curso'));
        $idciclos = $request->query('idciclos');
        $nombre_ciclo = urldecode($request->query('nombre_ciclo'));
        $año = $request->query('año');
        $periodo = $request->query('periodo');
        $año_de_inicio = $request->query('año_de_inicio');
        $nom_seccion = $request->query('nom_seccion');
        $tipodocente_curso = $request->query('tipodocente_curso');
        $idturno = $request->idturno;

        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $id_docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', Auth::user()->id)->pluck('iddocente')->first();

        $cursoAsig = DB::connection('mysql_segunda')->table('docente_curso')
            ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
            ->where('idcursos', $idcursos)
            ->where('id_docente', $id_docente)
            ->where('iddocente_curso', $iddocente_curso)
            ->where('semestre_academico.estado', 1)
            ->exists();
        // dd($cursoAsig);

        if (!$cursoAsig) {
            abort(403, 'No tienes acceso a este curso.');
        }

        //////////////////////////////////// asistencia
        $fechasAsistencia = DB::connection('mysql_segunda')->table('semestre_academico')
            ->select('fech_inicio_asis', 'fech_fin_asis')->where('estado', 1)->first();

        $inicio = Carbon::parse($fechasAsistencia->fech_inicio_asis);
        $fin = Carbon::parse($fechasAsistencia->fech_fin_asis);

        // Diferencia total en días
        $dias = $inicio->diffInDays($fin);

        // Total de semanas (redondeado hacia arriba si quieres incluir semanas incompletas)
        // $semanas = ceil($dias / 7);

        // semanas completas:
        $semanas_completas = floor($dias / 7);
        // dd($fechasAsistencia);

        if ($fechaCarbon->between($inicio, $fin)) {
            $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso, asis.estado AS estado_raw, asis.observacion, asis.fecha,
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
                        LEFT JOIN asistencias as asis ON asis.idincripcion_curso = ic.idincripcion_curso AND asis.fecha = ?
                        WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
                        ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$fecha, $iddocente_curso, 1, 1, $idciclos, $tipodocente_curso]);

            // dd($listAlumnos);
            if (empty($listAlumnos)) {
                $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso,
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
                        WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
                        ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, 1, 1, $idciclos, $tipodocente_curso]);
            }

            //cantidad de dias que dicta clases el docente durante la semana
            $quer = "SELECT ho.idHorario, ho.id_docente_curso, ho.idhora, ho.iddias, ho.idaula, ho.idseccion, cur.idcursos, doce.iddocente, cur.nombre_curso, ha.idturno FROM horario AS ho
            RIGHT JOIN docente_curso AS dc ON ho.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS cur ON dc.idcursos = cur.idcursos
            INNER JOIN docente AS doce ON dc.id_docente = doce.iddocente
            INNER JOIN semestre_academico AS sa ON dc.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN hora AS ha ON ho.idhora = ha.idhora
            INNER JOIN aula AS au ON ho.idaula = au.idaula
            WHERE doce.id_users = ? AND sa.estado = ? AND id_docente_curso = ? ";

            $bindings = ([Auth::user()->id, 1, $iddocente_curso]);

            $queryHorari = DB::connection('mysql_segunda')->select($quer, $bindings);
            $cantDias = [];
            foreach ($queryHorari as $veces) {
                if (!empty($veces->iddias)) {
                    $cantDias[$veces->iddias] = true;
                }
            }

            $diasPorSemana = count($cantDias);

            //para saber el porcentaje de asistencia respecto a las sessiones.
            $registros = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso, asis.estado AS estado_raw, asis.fecha,
                    postu.idpostulante, postu.apellidos_pater_postulante, postu.apellidos_mater_postulante, postu.nombres_postulante
                FROM incripcion_curso AS ic
                INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
                INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
                INNER JOIN plan_de_estudio AS pe ON pe.idcursos = c.idcursos
                INNER JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
                INNER JOIN semestre_academico as sa ON ma.idsemestre_academico = sa.idsemestre_academico
                INNER JOIN gamnielb_admision.postulante AS postu ON ma.id_alumno = postu.idpostulante
                LEFT JOIN asistencias AS asis ON asis.idincripcion_curso = ic.idincripcion_curso
                WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
                ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante, asis.fecha ASC;
                ', [$iddocente_curso, 1, 1, $idciclos, $tipodocente_curso]);

            $totalSesionesFijas = $diasPorSemana * $semanas_completas; // ejemplo: 3 días/semana → 54 sesiones
            $umbralFaltas = round($totalSesionesFijas * 0.30); // 30% de 54 = 16
            // dd($umbralFaltas);
            $alumnos = [];

            foreach ($registros as $r) {
                $id = $r->idpostulante;
                $estado = $r->estado_raw;
                $fechadd = $r->fecha;

                if (!isset($alumnos[$id])) {
                    $alumnos[$id] = [
                        'nombre' => "{$r->apellidos_pater_postulante} {$r->apellidos_mater_postulante}, {$r->nombres_postulante}",
                        'asistencias' => 0,
                        'sesiones_registradas' => [],
                    ];
                }

                // registrar fechadd como sesión única
                if ($fechadd) {
                    $alumnos[$id]['sesiones_registradas'][$fechadd] = true;
                }

                // contar asistencia válida
                if (in_array($estado, ['P', 'T', 'J'])) {
                    $alumnos[$id]['asistencias']++;
                }
            }

            // cálculo final por alumno
            foreach ($alumnos as $id => &$a) {
                $sesionesActuales = count($a['sesiones_registradas']);
                $a['faltas'] = $sesionesActuales - $a['asistencias'];
                $a['porcentaje'] = $sesionesActuales > 0
                    ? round(($a['asistencias'] / $sesionesActuales) * 100, 2)
                    : 0;

                // alerta por faltas acumuladas
                if ($a['faltas'] >= $umbralFaltas) {
                    $a['color'] = '#dc3545';
                    $a['mensaje'] = "Alerta: {$a['faltas']} faltas (≥ 30%)";
                } elseif ($a['faltas'] >= round($totalSesionesFijas * 0.15)) {
                    $a['color'] = '#ffc107';
                    $a['mensaje'] = "Advertencia: {$a['faltas']} faltas";
                } else {
                    $a['color'] = '#28a745';
                    $a['mensaje'] = "Asistencia aceptable";
                }
            }

            return view('docente.asistencia.index', compact(
                'fecha',
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

                'listAlumnos',
                'nom_usu',
                'fechasAsistencia',

                'alumnos',
                'inicio',
                'fin',
                'idturno'
            ));
        } else {
            abort(403, 'La fecha de asistencia está fuera del rango permitido para asistencia, deberá consultar con el ADMINISTRADOR DEL SISTEMA');
        }
    }

    public function guardarAsistencia(Request $request)
    {
        $asistencias = $request->input('asistencia', []);
        $observaciones = $request->input('observacion', []);

        try {
            DB::connection('mysql_segunda')->beginTransaction();

            foreach ($asistencias as $idInscripcion  => $valorAsis) {
                $observacion = $observaciones[$idInscripcion] ?? null;

                DB::connection('mysql_segunda')->table('asistencias')->updateOrInsert(
                    [
                        // condiciones para buscar
                        'idincripcion_curso' => $idInscripcion,
                        'fecha' => $request->fecha,
                    ],
                    [
                        // valores a actualizar o insertar
                        'estado' => $valorAsis,
                        'observacion' => $observacion,
                        'created_at' => now(), // solo se toma si es nuevo
                    ]
                );
            }
            DB::connection('mysql_segunda')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Asistencia registrada correctamente.'
            ]);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ',
            ], 500);
        }
    }
    // public function totalAsist($fecha, $idcursos, $iddocente_curso, $nombre_de_carrera, $nombre_curso, $idciclos, $nombre_ciclo, $año, $periodo, $año_de_inicio, $nom_seccion, $tipodocente_curso)

    public function totalAsist(Request $request)
    {
        // dd($request);
        $fecha = $request->query('fecha');
        $fechaCarbon = Carbon::parse($request->query('fecha'));

        $idcursos = $request->query('idcurso');
        $iddocente_curso = $request->query('iddocente_curso');
        $nombre_de_carrera = urldecode($request->query('nombre_de_carrera'));
        $nombre_curso = urldecode($request->query('nombre_curso'));
        $idciclos = $request->query('idciclos');
        $nombre_ciclo = urldecode($request->query('nombre_ciclo'));
        $año = $request->query('año');
        $periodo = $request->query('periodo');
        $año_de_inicio = $request->query('año_de_inicio');
        $nom_seccion = $request->query('nom_seccion');
        $tipodocente_curso = $request->query('tipodocente_curso');
        $idturno = $request->idturno;

        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $id_docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', Auth::user()->id)->pluck('iddocente')->first();

        $cursoAsig = DB::connection('mysql_segunda')->table('docente_curso')
            ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
            ->where('idcursos', $idcursos)
            ->where('id_docente', $id_docente)
            ->where('iddocente_curso', $request->iddocente_curso)
            ->where('semestre_academico.estado', 1)
            ->exists();

        if (!$cursoAsig) {
            abort(403, 'No tienes acceso a este curso.');
        }

        ////////////////////////////////////asistencia
        $fechasAsistencia = DB::connection('mysql_segunda')->table('semestre_academico')
            ->select('fech_inicio_asis', 'fech_fin_asis')->where('estado', 1)->first();

        $fecha_inicio = Carbon::parse($fechasAsistencia->fech_inicio_asis);
        $fecha_fin = Carbon::parse($fechasAsistencia->fech_fin_asis);

        // Validar que las fechas ingresadas estén dentro del rango
        $request->validate([
            'fecha_inicio_inter' => [
                'required',
                'date',
                'after_or_equal:' . $fecha_inicio->format('Y-m-d'),
                'before_or_equal:' . $fecha_fin->format('Y-m-d'),
            ],
            'fecha_fin_inter' => [
                'required',
                'date',
                'after_or_equal:fecha_inicio_inter',
                'before_or_equal:' . $fecha_fin->format('Y-m-d'),
            ],
        ]);

        if ($fechaCarbon->between($request->fecha_inicio_inter, $request->fecha_fin_inter)) {
            // Generar todas las fechas
            $fechas = [];
            $current = \Carbon\Carbon::parse($request->fecha_inicio_inter);
            $end = \Carbon\Carbon::parse($request->fecha_fin_inter);

            while ($current <= $end) {
                $fechas[] = $current->format('Y-m-d');
                $current->addDay();
            }


            $asistencias = DB::connection('mysql_segunda')
                ->table('asistencias')
                ->whereBetween('fecha', [$request->fecha_inicio_inter, $request->fecha_fin_inter])
                ->get()
                ->keyBy(function ($item) {
                    return $item->idincripcion_curso . '-' . $item->fecha;
                });

            ////////////////////////////////////////////////////////////////////////
            $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso,
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
                WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
                ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, 1, 1, $idciclos, $tipodocente_curso]);

            $fech_ini = $request->fecha_inicio_inter;
            $fech_fin = $request->fecha_fin_inter;

            return view('docente.asistencia.totalAsist', compact(
                'fecha',
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

                'listAlumnos',
                'nom_usu',
                'fechas',
                'asistencias',

                'fech_ini',
                'fech_fin',
                'idturno'
            ));
        } else {
            abort(403, 'La fecha de asistencia está fuera del rango permitido para asistencia, deberá consultar con el ADMINISTRADOR DEL SISTEMA');
        }
    }

    public function actuAsis(Request $request)
    {
        // dd($request);
        try {
            DB::connection('mysql_segunda')->beginTransaction();
            $existe = DB::connection('mysql_segunda')->table('asistencias')
                ->where('idincripcion_curso', $request->idInscripcion)
                ->where('fecha', $request->fecha)
                ->exists();

            if ($existe) {
                DB::connection('mysql_segunda')->table('asistencias')
                    ->where('idincripcion_curso', $request->idInscripcion)
                    ->where('fecha', $request->fecha)
                    ->update([
                        'estado' => $request->estado
                    ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No esta insertado, Debera registrar primero la asistencia en la fecha indicada.'
                ]);
            }

            DB::connection('mysql_segunda')->commit();
            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente.'
            ]);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function eliminarAsis(Request $request)
    {
        $request->validate([
            'fecha' => ['required', 'date'],
        ]);


        $id_docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', Auth::user()->id)->pluck('iddocente')->first();

        $cursoAsig = DB::connection('mysql_segunda')->table('docente_curso')
            ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
            ->where('id_docente', $id_docente)
            ->where('iddocente_curso', $request->iddocente_curso)
            ->where('semestre_academico.estado', 1)
            ->exists();
        // dd($cursoAsig);

        if (!$cursoAsig) {
            abort(403, 'No tienes acceso a este curso.');
        }

        $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso
            FROM incripcion_curso as ic
            INNER JOIN docente_curso AS dc ON ic.id_docente_curso = dc.iddocente_curso
            INNER JOIN cursos AS c ON dc.idcursos = c.idcursos
            INNER JOIN plan_de_estudio AS pe ON pe.idcursos = c.idcursos
            INNER JOIN malla_curricular AS mc ON pe.malla_curricular_idmalla_curricular = mc.idmalla_curricular
            INNER JOIN matricula AS ma ON ic.idmatricula = ma.idmatricula
            INNER JOIN semestre_academico as sa ON ma.idsemestre_academico = sa.idsemestre_academico
            INNER JOIN gamnielb_admision.postulante as postu ON ma.id_alumno = postu.idpostulante
            LEFT JOIN asistencias as asis ON asis.idincripcion_curso = ic.idincripcion_curso AND asis.fecha = ?
            WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
            ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$request->fecha, $request->iddocente_curso, 1, 1, $request->idciclos, $request->tipodocente_curso]);

        try {
            DB::connection('mysql_segunda')->beginTransaction();

            foreach ($listAlumnos as $value) {
                DB::connection('mysql_segunda')->table('asistencias')
                    ->where('idincripcion_curso',  $value->idincripcion_curso)
                    ->whereDate('fecha', $request->fecha)
                    ->delete();
            }
            DB::connection('mysql_segunda')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Asistencia eliminado correctamente'
            ]);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ',
                $th->getMessage(),
            ], 500);
        }
    }

    public function exportarPDF(Request $request)
    {

        $fecha = $request->query('fecha');
        $fechaCarbon = Carbon::parse($request->query('fecha'));

        $idcursos = $request->query('idcurso');
        $iddocente_curso = $request->query('iddocente_curso');
        $nombre_de_carrera = urldecode($request->query('nombre_de_carrera'));
        $nombre_curso = urldecode($request->query('nombre_curso'));
        $idciclos = $request->query('idciclos');
        $nombre_ciclo = urldecode($request->query('nombre_ciclo'));
        $año = $request->query('año');
        $periodo = $request->query('periodo');
        $año_de_inicio = $request->query('año_de_inicio');
        $nom_seccion = $request->query('nom_seccion');
        $tipodocente_curso = $request->query('tipodocente_curso');

        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();

        $id_docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', Auth::user()->id)->pluck('iddocente')->first();

        $cursoAsig = DB::connection('mysql_segunda')->table('docente_curso')
            ->join('semestre_academico', 'docente_curso.idsemestre_academico', '=', 'semestre_academico.idsemestre_academico')
            ->where('idcursos', $idcursos)
            ->where('id_docente', $id_docente)
            ->where('iddocente_curso', $request->iddocente_curso)
            ->where('semestre_academico.estado', 1)
            ->exists();
        // dd($cursoAsig);

        if (!$cursoAsig) {
            abort(403, 'No tienes acceso a este curso.');
        }

        ///////////////////////////////////asistencia
        $fechasAsistencia = DB::connection('mysql_segunda')->table('semestre_academico')
            ->select('fech_inicio_asis', 'fech_fin_asis')->where('estado', 1)->first();

        $fecha_inicio = Carbon::parse($fechasAsistencia->fech_inicio_asis);
        $fecha_fin = Carbon::parse($fechasAsistencia->fech_fin_asis);

        if ($fechaCarbon->between($request->fech_ini, $request->fech_fin)) {
            $fechas = DB::connection('mysql_segunda')
                ->table('asistencias')
                ->join('incripcion_curso', 'asistencias.idincripcion_curso', '=', 'incripcion_curso.idincripcion_curso')
                ->join('docente_curso', 'incripcion_curso.id_docente_curso', '=', 'docente_curso.iddocente_curso')
                ->where('docente_curso.iddocente_curso', $iddocente_curso)
                ->whereBetween('asistencias.fecha', [$request->fech_ini, $request->fech_fin])
                ->distinct()
                ->orderBy('asistencias.fecha')
                ->pluck('asistencias.fecha')
                ->toArray();

            $asistencias = DB::connection('mysql_segunda')
                ->table('asistencias')
                ->whereBetween('fecha', [$request->fech_ini, $request->fech_fin])
                ->get()
                ->keyBy(function ($item) {
                    return $item->idincripcion_curso . '-' . $item->fecha;
                });

            ////////////////////////////////////////////////////////////////////////
            $listAlumnos = DB::connection('mysql_segunda')->select('SELECT ic.idincripcion_curso,
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
                WHERE dc.iddocente_curso = ? AND ma.idestado_matricula = ? AND sa.estado = ? AND pe.idciclos = ? AND dc.tipodocente_curso = ?
                ORDER BY postu.apellidos_pater_postulante, postu.apellidos_mater_postulante ASC;', [$iddocente_curso, 1, 1, $idciclos, $tipodocente_curso]);

            $fech_ini = $request->fech_ini;
            $fech_fin = $request->fech_fin;


            $bloquesDeFechas = collect($fechas)->chunk(30); // cada bloque con 30 fechas

            /////////////////////////////////////////////// Para la cabecera dek pdf
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

            // $encargados = DB::connection('mysql_segunda')->table('encargados')->where('estado', 1)->get();

            $encargados = DB::connection('mysql_segunda')->select('SELECT upd.nombre AS direc, e.reso_direc, ups.nombre AS secre, e.logo FROM encargados e
            INNER JOIN userprofile upd ON e.iduserProfile_direc = upd.iduserProfile
            INNER JOIN userprofile ups ON e.iduserProfile_secre = ups.iduserProfile
            WHERE e.estado = 1;');

            $pdf = Pdf::loadView('pdf.asistencia_horizontal', compact('query1', 'listAlumnos', 'fechas', 'asistencias', 'bloquesDeFechas', 'nombre_de_carrera', 'nombre_curso', 'nombre_ciclo', 'año', 'periodo', 'fech_ini', 'fech_fin', 'tipodocente_curso', 'encargados'))
                ->setPaper('a4', 'landscape');

            return $pdf->stream('asistencia_horizontal.pdf');
        } else {
            abort(403, 'La fecha de asistencia está fuera del rango permitido para asistencia, deberá consultar con el ADMINISTRADOR DEL SISTEMA');
        }
    }
}
