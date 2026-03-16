<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ParagonIE\Sodium\Compat;

class searchDocu extends Controller
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
        return view('mesaPartes.searchDocu', compact('rol', 'id_depen', 'cont_est'));
    }

    public function search(Request $request)
    {
        $usuario = Auth::user();
        $id_depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();

        $id_usuTrabajador = $usuario->id;
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);

        $request->validate([
            'num_expe' => 'required|numeric'
        ]);

        $num_expe = $request->input('num_expe');

        $sql = <<<sql
            WITH RECURSIVE hilo AS (
                SELECT * FROM documentos WHERE iddocumentos = ?
                UNION ALL
                SELECT d.* FROM documentos d
                INNER JOIN hilo h ON d.iddocumento_referencia = h.iddocumentos
            )
                SELECT h.*,
                    m.idestado,
                    m.fecha_de_envio,
                    m.fecha_de_recepcion,
                    m.fecha_finalizacion,
                    de.nombre_dependencia as emisorr,
                    dr.nombre_dependencia as receptorr
                FROM hilo h
                LEFT JOIN movimiento m ON h.iddocumentos = m.iddocumentos
                LEFT JOIN dependencias de ON m.iddependencias_emior = de.iddependencias
                LEFT JOIN dependencias dr ON m.iddependencias_receptor = dr.iddependencias
                ORDER BY h.fecha_ingreso, m.fecha_de_envio;
        sql;

        $xdocumen = DB::connection('mysql_documentario')->select($sql, [$num_expe]);
        $documentos = collect(DB::connection('mysql_documentario')->select($sql, [$num_expe]));

        // dd($xdocumen);

        if (empty($xdocumen)) {
            return back()->with('error', 'No se encontro expediente.');
        }

        return view('mesaPartes.searchDocu', compact('rol', 'id_depen', 'cont_est', 'xdocumen', 'documentos'));
    }
}
