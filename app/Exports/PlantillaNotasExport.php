<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantillaNotasExport implements FromCollection, WithHeadings
{
    protected $semestreId;
    protected $ciclo;
    protected $mallaId;
    protected $tipoMatricula;
    protected $cursos;
    protected $semestreNombre;
    protected $tipoNombre;

    public function __construct($semestreId, $ciclo, $mallaId, $tipoMatricula)
    {
        $this->semestreId = $semestreId;
        $this->ciclo = $ciclo;
        $this->mallaId = $mallaId;
        $this->tipoMatricula = $tipoMatricula;

        // Obtener nombre del semestre
        $sem = DB::connection('mysql_segunda')
            ->table('semestre_academico')
            ->where('idsemestre_academico', $semestreId)
            ->first();

        $this->semestreNombre = $sem ? "{$sem->año} - {$sem->periodo}" : "Desconocido";

        // Nombre del tipo de matrícula
        $this->tipoNombre = $tipoMatricula == 1 ? 'Regular' : ($tipoMatricula == 2 ? 'Subsanación' : 'Desconocido');

        // Cursos del ciclo
        $this->cursos = DB::connection('mysql_segunda')
            ->table('plan_de_estudio as plan')
            ->join('cursos as curso', 'plan.idcursos', '=', 'curso.idcursos')
            ->where('plan.malla_curricular_idmalla_curricular', $this->mallaId)
            ->where('plan.idciclos', $this->ciclo)
            ->select('curso.idcursos', 'curso.nombre_curso')
            ->orderBy('curso.nombre_curso')
            ->get();
    }

    public function headings(): array
    {
        $base = ['idsemestre_academico', 'tipo_matricula', 'DNI', 'Apellidos y Nombres'];

        foreach ($this->cursos as $curso) {
            $base[] = $curso->nombre_curso . ' (Nota)';
            $base[] = $curso->nombre_curso . ' (Estado: Aprobado/Desaprobado)';
        }

        return $base;
    }

   public function collection()
{
    $leyenda = collect([
        "Semestre: {$this->semestreNombre} (ID: {$this->semestreId}) | Tipo de matrícula: {$this->tipoNombre}"
    ]);

    $encabezados = collect($this->headings());

    $ejemplo = collect([
        $this->semestreId,
        $this->tipoMatricula,
        '71688500',
        'López Anaya Jhonatan'
    ]);

    foreach ($this->cursos as $index => $curso) {
        $ejemplo[] = $index % 2 == 0 ? 18 : 14; // alterna notas
        $ejemplo[] = $index % 2 == 0 ? 'Aprobado' : 'Desaprobado';
    }

    return new Collection([
        $leyenda,
        $encabezados,
        $ejemplo
    ]);
}
}