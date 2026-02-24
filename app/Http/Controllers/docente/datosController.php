<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class datosController extends Controller
{
    public function datos(){
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        $datos = DB::connection('mysql_segunda')->table('userprofile')
            ->where('id_users', '=', Auth::user()->id)->first();
        $dni = DB::table('users')->where('id', '=', Auth::user()->id)->pluck('dni')->first();

        return view('docente.datos.datos', compact('nom_usu', 'datos', 'dni'));
    }
}
