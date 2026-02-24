<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    //protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // 🔹 Sobreescribimos el método para usar DNI en lugar de email
    public function username()
    {
        return 'dni';
    }

    // 🔹 Validación personalizada del login
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|exists:users,dni',
            'password' => 'required|string',
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.exists' => 'El DNI ingresado no está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('admin')) {
            // return redirect('postulantes/verpostulantes');
            return redirect('admin/users');
        }

        if ($user->hasRole('docente')) {
            // return redirect('/docente/index');
            return redirect('/docente/horario');
        }

        if ($user->hasRole('alumno')) {
            // return redirect('/alumno/index');
            return redirect('/alumno/matriReali');
        }

        if ($user->hasRole('postulante')) {
            return redirect('/postulante/index');
        }

        if ($user->hasRole('egresado')) {
            return redirect('/egresado/index');
        }

        if ($user->hasRole('admision')) {
            // return redirect('admin/users');
            return redirect('admin/postulantes/verpostulantes');
        }

        return redirect('/login'); // fallback por si no tiene rol
    }

    public function showLoginForm()
    {
        // Ejemplo: obtener nombre de institución desde tabla settings
        $institucion =  DB::table('procesos')->where('estado_proceso', 1)->value('estado_proceso');

        return view('auth.login', compact('institucion'));
    }
}
