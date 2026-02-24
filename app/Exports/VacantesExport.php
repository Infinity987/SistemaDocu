<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VacantesExport implements FromCollection, WithHeadings
{
    protected $carreras;

    public function __construct($carreras)
    {
        $this->carreras = $carreras;
    }

    public function collection()
    {
        // Convertimos los objetos a colecciones simples
        return collect($this->carreras)->map(function ($c) {
            return [
                'Proceso'   => $c->nombre_proceso,
                'Modalidad' => $c->nombre_modalidad,
                'Carrera'   => $c->nombre_de_carrera,
                'Vacantes'  => $c->cantidad_vacantes,
            ];
        });
    }

    public function headings(): array
    {
        return ['Proceso', 'Modalidad', 'Carrera', 'Vacantes'];
    }
}