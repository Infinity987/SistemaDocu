<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
// 🚀 Librerías necesarias para el procesamiento del Word y fechas
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class resoluciones extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $id_depen = session('dependencia_id');
        
        $rol = DB::connection('mysql_documentario')
            ->table('dependencias')
            ->where('iddependencias', $id_depen)
            ->first();

        $cont_est = DB::connection('mysql_documentario')->select(
            'SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
             FROM estado
             LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
             AND movimiento.iddependencias_receptor = ?
             WHERE estado.idestado IN (1,2,3)
             GROUP BY estado.idestado;', [$id_depen]
        );

        // Retornamos tu vista de siempre
        return view('mesaPartes.resoluciones', compact('rol', 'id_depen', 'cont_est'));
    }

    // 🚀 NUEVO MÉTODO: Generar y descargar la resolución en Word
    public function generarWord($id_proyecto)
    {
        // 1. Obtener información del proyecto de investigación
        $proyecto = DB::connection('mysql_documentario')
            ->table('proyecto_investigacion as pi')
            ->leftJoin('tipo_tramite as tt', 'pi.tipo_tramite', '=', 'tt.id_tipo_tramite')
            ->select('pi.*', 'tt.nombre_tramite as nombre_tramite_texto')
            ->where('pi.id_proyecto', $id_proyecto)
            ->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'El proyecto no existe en el sistema.');
        }

        // 2. Obtener los alumnos vinculados
        $alumnosIds = DB::connection('mysql_documentario')->table('alumno_proyecto')
            ->where('id_proyecto', $id_proyecto)
            ->pluck('id_alumno')
            ->toArray();

        $nombresAlumnos = [];
        if (count($alumnosIds) > 0) {
            $nombresAlumnos = DB::connection('mysql')->table('postulante')
                ->select(DB::raw("CONCAT(apellidos_pater_postulante, ' ', apellidos_mater_postulante, ', ', nombres_postulante) as nombre_completo"))
                ->whereIn('idpostulante', $alumnosIds)
                ->pluck('nombre_completo')
                ->toArray();
        }
        $alumnosTexto = implode("\n", $nombresAlumnos);

        // 3. Obtener el asesor activo del proyecto (id_rol_proyecto = 1, estado_asignado = 1)
        $asesorAsignado = DB::connection('mysql_documentario')->table('autoridades_proyecto')
            ->where('id_proyecto', $id_proyecto)
            ->where('id_rol_proyecto', 1)
            ->where('estado_asignado', 1)
            ->first();

        $nombreAsesor = "No asignado";
        if ($asesorAsignado) {
            $perfilAsesor = DB::connection('mysql_segunda')->table('userprofile')
                ->where('id_users', $asesorAsignado->id_docente)
                ->first();
            $nombreAsesor = $perfilAsesor ? trim($perfilAsesor->nombre) : "No asignado";
        }

        // 4. Obtener la escuela profesional (Carrera)
        $carreraNombre = "No especificado";
        if ($proyecto->id_carrera) {
            $carreraNombre = DB::connection('mysql')->table('carreras')
                ->where('idcarreras', $proyecto->id_carrera)
                ->value('nombre_de_carrera');
        }

        // 5. Lógica transaccional para el correlativo cíclico anual
        $anioActual = Carbon::now()->year;
        $numeroCorrelativo = 1;
        $siglas = 'JUI/DG/EESPP “GBM”-CP';

        DB::connection('mysql_documentario')->transaction(function () use ($anioActual, &$numeroCorrelativo, &$siglas) {
            $control = DB::connection('mysql_documentario')->table('control_correlativos')
                ->where('anio', $anioActual)
                ->lockForUpdate()
                ->first();

            if ($control) {
                $numeroCorrelativo = $control->ultimo_numero + 1;
                $siglas = $control->siglas_resolucion;

                DB::connection('mysql_documentario')->table('control_correlativos')
                    ->where('id_control', $control->id_control)
                    ->update(['ultimo_numero' => $numeroCorrelativo]);
            } else {
                DB::connection('mysql_documentario')->table('control_correlativos')->insert([
                    'anio' => $anioActual,
                    'ultimo_numero' => 1,
                    'siglas_resolucion' => $siglas
                ]);
                $numeroCorrelativo = 1;
            }
        });

        $numeroCompleto = "{$numeroCorrelativo}-{$anioActual}-{$siglas}";

        // 6. Registrar la resolución en tu historial de base de datos
        $fechaEmision = Carbon::now();
        $fechaLarga = "Cerro de Pasco, " . $fechaEmision->translatedFormat('d \d\e F Y'); // Ej: Cerro de Pasco, 06 de mayo 2026
        
        DB::connection('mysql_documentario')->table('resoluciones')->insert([
            'id_proyecto' => $id_proyecto,
            'numero_correlativo' => $numeroCorrelativo,
            'anio' => $anioActual,
            'numero_completo' => $numeroCompleto,
            'fecha_emision' => $fechaEmision->toDateString(),
            'anio_oficial_texto' => 'Año de la Esperanza y el Fortalecimiento de la Democracia', // Ajustar según año[cite: 1]
            'numeros_expediente' => '1063, 1064, 1065', // Valores dinámicos sugeridos
            'fecha_expediente' => Carbon::now()->subMonths(2)->toDateString(),
            'cantidad_folios' => 'trece (13) folios útiles',
        ]);

        // 7. Procesar y rellenar la plantilla Word (.docx)
        $rutaPlantilla = storage_path('app/templates/plantilla_resolucion.docx');

        if (!file_exists($rutaPlantilla)) {
            return redirect()->back()->with('error', 'No se encontró el archivo plantilla_resolucion.docx en storage/app/templates/.');
        }

        $templateProcessor = new TemplateProcessor($rutaPlantilla);

        // Reemplazar etiquetas con los valores del sistema
        $templateProcessor->setValue('NUMERO_COMPLETO', $numeroCompleto);
        $templateProcessor->setValue('FECHA_LARGA', $fechaLarga);
        $templateProcessor->setValue('EXPEDIENTES', '1063, 1064, 1065'); 
        $templateProcessor->setValue('FECHA_EXPEDIENTE', '19 de marzo de 2026'); 
        $templateProcessor->setValue('CANTIDAD_FOLIOS', 'trece (13) folios útiles');
        $templateProcessor->setValue('TITULO_INVESTIGACION', $proyecto->titulo_investigacion);
        $templateProcessor->setValue('PROGRAMA_ESTUDIOS', $carreraNombre);
        
        // Formateo de saltos de línea para PHPWord en el listado de alumnos
        $alumnosFormateados = preg_replace('/\n/', '</w:t><w:br/><w:t>', $alumnosTexto);
        $templateProcessor->setValue('ALUMNOS', $alumnosFormateados);
        
        $templateProcessor->setValue('ASESOR', $nombreAsesor);

        // Generación y descarga del archivo temporal
        $nombreArchivoDescarga = "Resolucion_N_{$numeroCorrelativo}_{$anioActual}.docx";
        $rutaDestino = storage_path('app/public/resoluciones_generadas/' . $nombreArchivoDescarga);

        if (!file_exists(storage_path('app/public/resoluciones_generadas'))) {
            mkdir(storage_path('app/public/resoluciones_generadas'), 0755, true);
        }

        $templateProcessor->saveAs($rutaDestino);

        return response()->download($rutaDestino)->deleteFileAfterSend(true);
    }
}