<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteMatriculaGeneralExport implements FromCollection, WithHeadings
{
    protected $idSemestre;

    public function __construct($idSemestre)
    {
        $this->idSemestre = $idSemestre;
    }

    public function collection()
{
    // ⚙️ Cambia aquí si el nombre real de la BD "gamnielb_admision" es diferente
    $db_admision = 'gamnielb_admision';

    $resultados = DB::connection('mysql_segunda')
        ->table('matricula')
        ->leftJoin('malla_curricular', 'matricula.idmalla', '=', 'malla_curricular.idmalla_curricular')
        ->leftJoin("{$db_admision}.carreras", 'malla_curricular.carrera_malla', '=', 'carreras.idcarreras')
        ->leftJoin('tipo_matricula', 'matricula.id_reporte_matricula', '=', 'tipo_matricula.idtipo_matricula')
        ->leftJoin('seccion', 'matricula.idseccion', '=', 'seccion.idseccion')
        ->leftJoin('turno', 'matricula.id_turno', '=', 'turno.idturno')
        ->leftJoin('ciclos', 'matricula.ciclo_matricula', '=', 'ciclos.idciclos')
        ->leftJoin("{$db_admision}.postulante", 'matricula.id_alumno', '=', 'postulante.idpostulante')
        ->leftJoin("{$db_admision}.tipo_documento_postu", 'postulante.tipodocumento', '=', 'tipo_documento_postu.id_tipo_documento')
        ->leftJoin("{$db_admision}.sexo", 'postulante.genero_postulante', '=', 'sexo.idsexo')
        ->leftJoin("{$db_admision}.lenguas", 'postulante.lengua_mater', '=', 'lenguas.id_lengua')
        ->leftJoin("{$db_admision}.identidad_etnica", 'postulante.id_identidad_etnica', '=', 'identidad_etnica.id')
        ->leftJoin('licencia', 'matricula.idmatricula', '=', 'licencia.idmatricula')
        ->leftJoin("{$db_admision}.discapacidad", 'postulante.tipo_discapacidad', '=', 'discapacidad.id_discapacidad')
        ->leftJoin("{$db_admision}.est_civil", 'postulante.id_est_civil', '=', 'est_civil.id')
        ->leftJoin("{$db_admision}.modalidad_beca", 'postulante.id_moda_beca', '=', 'modalidad_beca.id')
        ->leftJoin("{$db_admision}.trabajo", 'postulante.tipo_trabajo', '=', 'trabajo.id_trabajo')
        ->leftJoin("{$db_admision}.estudios_previos", 'postulante.id_estu_previos', '=', 'estudios_previos.id')
        ->selectRaw("
            tipo_matricula.nombre_tipo_matricula,
            '' AS resolucion_aprobacion,
            carreras.nombre_de_carrera,
            CONCAT('Resolución Viceministerial ', 
                REPLACE(
                    SUBSTRING_INDEX(SUBSTRING_INDEX(malla_curricular.nombre_malla_curricular, '(', -1), ')', 1),
                    '(', ''
                )
            ) AS resolucion_viceministerial,
            turno.nombre_turno,
            seccion.nom_seccion,
            CONCAT('CICLO ', ciclos.nombre_ciclo) AS ciclo_formateado,
            tipo_documento_postu.nombre AS tipo_documento,
            postulante.idpostulante,
            postulante.nacionalidad,
            postulante.nombres_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante,
            sexo.nombre AS sexo_nombre,
            postulante.fecha_de_nacimiento_postu,
            postulante.idubigeo_nacimiento,
            lenguas.nombre AS lengua_materna,
            identidad_etnica.name AS identidad_etnica,
            postulante.idubigeo_domicilio,
            postulante.direccion_domicilio,
            CASE WHEN licencia.resolucion_licencia IS NOT NULL THEN 'SÍ' ELSE 'NO' END AS tiene_licencia,
            licencia.resolucion_licencia,
            CASE postulante.discapacidad WHEN 1 THEN 'SÍ' ELSE 'NO' END AS discapacidad,
            discapacidad.nombre_discapacidad,
            postulante.num_conadis,
            est_civil.est_civil,
            CASE postulante.rebred WHEN 1 THEN 'SÍ' ELSE 'NO' END AS rebred,
            postulante.num_reso_rebred,
            CASE postulante.con_beca WHEN 1 THEN 'SÍ' ELSE 'NO' END AS con_beca,
            modalidad_beca.modalidad_beca,
            postulante.reso_beca,
            CASE postulante.con_hijos WHEN 1 THEN 'SÍ' ELSE 'NO' END AS con_hijos,
            postulante.cant_hijos,
             CASE postulante.con_proyecto WHEN 1 THEN 'SÍ' ELSE 'NO' END AS con_proyecto,
            postulante.nom_proyecto,
            CASE postulante.con_trabajo WHEN 1 THEN 'SÍ' ELSE 'NO' END AS con_trabajo,
            trabajo.nombre_trabajo,
            CASE postulante.con_estudios WHEN 1 THEN 'SÍ' ELSE 'NO' END AS con_estudios,
            estudios_previos.nom_estuprevios
        ")
        ->where('matricula.idsemestre_academico', $this->idSemestre)
        ->orderBy('matricula.idmatricula', 'asc')
        ->get();

    return $resultados->map(fn($item) => (array) $item);
}

    public function headings(): array
    {
        return [
            'TIPO_MATRICULA',
            'RESOLUCION_APROBACIÓN',
            'PROGRAMA DE ESTUDIOS / CARRERA PROFESIONAL',
            'PLAN DE ESTUDIOS',
            'TURNO',
            'SECCION',
            'CICLO',
            'TIPO_DOCUMENTO_IDENTIDAD',
            'NUMERO_DOCUMENTO_IDENTIDAD',
            'NACIONALIDAD',
            'NOMBRES',
            'APELLIDO_PATERNO',
            'APELLIDO_MATERNO',
            'SEXO',
            'FECHA_NACIMIENTO',
            'UBIGEO_NACIMIENTO',
            'LENGUA_MATERNA',
            'AUTOIDENTIFACION_ETNICA',
            'UBIGEO_DOMICILIO',
            'DIRECCION_DOMICILIO',
            'CON_LICENCIA',
            'RESOLUCION_LICENCIA',
            'DISCAPACIDAD',
            'TIPO_DISCAPACIDAD',
            'NUMERO_CONADIS',
            'ESTADO_CIVIL',
            'BENEFICIARIO_REBRED',
            'NUMERO_RESOLUCION_REBRED',
            'CON_BECA',
            'MODALIDAD_BECA',
            'RESOLUCION_BECA',
            'CON_HIJOS',
            'CANTIDAD_HIJOS',
            'PROY_INV_APROBADO',
            'DENOMINACION_PROY_INV',
            'CON_TRABAJO',
            'TIPO_TRABAJO',
            'CON_ESTUDIOS_PREVIOS',
            'ESTUDIOS_PREVIOS_EN'
        ];
    }
}