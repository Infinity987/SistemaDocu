<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class resultado extends Controller
{
    public function index(){
        $postulante = DB::select('SELECT * FROM postulante');
        return view('resultados.index')->with('postulante',$postulante );
    }

    public function primeranota(Request $request)
    {
        // Obtener los datos del array 'vacantes' del request
        $vacantes = $request->input('vacantes');
    
        // Insertar la fecha actual en la tabla pdf_primeranota
        $fechaActual = date('Y-m-d'); // Obtener la fecha actual sin hora
        $idPdfPrimerNota = DB::table('pdf_primeranota')->insertGetId([
            'fecha' => $fechaActual,
        ]);
    
        // Iterar sobre cada vacante y procesar solo la primera nota
        foreach ($vacantes as $vacante) {
            // Si 'nota1' no está definida o es null, asignarle el valor 0
            $nota1 = isset($vacante['nota1']) ? $vacante['nota1'] : 0;
    
            // Determinar los valores necesarios
            $asistencia = empty($nota1) ? 'No se presentó' : 'Se presentó';
            $estado_apro_desa = ($nota1 < 11) ? 'Desaprobó' : 'Aprobó';
    
            // Insertar o actualizar solo la primera nota en la tabla resultados
            DB::table('resultados')->updateOrInsert(
                // Condición para encontrar el registro existente
                ['idinscripcion' => $vacante['idincripcion']],
                // Datos para insertar o actualizar
                [
                    'asistencia' => $asistencia,
                    'nota1' => $nota1, // Se asegura que la nota sea al menos 0
                    'nota2' => null, // Nota2 no se registra
                    'nota3' => null, // Nota3 no se registra
                    'nota_total' => null, // No calculamos nota_total todavía
                    'estado_apro_desa' => $estado_apro_desa,
                    // Solo registramos el id_pdf_primeranota en la primera nota
                    'id_pdfprimeranota' => DB::raw('IFNULL(id_pdfprimeranota, '.$idPdfPrimerNota.')'),
                    'estado_ingreso' => null, // Opcional, si aplica
                    'orden_de_merito' => null // Opcional, si aplica
                ]
            );
        }
    
        // Redirigir con mensaje de éxito
        return redirect()->back()->with('success', 'SE REGISTRÓ LA PRIMERA NOTA CON ÉXITO.');
    }
    


public function segundayterceranota(Request $request)
{
    // Obtener los datos del array 'vacantes' del request
    $vacantes = $request->input('vacantes');

    // Iterar sobre cada vacante y procesar solo la segunda y tercera nota
    foreach ($vacantes as $vacante) {
        // Obtener las notas
        $nota1 = $vacante['nota1']; // Suponemos que esta ya existe
        $nota2 = $vacante['nota2'] ?? null; // Nota 2
        $nota3 = $vacante['nota3'] ?? null; // Nota 3

        // Calcular la nota total solo si se proporcionan las tres notas
        $nota_total = (!is_null($nota1) && !is_null($nota2) && !is_null($nota3)) 
            ? round(($nota1 + $nota2 + $nota3) / 3, 2) 
            : null;

      

        // Actualizar o insertar los resultados
        DB::table('resultados')->updateOrInsert(
            // Condición para encontrar el registro existente
            ['idinscripcion' => $vacante['idincripcion']],
            // Datos para insertar o actualizar
            [
                'nota2' => $nota2,
                'nota3' => $nota3,
                'nota_total' => $nota_total,
               
            ]
        );
    }

    return redirect()->back()->with('success', 'SE ACTUALIZARON LAS SEGUNDA Y TERCERA NOTAS CON ÉXITO.');
}


    
    
public function generaringresantes(Request $request) {
    // Consulta inicial para obtener los datos de resultados
    $resultados = DB::select('
        SELECT resultados.idinscripcion, resultados.nota_total, resultados.estado_ingreso, 
               resultados.orden_de_merito, vacantes.cantidad_vacantes, vacantes.idvacantes 
        FROM resultados 
        INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion 
        INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes 
        WHERE vacantes.idprocesos = ?
    ', [$request->idproceso]);

    // Convertir a colección y agrupar por idvacantes (vacante o carrera)
    $resultadosAgrupados = collect($resultados)->groupBy('idvacantes');

    // Insertar en la tabla pdf_ingresantes
    $idPdfIngresantes = DB::table('pdf_ingresantes')->insertGetId([
        'fecha' => now(), // Fecha actual
    ]);

    // Procesar cada grupo de vacantes por separado
    foreach ($resultadosAgrupados as $idVacante => $postulantes) {
        // Ordenar los postulantes por nota_total (descendente)
        $postulantes = $postulantes->sortByDesc('nota_total')->values();

        // Obtener la cantidad de vacantes disponibles para esta vacante
        $vacantesDisponibles = $postulantes[0]->cantidad_vacantes;
        $ordenMerito = 1;

        foreach ($postulantes as $postulante) {
            // Determinar si el postulante ingresa
            if ($vacantesDisponibles > 0) {
                $estadoIngreso = 'Alcanzó vacante';
                $vacantesDisponibles--; // Reducir las vacantes disponibles
            } else {
                $estadoIngreso = 'No Alcanzó Vacante';
            }

            // Actualizar el estado de ingreso, orden de mérito y id_pdfingresantes en la base de datos
            DB::table('resultados')
                ->where('idinscripcion', $postulante->idinscripcion)
                ->update([
                    'estado_ingreso' => $estadoIngreso,
                    'orden_de_merito' => $ordenMerito++, // Incrementar el orden de mérito dentro de esta vacante
                    'id_pdfingresantes' => $idPdfIngresantes, // Asignar el ID del PDF generado
                ]);
        }
    }

    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'SE GENERARON LOS INGRESANTES CON ÉXITO.');
}


public function indexingre(){
        $postulante = DB::select('SELECT * FROM postulante');
        return view('resultados.ingreindex')->with('postulante',$postulante );
    }

}
