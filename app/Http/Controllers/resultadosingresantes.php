<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;


class resultadosingresantes extends Controller
{
    public function index(){
        $procesosresultadoprimera = DB::select('SELECT DISTINCT(pdf_ingresantes.id_pdfingresantes) AS idpdfingresantes, pdf_ingresantes.fecha, procesos.idprocesos, procesos.nombre_proceso, modalidad.nombre_modalidad FROM pdf_ingresantes INNER JOIN resultados ON pdf_ingresantes.id_pdfingresantes = resultados.id_pdfingresantes INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN procesos ON inscripcion.proceso_distin = procesos.idprocesos INNER JOIN modalidad ON inscripcion.modalidad_distin = modalidad.idmodalidad;');
        return view('pdf_segundanota.index')->with('procesosresultadoprimera',$procesosresultadoprimera );
    }

   public function eliminaringresantes(Request $request)
{
    $id = $request->input('idpdfingresantes');

    if (!$id || !is_numeric($id)) {
        return back()->with('error', 'ID inválido. No se pudo eliminar la nota.');
    }

    // Obtener todos los idinscripcion afectados
    $inscripciones = DB::table('resultados')
        ->where('id_pdfingresantes', $id)
        ->pluck('idinscripcion');

    foreach ($inscripciones as $idInscripcion) {
        // Obtener el idpostulante
        $idPostulante = DB::table('inscripcion')
            ->where('idinscripcion', $idInscripcion)
            ->value('idpostulante');

        // Eliminar la malla curricular asignada
        DB::table('postulante')
            ->where('idpostulante', $idPostulante)
            ->update(['id_malla' => null]);

        // Obtener el DNI del postulante
        $dni = DB::table('postulante')
            ->where('idpostulante', $idPostulante)
            ->value('idpostulante');

        // Buscar el usuario por DNI
        $idUser = DB::table('users')
            ->where('dni', $dni)
            ->value('id');

        // Restaurar el rol si existe
        if ($idUser) {
            DB::table('model_has_roles')
                ->where('model_id', $idUser)
                ->update([
                    'role_id' => 3, // Estado anterior al ingreso
                    'model_type' => 'App\\Models\\User',
                ]);
        }
    }

    // Eliminar resultados y PDF
    DB::table('resultados')
        ->where('id_pdfingresantes', $id)
        ->delete();

    DB::table('pdf_ingresantes')
        ->where('id_pdfingresantes', $id)
        ->delete();

    return back()->with('success', 'Notas y asignaciones revertidas correctamente.');
}

public function fichaingresantesExcel(Request $request)
{
    $idpdf = $request->idpdfingresa;

    $datos = DB::select('
    SELECT carreras.nombre_de_carrera, postulante.idpostulante,
           postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante,
           postulante.nombres_postulante, resultados.nota1_comu, resultados.nota1_mate,
           resultados.nota1_demo, resultados.nota1, resultados.nota2_cola,
           resultados.nota2_pensa, resultados.nota2_TI, resultados.nota2,
           resultados.nota_total, resultados.estado_ingreso, resultados.orden_de_merito
    FROM resultados
    INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
    INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
    INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
    INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
    WHERE resultados.id_pdfingresantes = ?
    ORDER BY carreras.idcarreras DESC, resultados.nota_total DESC, resultados.orden_de_merito ASC
', [$idpdf]);

    $collection = collect($datos)->map(function ($item) {
    return [
        'Carrera' => $item->nombre_de_carrera,
        'DNI' => $item->idpostulante,
        'Apellido Paterno' => $item->apellidos_pater_postulante,
        'Apellido Materno' => $item->apellidos_mater_postulante,
        'Nombres' => $item->nombres_postulante,
        'Nota 1 - Comunicación' => $item->nota1_comu,
        'Nota 1 - Matemática' => $item->nota1_mate,
        'Nota 1 - Demografía' => $item->nota1_demo,
        'Promedio Fase 1' => $item->nota1,
        'Nota 2 - Colaboración' => $item->nota2_cola,
        'Nota 2 - Pensamiento Crítico' => $item->nota2_pensa,
        'Nota 2 - TI' => $item->nota2_TI,
        'Promedio Fase 2' => $item->nota2,
        'Nota Total' => $item->nota_total,
        'Estado de Ingreso' => $item->estado_ingreso,
        'Orden de Mérito' => $item->orden_de_merito,
    ];
});

    return Excel::download(new class($collection) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
        protected $collection;

        public function __construct(Collection $collection)
        {
            $this->collection = $collection;
        }

        public function collection()
        {
            return $this->collection;
        }

        public function headings(): array
{
    return [
        'Carrera', 'DNI', 'Apellido Paterno', 'Apellido Materno', 'Nombres',
        'Nota 1 - Comunicación', 'Nota 1 - Matemática', 'Nota 1 - Demografía', 'Promedio Fase 1',
        'Nota 2 - Colaboración', 'Nota 2 - Pensamiento Crítico', 'Nota 2 - TI', 'Promedio Fase 2',
        'Nota Total', 'Estado de Ingreso', 'Orden de Mérito'
    ];
}
    }, 'Ingresantes_' . $idpdf . '.xlsx');
}

}
