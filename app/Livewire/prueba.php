<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Obtener el ID del usuario autenticado
$userId = Auth::id();

class prueba extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener el DNI del usuario autenticado
        $userIddni = $user->dni_usuario;

        // Realizar la consulta en la base de datos
        $existeDni = DB::table('postulante')->where('idpostulante', $userIddni)->exists();

        // Pasar los datos a la vista
        $mensaje = $existeDni ? 'DNI en postulantes' : 'DNI no encontrado';
        $mostrarFormulario = !$existeDni;

        return view('prueba.index', compact('mensaje', 'mostrarFormulario'));
    }
  
}

