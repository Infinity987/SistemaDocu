<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\Datatables;

class crearDocu extends Controller
{
    public function creardocu()
    {
        try {
            $usuario = Auth::user()->dni;
            $id_depen = session('dependencia_id');
            // dd($id_depen);
            $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();
            $id_usuTrabajador = Auth::user()->id;

            $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);
            $dependencias = DB::connection('mysql_documentario')->table('dependencias')->get();
            $detalle_tramite = DB::connection('mysql_documentario')->table('detalle_tramite')->get();
            return view('docente.documentario.indexCrear', compact('dependencias', 'detalle_tramite', 'id_depen', 'rol', 'id_usuTrabajador', 'cont_est'));
        } catch (\Throwable $th) {
            return view('docente.documentario.indexCrear', compact('dependencias'));
        }
    }

    public function emitidos($idtipo_docu, $emisor)
    {
        $id_user = Auth::user()->id;
        $query_emitidos = DB::connection('mysql_documentario')->select('SELECT
            documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto,
            documentos.folio, movi_est.id_estado
            FROM documentos
            INNER JOIN (SELECT movimiento.iddocumentos as movi_iddocu, MAX(idestado) AS id_estado
            FROM `movimiento` GROUP BY movi_iddocu ) AS movi_est ON documentos.iddocumentos = movi_est.movi_iddocu
            WHERE idtipo_documento = ? AND emisor = ? AND est_firma = ? AND id_user = ?
            GROUP BY documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto, documentos.folio, movi_est.id_estado ORDER BY documentos.iddocumentos DESC;', [
            $idtipo_docu,
            $emisor,
            1,
            $id_user
        ]);

        if (request()->ajax()) {
            return datatables::of($query_emitidos)->addColumn('btn', 'mesaPartes.butons')->rawColumns(['btn'])->toJson();
        }
    }
}
