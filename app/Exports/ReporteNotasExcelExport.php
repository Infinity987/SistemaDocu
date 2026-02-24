<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteNotasExcelExport implements FromCollection, WithHeadings
{
    protected $idSemestre;

    public function __construct($idSemestre)
    {
        $this->idSemestre = $idSemestre;
    }

    public function collection()
    {
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
            ->leftJoin('licencia', 'matricula.idmatricula', '=', 'licencia.idmatricula')
            ->leftJoin('incripcion_curso', 'matricula.idmatricula', '=', 'incripcion_curso.idmatricula')
            ->leftJoin('docente_curso', 'incripcion_curso.id_docente_curso', '=', 'docente_curso.iddocente_curso')
            ->leftJoin('cursos', 'docente_curso.idcursos', '=', 'cursos.idcursos')
            ->selectRaw("
                tipo_matricula.nombre_tipo_matricula,
                '' AS resolucion_aprobacion,
                carreras.nombre_de_carrera,
                CONCAT(
                    'Resolución Viceministerial ',
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
                postulante.nombres_postulante,
                postulante.apellidos_pater_postulante,
                postulante.apellidos_mater_postulante,
                CASE WHEN licencia.resolucion_licencia IS NOT NULL THEN 'SÍ' ELSE 'NO' END AS tiene_licencia,
                licencia.resolucion_licencia,
                cursos.nombre_curso,
                cursos.credito,                
                CASE
                    WHEN incripcion_curso.total BETWEEN 1 AND 5 THEN 'PREVIO AL INICIO'
                    WHEN incripcion_curso.total BETWEEN 6 AND 8 THEN 'INICIO'
                    WHEN incripcion_curso.total BETWEEN 9 AND 13 THEN 'EN PROCESO'
                    WHEN incripcion_curso.total BETWEEN 14 AND 17 THEN 'LOGRADO'
                    WHEN incripcion_curso.total BETWEEN 18 AND 20 THEN 'DESTACADO'
                    ELSE ''
                END AS nivel_logro,
                incripcion_curso.total
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
            'PLAN_ESTUDIOS',
            'TURNO',
            'SECCION',
            'CICLO',
            'TIPO_DOCUMENTO_IDENTIDAD',
            'NUMERO_DOCUMENTO_IDENTIDAD',
            'NOMBRES',
            'APELLIDO_PATERNO',
            'APELLIDO_MATERNO',
            'CON_LICENCIA',
            'RESOLUCION_LICENCIA',
            'CURSO / MÓDULO',
            'CREDITOS',
            'NIVEL DE DESEMPEÑO ALCANZADO EN EL CURSO',
            'CALIFICACIÓN (Escala Vigesimal)'
        ];
    }
}