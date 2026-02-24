<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\select;

class asignarCurso extends Controller
{
    public function index()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('admin.asignarCurso.index', compact('nom_usu'));
    }

    public function buscarDocente(Request $request)
    {
        $busqueda = $request->get('q');

        $query = DB::connection('mysql_segunda')->table('docente')
            ->join('userprofile', 'docente.id_users', '=', 'userprofile.id_users');

        if (!empty($busqueda)) {
            $query->where('nombre', 'like', "%{$busqueda}%");
        }
        $usu =$query->limit(10)
            ->get(['docente.iddocente as iddocen', 'userprofile.nombre as nombre']);


        return response()->json([
            'results' => $usu,
        ], 200);
    }
}
