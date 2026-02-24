<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class InscritosExport implements FromCollection, WithHeadings
{
    protected $idProceso;
    protected $idModalidad;

    public function __construct($idProceso, $idModalidad)
    {
        $this->idProceso = $idProceso;
        $this->idModalidad = $idModalidad;
    }

    public function collection()
    {
        return DB::table('inscripcion')
            ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->select(
                'carreras.nombre_de_carrera',
                'procesos.nombre_proceso',
                'modalidad.nombre_modalidad',
                DB::raw("LPAD(inscripcion.idpostulante, 8, '0') AS DNI"),
                DB::raw("CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) AS Nombre_Completo")
            )
            ->where('vacantes.idprocesos', $this->idProceso)
            ->where('vacantes.idmodalidad', $this->idModalidad)
            ->orderBy('carreras.nombre_de_carrera')
            ->orderBy('postulante.apellidos_pater_postulante')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Carrera',
            'Proceso',
            'Modalidad',
            'DNI',
            'Nombre Completo'
        ];
    }
}