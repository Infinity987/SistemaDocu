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

    // 🔹 Sobreescribimos el método para usar DNI en lugar de email
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
        // dd("aquui");
        $roles = $user->roles;

        if ($roles->count() > 1) {
            // Redirigir a la pantalla de selección
            return redirect()->route('selector.roles');
        }

        if ($roles->count() === 1) {
            // Solo tiene un rol, lo asignamos de inmediato
            // dd("aquui");
            $role = $roles->first();
            session([
                'dependencia_id' => $role->id,
                'active_role_name' => $role->name
            ]);

            return match ($role->name) {
                'admin'      => redirect('admin/users'),
                'docente'    => redirect('/docente/horario'),
                'alumno'     => redirect('/alumno/matriReali'),
                'postulante' => redirect()->route('postulante.index'),
                'egresado'   => redirect('/alumno/matriPorCurri'),
                'admision'   => redirect('admin/procesos'),

                'Dirección',
                'Jefatura de unidad Académica',
                'Jefatura de unidad Administrativa',
                'Secretaria Académica',
                'Coordin. Prog. Estudios Educ. Inicial',
                'Coordin. Prog. Estudios Primaria Epib',
                'Coordin. Prog. Estudios Educ. Física',
                'Coordin. Prog. Educac. Secundaria',
                'J. Area Acad. Educ. Básica Regular',
                'Jefe de Unidad de Formación Contínua',
                'J. Unidad de bienestar y empleabilidad',
                'J. Unidad de Investigación',
                'J. Area de Calidad',
                'Coord. del área de Práctica Profesional e investigación',
                'Biblioteca',
                'Y/O Cargos',
                'PPD',
                'Mesa de partes',
                'Asistente J. Unidad de Investigación',
                'Asistente Jefatura de unidad Administrativa',
                'Logística',
                // ... puedes seguir agregando los nombres exactos aquí
                => redirect()->route('documentario.mesapar.index'),

                default => redirect()->route('documentario.mesapar.index'),
            };
        }

        return redirect()->intended($this->redirectPath());
        // $roles = $user->roles;
        // $roleIds = $user->roles->pluck('id')->toArray();
        // dd($roleIds);

        // if ($user->hasRole('admin')) {
        //     session(['dependencia_id' => 1]);
        //     return redirect('admin/users');
        // } else if ($user->hasRole('docente')) {
        //     return redirect('/docente/horario');
        // } else if ($user->hasRole('alumno')) {
        //     return redirect('/alumno/matriReali');
        // } else if ($user->hasRole('postulante')) {
        //     return redirect('/postulante/index');
        // } else if ($user->hasRole('egresado')) {
        //     return redirect('/egresado/index');
        // } else if ($user->hasRole('admision')) {
        //     return redirect('admin/postulantes/verpostulantes');
        // } else if ($user->hasRole('Dirección')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Jefatura de unidad Académica')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Jefatura de unidad Administrativa')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Secretaria Académica')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Coordin. Prog. Estudios Educ. Inicial')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Coordin. Prog. Estudios Primaria Epib')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Coordin. Prog. Estudios Educ. Física')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Coordin. Prog. Educac. Secundaria')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('J. Area Acad. Educ. Básica Regular')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Jefe de Unidad de Formación Contínua')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('J. Unidad de bienestar y empleabilidad')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('J. Unidad de Investigación')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('J. Area de Calidad')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Coord. del área de Práctica Profesional e investigación')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Biblioteca')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Y/O Cargos')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('PPD')) {
        //     return redirect('/Docu');
        // } else if ($user->hasRole('Mesa de partes')) {
        //     // return redirect('documentario/Docu');
        //     session(['dependencia_id' => 24]);

        //     return redirect()->route('documentario.mesapar.index',);
        // }

        // return redirect('/login'); // fallback por si no tiene rol
    }

    public function showLoginForm()
    {
        // Ejemplo: obtener nombre de institución desde tabla settings
        $institucion =  DB::table('procesos')->where('estado_proceso', 1)->value('estado_proceso');

        return view('auth.login', compact('institucion'));
    }
}
