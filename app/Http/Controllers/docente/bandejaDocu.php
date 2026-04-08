<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Events\noEditarDocumento;


class bandejaDocu extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $id_depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::connection('mysql_documentario')->select('SELECT * FROM dependencias');
        $detalledocumento = DB::connection('mysql_documentario')->select('SELECT * FROM detalle_tramite');

        $id_usuTrabajador =  Auth::user()->id;
        $cont_fecha = DB::connection('mysql_documentario')->select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
                        SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
                        SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo FROM movimiento
                        WHERE id_user_receptor = ? AND idestado= 1', [$id_usuTrabajador]);

        $cont_est_doce_band = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_usuTrabajador]);
        // dd($cont_est);
        return view('docente.documentario.recibidos', compact('depen', 'usuario', 'rol', 'cont_est_doce_band', 'id_depen', 'dependencias', 'detalledocumento', 'cont_fecha'));
    }

    public function bandejaList($idtipo_estado, $emisor)
    {
        $id_users = Auth::user()->id;
        $depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();

        if ($idtipo_estado == 1) {
            $query = DB::connection('mysql_documentario')->select('
                SELECT
                    movimiento.idmovimiento AS movimient,movimiento.iddocumentos AS iddocument,iddependencias_emior,dependencias.nombre_dependencia AS nom_emiso,
                    iddependencias_receptor,fecha_de_envio,fecha_de_recepcion,documentos.numero_de_exp AS num_exp,documentos.asunto AS asunt,
                    documentos.folio AS foli,documentos.idtipo_documento AS tipo_docu,documentos.iddetalle_tramite AS deta_trami,
                    detalle_tramite.nombre_detalle_tramite AS nom_deta_trami,documentos.recomendacion AS recomend,
                    movimiento.idestado AS estado,documenpdf.nombre_del_documento AS nombrepdf, up.nombre AS nombre_doce
                FROM movimiento
                INNER JOIN documentos ON movimiento.iddocumentos = documentos.iddocumentos
                LEFT JOIN documenpdf ON documentos.iddocumentos = documenpdf.iddocumentos
                LEFT JOIN dependencias ON movimiento.iddependencias_emior = dependencias.iddependencias
                LEFT JOIN detalle_tramite ON documentos.iddetalle_tramite = detalle_tramite.iddetalle_tramite
                INNER JOIN gamnielb_sia.userprofile up ON documentos.id_user = up.id_users
                WHERE id_user_receptor = ? AND idestado = ?
                ORDER BY movimiento.fecha_de_envio DESC
            ', [$id_users, $idtipo_estado]);
        } else {
            $query = DB::connection('mysql_documentario')->select('
                SELECT
                    movimiento.idmovimiento AS movimient,movimiento.iddocumentos AS iddocument,
                    iddependencias_emior,
                    dependencias.nombre_dependencia AS nom_emiso,
                    iddependencias_receptor,
                    fecha_de_envio,
                    fecha_de_recepcion,
                    documentos.numero_de_exp AS num_exp,
                    documentos.asunto AS asunt,
                    documentos.folio AS foli,
                    documentos.idtipo_documento AS tipo_docu,
                    documentos.iddetalle_tramite AS deta_trami,
                    detalle_tramite.nombre_detalle_tramite AS nom_deta_trami,
                    documentos.recomendacion AS recomend,
                    movimiento.idestado AS estado,
                    documenpdf.nombre_del_documento AS nombrepdf,
                    up.nombre AS nombre_doce
                FROM movimiento
                INNER JOIN documentos ON movimiento.iddocumentos = documentos.iddocumentos
                LEFT JOIN documenpdf ON documentos.iddocumentos = documenpdf.iddocumentos
                LEFT JOIN dependencias ON movimiento.iddependencias_emior = dependencias.iddependencias
                LEFT JOIN detalle_tramite ON documentos.iddetalle_tramite = detalle_tramite.iddetalle_tramite
                INNER JOIN gamnielb_sia.userprofile up ON documentos.id_user = up.id_users
                WHERE id_user_receptor = ? AND idestado = ?
                ORDER BY movimiento.fecha_de_recepcion DESC
            ', [$id_users, $idtipo_estado]);
        }


        if (request()->ajax()) {
            return datatables::of($query)->addColumn('btn', 'docente.documentario.butons_bandeja')->rawColumns(['btn'])->toJson();
        }
    }

    public function bandejaRecepcionar($idtipo_estado, $iddocument, $movimient, $iddependencias_emior)
    {
        $fechaHoraActual = Carbon::now();
        try {
            DB::beginTransaction();
            $query = DB::connection('mysql_documentario')->update('UPDATE movimiento SET idestado = ?, fecha_de_recepcion = ? WHERE iddocumentos = ? AND idmovimiento = ?', [$idtipo_estado, $fechaHoraActual, $iddocument, $movimient]);
            $query2 = DB::connection('mysql_documentario')->update('UPDATE documentos SET estado_actu = ? WHERE iddocumentos = ?', [$idtipo_estado, $iddocument]);
            DB::commit();

            //para ver si es personal o dependencia
            if ($iddependencias_emior == 2 || $iddependencias_emior == 4 || $iddependencias_emior == 5) {
                $tipo = 'personal';
                $usu = DB::connection('mysql_documentario')->table('documentos')
                    ->where('iddocumentos', $iddocument)
                    ->value('id_user');
                $iddependencias_emior = $usu;
            } else {
                $tipo = 'dependencia';
            }

            event(new noEditarDocumento($iddependencias_emior, $tipo));
            return redirect()->back()->with('success', 'Documento recibido');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error al recibir documento');
        }
    }

    public function responderDocumentoDoce($iddocument)
    {
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::connection('mysql_documentario')->select('SELECT * FROM dependencias');
        $detalledocumento = DB::connection('mysql_documentario')->select('SELECT * FROM detalle_tramite');

        // Traer el documento específico
        $documento = DB::connection('mysql_documentario')->table('documentos')->where('iddocumentos', $iddocument)->first();

        // Conteos (igual que en index/solucionar)
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depen]);

        $cont_fecha = DB::connection('mysql_documentario')->select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
                    SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
                    SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo
            FROM movimiento
            WHERE iddependencias_receptor = ? AND idestado= 1', [$depen]);

        $id_depen = session('dependencia_id');

        return view('docente.documentario.responderdocumento', compact(
            'usuario',
            'depen',
            'id_depen',
            'rol',
            'dependencias',
            'detalledocumento',
            'documento',
            'cont_est',
            'cont_fecha'
        ));
    }
}
