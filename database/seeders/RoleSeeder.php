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

        $role7 = Role::create(['name' => 'Dirección']);
        $role8 = Role::create(['name' => 'Jefatura de unidad Académica']);
        $role9 = Role::create(['name' => 'Jefatura de unidad Administrativa']);
        $role10 = Role::create(['name' => 'Secretaria Académica']);
        $role11 = Role::create(['name' => 'Coordin. Prog. Estudios Educ. Inicial']);
        $role12 = Role::create(['name' => 'Coordin. Prog. Estudios Primaria Epib']);
        $role13 = Role::create(['name' => 'Coordin. Prog. Estudios Educ. Física']);
        $role14 = Role::create(['name' => 'Coordin. Prog. Educac. Secundaria']);
        $role15 = Role::create(['name' => 'J. Area Acad. Educ. Básica Regular']);
        $role16 = Role::create(['name' => 'Jefe de Unidad de Formación Contínua']);
        $role17 = Role::create(['name' => 'J. Unidad de bienestar y empleabilidad']);
        $role18 = Role::create(['name' => 'J. Unidad de Investigación']);
        $role19 = Role::create(['name' => 'J. Area de Calidad']);
        $role20 = Role::create(['name' => 'Coord. del área de Práctica Profesional e investigación']);
        $role21 = Role::create(['name' => 'Biblioteca']);
        $role22 = Role::create(['name' => 'Y/O Cargos']);
        $role23 = Role::create(['name' => 'PPD']);
        $role24 = Role::create(['name' => 'Mesa de partes']);


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

        //para tramite doc
        // Permission::create(['name' => 'admin.home'])->syncRoles([$role1, $role2, $role3, $role4, $role5, $role6, $role7, $role8, $role9, $role10, $role11,$role12, $role13, $role14, $role15, $role16, $role17, $role18, $role19]);

        // Permission::create(['name' => 'admin.users.index'])->syncRoles([$role1]);
        // Permission::create(['name' => 'admin.users.edit'])->syncRoles([$role1]);
        // Permission::create(['name' => 'admin.users.update'])->syncRoles([$role1]);

        Permission::create(['name' => 'documentario.admin.registerUser'])->syncRoles([$role1]);
        Permission::create(['name' => 'documentario.mesapar.index'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.num_tipo_documento_expe'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.buscarUsuario'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.registrarDocu'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.mesapar.emitidos'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.mesapar.showEmitido'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.mesapar.updateDocuEmi'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);

        Permission::create(['name' => 'documentario.mesapar.bandeja'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.bandeja.bandejaEstado'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);
        Permission::create(['name' => 'documentario.bandeja.recibir'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);

        Permission::create(['name' => 'documentario.reporDepen.index'])->syncRoles([$role7]);

        Permission::create(['name' => 'documentario.pagos.index'])->syncRoles([$role7, $role8, $role9, $role10, $role11, $role12, $role13, $role14, $role15, $role16, $role17,$role18, $role19, $role20, $role21, $role22, $role23, $role24]);

    }
}
