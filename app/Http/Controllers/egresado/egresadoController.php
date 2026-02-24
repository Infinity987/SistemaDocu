<?php

namespace App\Http\Controllers\egresado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class egresadoController extends Controller
{
    public function index()
    {
        return view('egresado.index');
    }
}
