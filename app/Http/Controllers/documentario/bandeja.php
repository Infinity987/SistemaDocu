<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\noEditarDocumento;

class bandeja extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $id_depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::connection('mysql_documentario')->select('SELECT * FROM dependencias');
        $detalledocumento = DB::connection('mysql_documentario')->select('SELECT * FROM detalle_tramite');

        $id_usuTrabajador = $usuario->id;
        $cont_fecha = DB::connection('mysql_documentario')->select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
                                                  SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
                                                  SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo FROM movimiento
                                                  WHERE iddependencias_receptor = ? AND idestado= 1', [$depen]);

        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depen]);
        return view('mesaPartes.recibidos', compact('depen', 'usuario', 'rol', 'cont_est', 'id_depen', 'dependencias', 'detalledocumento', 'cont_fecha'));
    }

    public function bandejaEstado($idtipo_estado, $emisor)
    {
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();

        // $primera_query = 'SELECT
        //             movimiento.idmovimiento AS movimient,movimiento.iddocumentos AS iddocument,iddependencias_emior,dependencias.nombre_dependencia AS nom_emiso,
        //             iddependencias_receptor,fecha_de_envio,fecha_de_recepcion,documentos.numero_de_exp AS num_exp,documentos.asunto AS asunt,
        //             documentos.folio AS foli,documentos.idtipo_documento AS tipo_docu,documentos.iddetalle_tramite AS deta_trami,
        //             detalle_tramite.nombre_detalle_tramite AS nom_deta_trami,documentos.recomendacion AS recomend,
        //             movimiento.idestado AS estado,documenpdf.nombre_del_documento AS nombrepdf, up.nombre AS nombre_doce
        //         FROM movimiento
        //         INNER JOIN documentos ON movimiento.iddocumentos = documentos.iddocumentos
        //         LEFT JOIN documenpdf ON documentos.iddocumentos = documenpdf.iddocumentos
        //         LEFT JOIN dependencias ON movimiento.iddependencias_emior = dependencias.iddependencias
        //         LEFT JOIN detalle_tramite ON documentos.iddetalle_tramite = detalle_tramite.iddetalle_tramite
        //         left JOIN gamnielb_sia.userprofile up ON documentos.id_user = up.id_users
        //         WHERE iddependencias_receptor = ? AND idestado = ?
        //         ORDER BY movimiento.fecha_de_envio DESC';

        if ($idtipo_estado == 1) {
            $query = DB::connection('mysql_documentario')->select('
                SELECT
                    u.dni,
                    movimiento.idmovimiento AS movimient,
                    movimiento.iddocumentos AS iddocument,
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

                    -- Si encuentra el nombre en userprofile lo usa, si no, concatena los campos de postulante
                    COALESCE(
                        up.nombre,
                        CONCAT_WS(" ", pos.nombres_postulante, pos.apellidos_pater_postulante, pos.apellidos_mater_postulante)
                    ) AS nombre_doce

                FROM movimiento
                INNER JOIN documentos ON movimiento.iddocumentos = documentos.iddocumentos
                LEFT JOIN documenpdf ON documentos.iddocumentos = documenpdf.iddocumentos
                LEFT JOIN dependencias ON movimiento.iddependencias_emior = dependencias.iddependencias
                LEFT JOIN detalle_tramite ON documentos.iddetalle_tramite = detalle_tramite.iddetalle_tramite

                -- Camino 1: Docentes y Administrativos (SIA)
                LEFT JOIN gamnielb_sia.userprofile up ON documentos.id_user = up.id_users

                -- Camino 2: Postulantes (Admisión)
                LEFT JOIN gamnielb_admision.users u ON documentos.id_user = u.id
                LEFT JOIN gamnielb_admision.postulante pos ON u.dni = pos.idpostulante

                WHERE iddependencias_receptor = ? AND idestado = ?
                ORDER BY movimiento.fecha_de_envio DESC
            ', [$emisor, $idtipo_estado]);
        } else {
            $query = DB::connection('mysql_documentario')->select('
                SELECT
                    u.dni,
                    movimiento.idmovimiento AS movimient,
                    movimiento.iddocumentos AS iddocument,
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

                    -- Si encuentra el nombre en userprofile lo usa, si no, concatena los campos de postulante
                    COALESCE(
                        up.nombre,
                        CONCAT_WS(" ", pos.nombres_postulante, pos.apellidos_pater_postulante, pos.apellidos_mater_postulante)
                    ) AS nombre_doce

                FROM movimiento
                INNER JOIN documentos ON movimiento.iddocumentos = documentos.iddocumentos
                LEFT JOIN documenpdf ON documentos.iddocumentos = documenpdf.iddocumentos
                LEFT JOIN dependencias ON movimiento.iddependencias_emior = dependencias.iddependencias
                LEFT JOIN detalle_tramite ON documentos.iddetalle_tramite = detalle_tramite.iddetalle_tramite

                -- Camino 1: Docentes y Administrativos (SIA)
                LEFT JOIN gamnielb_sia.userprofile up ON documentos.id_user = up.id_users

                -- Camino 2: Postulantes (Admisión)
                LEFT JOIN gamnielb_admision.users u ON documentos.id_user = u.id
                LEFT JOIN gamnielb_admision.postulante pos ON u.dni = pos.idpostulante

                WHERE iddependencias_receptor = ? AND idestado = ?
                ORDER BY movimiento.fecha_de_recepcion DESC
            ', [$emisor, $idtipo_estado]);
        }


        if (request()->ajax()) {
            return datatables::of($query)->addColumn('btn', 'mesaPartes.butons_bandeja')->rawColumns(['btn'])->toJson();
        }
    }

    public function bandejaEstado_upda($idtipo_estado, $iddocument, $movimient, $iddependencias_emior)
    {
        $fechaHoraActual = Carbon::now();
        try {
            DB::beginTransaction();
            $query = DB::connection('mysql_documentario')->update('UPDATE movimiento SET idestado = ?, fecha_de_recepcion = ? WHERE iddocumentos = ? AND idmovimiento = ?', [$idtipo_estado, $fechaHoraActual, $iddocument, $movimient]);
            $query2 = DB::connection('mysql_documentario')->update('UPDATE documentos SET estado_actu = ? WHERE iddocumentos = ?', [$idtipo_estado, $iddocument]);
            DB::commit();
            // dd($dependencia_id.' - cjiwncwecni');

            //para ver si es personal o dependencia
            if ($iddependencias_emior == 2 || $iddependencias_emior == 4 || $iddependencias_emior == 5) {
                $tipo = 'personal';
                $usu = DB::connection('mysql_documentario')->table('documentos')
                    ->where('iddocumentos', $iddocument)
                    ->value('id_user');
                $iddependencias_emior = $usu;
                // dd($usu);
            } else {
                $tipo = 'dependencia';
            }

            // dd($tipo.' '.$iddependencias_emior);

            event(new noEditarDocumento($iddependencias_emior, $tipo));
            return redirect()->back()->with('success', 'Documento recibido...-');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error al recibir documento');
        }
    }
}
