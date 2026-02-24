<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class resultadoprimera extends Controller
{
    public function index(){
        $procesosresultadoprimera = DB::select('SELECT DISTINCT(pdf_primeranota.id_pdf_primeranota) AS idnota1, pdf_primeranota.fecha, procesos.idprocesos, procesos.nombre_proceso, modalidad.nombre_modalidad FROM `pdf_primeranota` INNER JOIN resultados ON pdf_primeranota.id_pdf_primeranota = resultados.id_pdfprimeranota INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN procesos ON inscripcion.proceso_distin = procesos.idprocesos INNER JOIN modalidad ON inscripcion.modalidad_distin = modalidad.idmodalidad;');
        return view('pdf_primeranota.index')->with('procesosresultadoprimera',$procesosresultadoprimera );
    }

    public function eliminarnota1(Request $request)
{
    $idPdfNota1 = $request->input('idnota1');

    if (!$idPdfNota1 || !is_numeric($idPdfNota1)) {
        return redirect()->back()->with('error', 'ID inválido. No se pudo eliminar la nota.');
    }

    DB::table('resultados')
        ->where('id_pdfprimeranota', $idPdfNota1)
        ->delete();

    DB::table('pdf_primeranota')
        ->where('id_pdf_primeranota', $idPdfNota1)
        ->delete();

    return redirect()->back()->with('success', 'Nota eliminada correctamente.');
}



public function exportExcel(Request $request)
{
    $id = $request->input('idnota1');

    $datos = DB::select('
    SELECT carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante,
           postulante.apellidos_mater_postulante, postulante.nombres_postulante,
           resultados.nota1_comu, resultados.nota1_mate, resultados.nota1_demo, resultados.nota1,
           resultados.estado_apro_desa, resultados.asistencia
    FROM resultados
    INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
    INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
    INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
    INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
    WHERE resultados.id_pdfprimeranota = ?
    ORDER BY carreras.idcarreras DESC, resultados.nota1 DESC
', [$id]);

   $collection = collect($datos)->map(function ($item) {
    return [
        'Carrera' => $item->nombre_de_carrera,
        'DNI' => $item->idpostulante,
        'Apellido Paterno' => $item->apellidos_pater_postulante,
        'Apellido Materno' => $item->apellidos_mater_postulante,
        'Nombres' => $item->nombres_postulante,
        'Comunicación' => $item->nota1_comu,
        'Matemática' => $item->nota1_mate,
        'Demografía' => $item->nota1_demo,
        'Promedio Fase 1' => $item->nota1,
        'Promedio Normalizado' => $item->asistencia === 'No se presentó' ? 'NSP' : round($item->nota1 / 2.5, 2),
        'Condición' => $item->asistencia === 'No se presentó'
            ? 'NSP'
            : ($item->estado_apro_desa === 'Aprobó'
                ? 'Aprobado (apto para entrevista)'
                : 'Desaprobado (No pasa a la segunda evaluación)')
    ];
});

    return Excel::download(new class($collection) implements FromCollection, WithHeadings {
        protected $data;
        public function __construct(Collection $data) { $this->data = $data; }
        public function collection() { return $this->data; }
        public function headings(): array {
    return [
        'Carrera', 'DNI', 'Apellido Paterno', 'Apellido Materno', 'Nombres',
        'Comunicación', 'Matemática', 'Demografía', 'Promedio Fase 1',
        'Promedio Normalizado', 'Condición'
    ];
}
    }, 'Reporte_Primera_Nota.xlsx');
}



}
