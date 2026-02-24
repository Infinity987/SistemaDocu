<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class reportes extends Controller
{
    public function index(){
        $postulante = DB::select('SELECT * FROM postulante');
        return view('reportes.index')->with('postulante',$postulante );
    }
}
