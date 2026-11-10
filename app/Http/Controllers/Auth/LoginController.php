<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Rules\RecaptchaV3;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    //protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'dni';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|exists:users,dni',
            'password' => 'required|string',
            // 'g-recaptcha-response' => ['required', new RecaptchaV3],
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
        // 1. Filtramos el rol 'personal_base' porque es un rol de permisos, no de navegación.
        // Esto evita que el usuario vaya al "selector de roles" solo por tener este rol extra.
        $rolesNavegacion = $user->roles->filter(function ($role) {
            return $role->name !== 'personal_base';
        });

        // 2. Si tiene más de un rol de navegación (ej. es Docente Y Dirección), debe elegir
        if ($rolesNavegacion->count() > 1) {
            return redirect()->route('selector.roles');
        }

        // 3. Si tiene exactamente un rol de navegación
        if ($rolesNavegacion->count() === 1) {
            $role = $rolesNavegacion->first();

            // Guardamos en sesión el ID del rol como dependencia_id y su nombre
            session([
                'dependencia_id' => $role->id,
                'active_role_name' => $role->name
            ]);

            // 4. Redirección inteligente y automática
            return match ($role->name) {
                'admin'      => redirect('admin/users'),
                'docente'    => redirect('/docente/horario'),
                'alumno'     => redirect('/alumno/matriReali'),
                'postulante' => redirect()->route('postulante.index'),
                'egresado'   => redirect('/alumno/matriPorCurri'),
                'admision'   => redirect('admin/procesos'),

                default => redirect()->route('documentario.mesapar.index'),
            };
        }

        // Fallback por si acaso (ej. usuario sin roles, aunque no debería pasar)
        return redirect()->intended($this->redirectPath());
    }

    public function showLoginForm()
    {
        // Ejemplo: obtener nombre de institución desde tabla settings
        $institucion =  DB::table('procesos')->where('estado_proceso', 1)->value('estado_proceso');

        return view('auth.login', compact('institucion'));
    }
}
