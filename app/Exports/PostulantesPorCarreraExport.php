<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostulantesPorCarreraExport implements FromCollection, WithHeadings
{
    protected $idvacantes;

    public function __construct($idvacantes)
    {
        $this->idvacantes = $idvacantes;
    }

    public function collection()
    {
        return DB::table('inscripcion')
            ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->where('inscripcion.idvacantes', $this->idvacantes)
            ->select(
                'postulante.idpostulante',
                'postulante.nombres_postulante',
                'postulante.apellidos_pater_postulante',
                'postulante.apellidos_mater_postulante',
                'postulante.edad_postulante',
                'carreras.nombre_de_carrera'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Nombres',
            'Apellido Paterno',
            'Apellido Materno',
            'Edad',
            'Carrera',
        ];
    }
}
