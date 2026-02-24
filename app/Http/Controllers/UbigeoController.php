<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar DB

class UbigeoController extends Controller
{
    public function getDepartamentos()
    {
        $departamentos = DB::table('ubigeo')
            ->select('departamento')
            ->distinct()
            ->get();

        return response()->json($departamentos);
    }

    public function getProvincias($departamento)
    {
        $provincias = DB::table('ubigeo')
            ->where('departamento', $departamento)
            ->select('provincia')
            ->distinct()
            ->get();

        return response()->json($provincias);
    }

    public function getDistritos($provincia)
    {
        $distritos = DB::table('ubigeo')
            ->where('provincia', $provincia)
            ->select('Ubigeo','distrito')
            ->distinct()
            ->get();

        return response()->json($distritos);
    }
}
