<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class malla_curri extends Controller
{
    public function index()
    {

        $mallas = DB::connection('mysql_segunda')->select('SELECT idmalla_curricular, nombre_malla_curricular, año_de_inicio FROM malla_curricular');


        return view('matricula.malla_curri.index')->with('mallas', $mallas);
    }

    public function archivocsv(Request $request)
    {
        // 1. Validar que se haya subido un archivo válido (CSV o TXT, máx 2MB)
        $request->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csvFile');
        $data = [];

        // 2. Leer el archivo CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = [];

            // Leer cada fila del archivo
            while (($row = fgetcsv($handle, 1000, ";")) !== false) {
                // La primera fila son los encabezados (cabeceras)
                if (empty($headers)) {
                    $headers = array_map('trim', $row);
                    continue;
                }

                // Convertir caracteres especiales (ej. ñ, á, etc.)
                // y limpiar los valores
                $row = array_map(fn($val) => mb_convert_encoding(trim($val), 'UTF-8', 'Windows-1252'), $row);

                // Combinar encabezados con valores de la fila
                $data[] = array_combine($headers, $row);
            }

            fclose($handle); // Cerrar el archivo
        }

        // 3. Agrupar datos por carrera (cada malla es por carrera)
        $agrupadoPorCarrera = collect($data)->groupBy('id_carrera');

        // Mapa para relacionar cada carrera con el ID generado de su malla
        $mallasMap = [];

        foreach ($agrupadoPorCarrera as $idCarrera => $grupo) {
            $first = $grupo->first(); // Primera fila del grupo

            // 4. Insertar una malla curricular por carrera
            $idMalla = DB::connection('mysql_segunda')->table('malla_curricular')->insertGetId([
                'nombre_malla_curricular' => $first['nombre_malla'],
                'año_de_inicio'           => $first['anio_malla'],
                'carrera_malla'           => $idCarrera,
                'creditos_total'          => $grupo->sum(fn($item) => is_numeric($item['credito']) ? (float)$item['credito'] : 0),
                'horal_total'             => $grupo->sum(fn($item) => is_numeric($item['hora']) ? (float)$item['hora'] : 0),
            ]);

            // Guardar el id de la malla generada por carrera
            $mallasMap[$idCarrera] = $idMalla;
        }

        // 5. Insertar cursos y planes de estudio por cada fila del CSV
        foreach ($data as $fila) {
            $nombreCurso = $fila['curso'];
            $credito = is_numeric($fila['credito']) ? (int)$fila['credito'] : 0;
            $horas = is_numeric($fila['hora']) ? (int)$fila['hora'] : 0;
            $tipo = $fila['tipo'];
            $idCarrera = $fila['id_carrera'];
            $idCiclo = $fila['ciclo'];
            $formacion = $fila['formacion'] ?? null;
            $numCompetencias = is_numeric($fila['numero_competencias']) ? (int)$fila['numero_competencias'] : 0;
            // 6. Verificar si el curso ya existe
            $curso = DB::connection('mysql_segunda')->table('cursos')->where('nombre_curso', $nombreCurso)->first();

            // insertar curso
            $idCurso = DB::connection('mysql_segunda')->table('cursos')->insertGetId([
                'nombre_curso' => $nombreCurso,
                'credito' => $credito,
                'codigo_curso' => null,
                'horas' => $horas,
                'idtipo_curso' => $tipo,
                'Formacion'         => $formacion,          
    'num_competencias'  => $numCompetencias      

            ]);

            // 7. Relacionar el curso con su malla en plan_de_estudio
            $idMalla = $mallasMap[$idCarrera] ?? null;

            if ($idMalla && $idCurso) {
                DB::connection('mysql_segunda')->table('plan_de_estudio')->insert([
                    'malla_curricular_idmalla_curricular' => $idMalla,
                    'idcursos' => $idCurso,
                    'idciclos' => $idCiclo,
                     'obligatorio' => 1 // ✅ valor fijo por defecto

                ]);
            }
        }

        // 8. Redireccionar con mensaje de éxito
        return back()->with('success', '¡Malla curricular, cursos y plan de estudio importados correctamente!');
    }

    public function cursosmodi(Request $request)
    {
        try {
            // Actualizar el curso
            DB::connection('mysql_segunda')->table('cursos')
                ->where('idcursos', $request->idcurso)
                ->update([
                    'nombre_curso' => $request->nombrecurso,
                    'horas' => $request->horacurso,
                    'credito' => $request->creditocurso,

                ]);

            return back()->with('success', 'Curso actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar curso: ' . $e->getMessage());
        }
    }
    public function eliminar(Request $request)
    {
        $idMalla = $request->input('id_plan');

        DB::connection('mysql_segunda')->beginTransaction();

        try {
            // 1. Obtener los cursos relacionados con el plan de estudio de esta malla
            $cursosIds = DB::connection('mysql_segunda')
                ->table('plan_de_estudio')
                ->where('malla_curricular_idmalla_curricular', $idMalla)
                ->pluck('idcursos')
                ->toArray();

            // 2. Eliminar plan de estudio relacionado con la malla
            DB::connection('mysql_segunda')
                ->table('plan_de_estudio')
                ->where('malla_curricular_idmalla_curricular', $idMalla)
                ->delete();

            // 3. Eliminar la malla curricular
            DB::connection('mysql_segunda')
                ->table('malla_curricular')
                ->where('idmalla_curricular', $idMalla)
                ->delete();

            // 4. Verificar si esos cursos están en otro plan de estudio
            foreach ($cursosIds as $idCurso) {
                $usadoEnOtroPlan = DB::connection('mysql_segunda')
                    ->table('plan_de_estudio')
                    ->where('idcursos', $idCurso)
                    ->exists();

                if (!$usadoEnOtroPlan) {
                    // Eliminar curso si ya no se usa en ningún plan
                    DB::connection('mysql_segunda')
                        ->table('cursos')
                        ->where('idcursos', $idCurso)
                        ->delete();
                }
            }

            DB::connection('mysql_segunda')->commit();

            return back()->with('success', 'Plan de estudio, malla curricular y cursos asociados eliminados correctamente.');
        } catch (\Exception $e) {
            DB::connection('mysql_segunda')->rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }


public function subircompete(Request $request)
{
    $idMalla = $request->input('id_plan');
    $file = $request->file('csvFile');

    try {
        if (($handle = fopen($file, 'r')) !== false) {
            stream_filter_append($handle, 'convert.iconv.Windows-1252/UTF-8'); // O usa ISO-8859-1 si esto falla

            $header = fgetcsv($handle, 1000, ';');
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]); // Quitar BOM
            Log::info('Encabezados CSV:', $header);

            $rowNumber = 1;

            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                Log::info("Fila {$rowNumber}:", $data);

                if (count($data) < 3 || !is_string($data[0])) {
                    Log::warning("Fila {$rowNumber} inválida o incompleta. Datos:", $data);
                    $rowNumber++;
                    continue;
                }

                [$dominioNombre, $competencia, $descripcion] = array_map('trim', array_pad($data, 3, null));

                if (empty($dominioNombre) || empty($competencia) || empty($descripcion)) {
                    Log::warning("Fila {$rowNumber} con campos vacíos", compact('dominioNombre', 'competencia', 'descripcion'));
                    $rowNumber++;
                    continue;
                }

                // Buscar dominio (evitando errores de collation)
                $dominio = DB::connection('mysql_segunda')->table('dominio_competencia')
                    ->whereRaw("CONVERT(Nombre_dominio USING utf8mb4) COLLATE utf8mb4_unicode_ci = ?", [$dominioNombre])
                    ->first();

                $dominioId = $dominio->iddominio_competencia ?? null;

                // Insertar dominio si no existe
                if (!$dominioId) {
                    $dominioId = DB::connection('mysql_segunda')->table('dominio_competencia')->insertGetId([
                        'Nombre_dominio' => $dominioNombre
                    ]);
                    Log::info("Nuevo dominio insertado", ['Nombre_dominio' => $dominioNombre, 'iddominio_competencia' => $dominioId]);
                }

                // Insertar competencia
                DB::connection('mysql_segunda')->table('competencias')->insert([
                    'iddominio_competencia' => $dominioId,
                    'competencia' => $competencia,
                    'descripcion' => $descripcion, // Asegúrate que la columna sea suficientemente larga
                    'idmalla_curricular' => $idMalla
                ]);

                Log::info("Competencia insertada en fila {$rowNumber}", compact('competencia', 'descripcion', 'idMalla'));

                $rowNumber++;
            }

            fclose($handle);
        }
    } catch (\Exception $e) {
        Log::error("Error al procesar archivo CSV: " . $e->getMessage());
        return back()->with('error', 'Ocurrió un error al procesar el archivo CSV.');
    }

    return back()->with('success', 'Competencias cargadas correctamente.');
}


}
