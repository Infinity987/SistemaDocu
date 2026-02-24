<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResultadosHistoricosExport implements FromCollection, WithHeadings
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        return collect($this->datos);
    }

    public function headings(): array
    {
        return [
            'idinscripcion',
            'nombres',
            'carrera',
            'nota1_mate',
            'nota1_comu',
            'nota1_demo',
            'nota1',
            'nota2_cola',
            'nota2_pensa',
            'nota2_TI',
            'nota2',
            'nota_total',
            'estado_apro_desa',
            'estado_ingreso',
            'orden_de_merito',
            'id_pdfprimeranota',
            'id_pdfingresantes',
        ];
    }
}