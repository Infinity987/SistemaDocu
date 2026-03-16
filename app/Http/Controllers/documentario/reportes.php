<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class reportes extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $id_depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);

        //consulta de cantidad de movi por dependen
        $CantMoviDepen = DB::connection('mysql_documentario')->select('SELECT dependencias.iddependencias, dependencias.nombre_dependencia, COALESCE(COUNT(movimiento.idmovimiento), 0) AS contdepen FROM dependencias
                                LEFT JOIN movimiento ON dependencias.iddependencias = movimiento.iddependencias_emior
                                WHERE dependencias.iddependencias NOT IN (1)
                                GROUP BY dependencias.iddependencias, dependencias.nombre_dependencia;');
        $depenBarChar = array_map(fn($item) => $item->nombre_dependencia, $CantMoviDepen);
        $cantBarChar = array_map(fn($item) => $item->contdepen, $CantMoviDepen);

        //cuantos doc o movimientos ha enviado cada dependencia y en qué estado se encuentra cada uno
        $cantDocusDepenEnvEst = DB::connection('mysql_documentario')->select('SELECT d.iddependencias, d.nombre_dependencia,
                                            COALESCE(SUM(CASE WHEN m.idestado = 1 THEN 1 ELSE 0 END), 0) AS estado_1,
                                            COALESCE(SUM(CASE WHEN m.idestado = 2 THEN 1 ELSE 0 END), 0) AS estado_2,
                                            COALESCE(SUM(CASE WHEN m.idestado = 3 THEN 1 ELSE 0 END), 0) AS estado_3
                                            FROM dependencias d
                                            LEFT JOIN movimiento m ON d.iddependencias = m.iddependencias_emior
                                            WHERE d.iddependencias NOT IN (1)
                                            GROUP BY d.iddependencias, d.nombre_dependencia;');
        $cantDepenEnv_est1 = array_map(fn($item) => $item->estado_1, $cantDocusDepenEnvEst);
        $cantDepenEnv_est2 = array_map(fn($item) => $item->estado_2, $cantDocusDepenEnvEst);
        $cantDepenEnv_est3 = array_map(fn($item) => $item->estado_3, $cantDocusDepenEnvEst);
        //cuantos docus a recibido cada depen y en cada estado estn
        $cantDocusDepenReciEst = DB::connection('mysql_documentario')->select('SELECT d.iddependencias,d.nombre_dependencia,
                                            COALESCE(SUM(CASE WHEN m.idestado = 1 THEN 1 ELSE 0 END), 0) AS estado_1,
                                            COALESCE(SUM(CASE WHEN m.idestado = 2 THEN 1 ELSE 0 END), 0) AS estado_2,
                                            COALESCE(SUM(CASE WHEN m.idestado = 3 THEN 1 ELSE 0 END), 0) AS estado_3
                                            FROM dependencias d
                                            LEFT JOIN movimiento m ON d.iddependencias = m.iddependencias_receptor
                                            WHERE d.iddependencias NOT IN (1)
                                            GROUP BY d.iddependencias, d.nombre_dependencia;');

        $cantDepenReci_est1 = array_map(fn($item) => $item->estado_1, $cantDocusDepenReciEst);
        $cantDepenReci_est2 = array_map(fn($item) => $item->estado_2, $cantDocusDepenReciEst);
        $cantDepenReci_est3 = array_map(fn($item) => $item->estado_3, $cantDocusDepenReciEst);

        //tiempo prome de atencion por dias de cada depen
        $tiemPromePorDepen = DB::connection('mysql_documentario')->select('SELECT
                                            d.iddependencias,
                                            d.nombre_dependencia,
                                            ROUND(COALESCE(AVG(
                                                CASE
                                                WHEN m.fecha_de_envio IS NOT NULL THEN
                                                    (
                                                    DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + 1
                                                    - (2 * FLOOR((DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + WEEKDAY(m.fecha_de_envio)) / 7))
                                                    - IF(WEEKDAY(m.fecha_de_envio) = 6, 1, 0)
                                                    - IF(WEEKDAY(COALESCE(m.fecha_finalizacion, CURRENT_DATE)) = 6, 1, 0)
                                                    )
                                                ELSE NULL
                                                END
                                            ), 0.0), 1) AS dias_promedio
                                            FROM dependencias d
                                            LEFT JOIN movimiento m ON d.iddependencias = m.iddependencias_receptor
                                            WHERE d.iddependencias NOT IN (1)
                                            GROUP BY d.iddependencias, d.nombre_dependencia
                                            ORDER BY d.iddependencias;');

        $dias_promeAten = array_map(fn($item) => floatval($item->dias_promedio), $tiemPromePorDepen);
        return view('mesaPartes.reporDepen', compact('rol', 'id_depen', 'cont_est', 'depenBarChar', 'cantBarChar', 'cantDepenEnv_est1', 'cantDepenEnv_est2', 'cantDepenEnv_est3', 'cantDepenReci_est1', 'cantDepenReci_est2', 'cantDepenReci_est3', 'dias_promeAten'));
    }

    //Tasa de cumplimiento de plazos (SLA) (Movimientos dentro del SLA / Total de movimientos) * 100
    public function mostrarTasaSla(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio', now()->subMonth()->toDateString());
        $fecha_fin = $request->input('fecha_fin', now()->toDateString());

        $reglas = [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ];

        $mensajes = [
            'fecha_inicio.required' => 'Ingrese fecha de inicio',
            'fecha_inicio.date' => 'Ingrese una fecha valida',
            'fecha_fin.required' => 'Ingrese fecha fin',
            'fecha_fin.date' => 'Ingrese una fecha valida',
            'fecha_fin.after_or_equal' => 'Ingresaste una fecha que es antes de inicio',
        ];

        $valida = $request->validate($reglas, $mensajes);

        $tasa_sla = DB::connection('mysql_documentario')->select('SELECT
                                d.iddependencias,
                                d.nombre_dependencia,

                                COUNT(CASE
                                        WHEN m.fecha_de_envio IS NOT NULL THEN 1
                                    END) AS total_movimientos,

                                COUNT(CASE
                                        WHEN m.fecha_de_envio IS NOT NULL AND (
                                            DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + 1
                                            - (2 * FLOOR((DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + WEEKDAY(m.fecha_de_envio)) / 7))
                                            - IF(WEEKDAY(m.fecha_de_envio) = 6, 1, 0)
                                            - IF(WEEKDAY(COALESCE(m.fecha_finalizacion, CURRENT_DATE)) = 6, 1, 0)
                                        ) <= 7 THEN 1
                                    END) AS movimientos_dentro_sla,

                                ROUND(
                                    IFNULL(
                                    COUNT(CASE
                                            WHEN m.fecha_de_envio IS NOT NULL AND (
                                                DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + 1
                                                - (2 * FLOOR((DATEDIFF(COALESCE(m.fecha_finalizacion, CURRENT_DATE), m.fecha_de_envio) + WEEKDAY(m.fecha_de_envio)) / 7))
                                                - IF(WEEKDAY(m.fecha_de_envio) = 6, 1, 0)
                                                - IF(WEEKDAY(COALESCE(m.fecha_finalizacion, CURRENT_DATE)) = 6, 1, 0)
                                            ) <= 7 THEN 1
                                        END)
                                    / NULLIF(COUNT(CASE WHEN m.fecha_de_envio IS NOT NULL THEN 1 END), 0) * 100,
                                    0
                                    ), 1
                                ) AS tasa_cumplimiento_sla

                                FROM dependencias d
                                LEFT JOIN movimiento m ON d.iddependencias = m.iddependencias_receptor
                                AND m.fecha_de_envio BETWEEN ? AND ?
                                WHERE d.iddependencias NOT IN (1)

                                GROUP BY d.iddependencias, d.nombre_dependencia
                                ORDER BY d.iddependencias;', [$request->fecha_inicio, $request->fecha_fin]);

        $nombre_dependencia = array_map(fn($item) => $item->nombre_dependencia, $tasa_sla);
        $total_movimientos = array_map(fn($item) => $item->total_movimientos, $tasa_sla);
        $movimientos_dentro_sla = array_map(fn($item) => $item->movimientos_dentro_sla, $tasa_sla);
        $tasa_cumplimiento_sla = array_map(fn($item) => $item->tasa_cumplimiento_sla, $tasa_sla);


        return response()->json([
            'status' => 'success',
            'nombre_dependencia' => $nombre_dependencia,
            'total_movimientos' => $total_movimientos,
            'movimientos_dentro_sla' => $movimientos_dentro_sla,
            'tasa_cumplimiento_sla' => $tasa_cumplimiento_sla
        ]);
        // return view('mesaPartes.reporDepen', compact('fecha_inicio'));
    }
}
