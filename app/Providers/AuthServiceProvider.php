<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        $roles = [
            'admin',
            'docente',
            'postulante',
            'alumno',
            'egresado',
            'admision',
        ];

        foreach ($roles as $rol) {
            Gate::define('rol-' . $rol, function ($user) use ($rol) {
                return session('active_role_name') === $rol;
            });
        }

        Gate::define('rol-documentario', function ($user) {

            $rolesDoc = [
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
            ];

            return in_array(session('active_role_name'), $rolesDoc);
        });
    }
}
