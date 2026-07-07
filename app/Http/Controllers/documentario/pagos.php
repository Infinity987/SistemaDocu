<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ParagonIE\Sodium\Compat;

class pagos extends Controller
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
        return view('mesaPartes.pagos', compact('rol', 'id_depen', 'cont_est'));
    }
   
}
