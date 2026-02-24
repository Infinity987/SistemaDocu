<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'docente']);
        $role3 = Role::create(['name' => 'postulante']);
        $role4 = Role::create(['name' => 'alumno']);
        $role5 = Role::create(['name' => 'egresado']);
        $role6 = Role::create(['name' => 'admision']);


        //ADMIN
        Permission::create(['name' => 'admin.home'])->syncRoles([$role1]);

        Permission::create(['name' => 'admin.users.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.users.edit'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.users.update'])->syncRoles([$role1]);

        Permission::create(['name' => 'admin.verpostulantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.agregar'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.editar'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.indexvacantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.agregarvacantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'procesos.editarvacantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'inscripcion.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'inscripcion.agregarinscripcion'])->syncRoles([$role6]);
        Permission::create(['name' => 'resultado.index'])->syncRoles([$role6]);

        Permission::create(['name' => 'resultado.primeranota'])->syncRoles([$role6]);
        Permission::create(['name' => 'resultado.segundayterceranota'])->syncRoles([$role6]);
        Permission::create(['name' => 'resultado.generaringresantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'resultadoprimera.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'resultadoingresantes.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'prueba.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'pdf.fichainscritos'])->syncRoles([$role6]);
        Permission::create(['name' => 'pdf.fichaprimeranota'])->syncRoles([$role6]);
        Permission::create(['name' => 'pdf.fichaingresantes'])->syncRoles([$role6]);
        Permission::create(['name' => 'padron.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'constancia.index'])->syncRoles([$role6]);
        Permission::create(['name' => 'Reportes.index'])->syncRoles([$role6]);

        Permission::create(['name' => 'malla.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'matricula_proceso.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'historialalumno.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'asignarCurso.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'horario.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'semestre.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'actaEvalu.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'notageneral.index'])->syncRoles([$role1]);

        //POSTULANTE
        Permission::create(['name' => 'postulante.index'])->syncRoles([$role3, $role4, $role5]);

        //ALUMNO
        Permission::create(['name' => 'alumno.matricula.index'])->syncRoles([$role4, $role5]); //
        Permission::create(['name' => 'alumno.matriActual.index'])->syncRoles([$role4]);
        Permission::create(['name' => 'alumno.matriPorCurri.index'])->syncRoles([$role4, $role5]);
        Permission::create(['name' => 'alumno.matriReali.index'])->syncRoles([$role4, $role5]);

        //EGRESADO
        Permission::create(['name' => 'egresado.index'])->syncRoles([$role5]);

        //DOCENT
        Permission::create(['name' => 'docente.index'])->syncRoles([$role2]);
        Permission::create(['name' => 'docente.horario'])->syncRoles([$role2]);
        Permission::create(['name' => 'docente.calificaciones'])->syncRoles([$role2]);

        //////agregar roles desde aqui //////////////////////////////////////
        Permission::create(['name' => 'encargados.index'])->syncRoles([$role1]);
        Permission::create(['name' => 'verDetalle.postulante'])->syncRoles([$role4, $role6, $role1]);
        Permission::create(['name' => 'ingre.index'])->syncRoles([$role6]);

        // Permission::create(['name' => 'admin.verpostulantes'])->syncRoles([$role4]);

    }
}
