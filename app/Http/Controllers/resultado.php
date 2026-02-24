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
 
    // dd($request);
    // Obtener los datos del array 'vacantes' del request
    $vacantes = $request->input('vacantes');

    // dd($vacantes);

    // Insertar la fecha actual en la tabla pdf_primeranota
    $fechaActual = date('Y-m-d'); // Obtener la fecha actual sin hora
    $idPdfPrimerNota = DB::table('pdf_primeranota')->insertGetId([
        'fecha' => $fechaActual,
    ]);

    // Iterar sobre cada vacante y procesar solo la primera nota
    foreach ($vacantes as $vacante) {
        // Obtener la nota mínima aprobatoria desde la tabla 'procesos' usando el id de inscripción
        $notaMinima = DB::table('inscripcion')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->where('inscripcion.idinscripcion', $vacante['idincripcion'])
            ->value('procesos.nota_min_apro'); // Devuelve la nota mínima aprobatoria

        // Asignar nota mínima predeterminada si no se encuentra
        $notaMinima = $notaMinima ?? 11; // Nota mínima por defecto, en caso de ser nula

        // Obtener la nota original y calcular la nota dividida
        $nota1_1 = isset($vacante['nota1_1']) ? $vacante['nota1_1'] : 0;
        $nota1_2 = isset($vacante['nota1_2']) ? $vacante['nota1_2'] : 0;
        $nota1_3 = isset($vacante['nota1_3']) ? $vacante['nota1_3'] : 0;
        $notaOriginal= $nota1_1+ $nota1_2 + $nota1_3;
        $notaDividida = $notaOriginal / 2.5;

        // Determinar los valores necesarios
        $asistencia = empty($nota1_1) ? 'No se presentó' : 'Se presentó';
        $estado_apro_desa = ($notaDividida < $notaMinima) ? 'Desaprobó' : 'Aprobó';

        // Insertar o actualizar solo la primera nota en la tabla resultados
        DB::table('resultados')->updateOrInsert(
            // Condición para encontrar el registro existente
            ['idinscripcion' => $vacante['idincripcion']],
            // Datos para insertar o actualizar
            [
                'asistencia' => $asistencia,
                'nota1_mate' => $nota1_1, // nota de matematica
                'nota1_comu' => $nota1_2, //nota de comunicacion
                'nota1_demo' => $nota1_3, //nota de diversidad
                'nota2_cola' => null,
                'nota2_pensa' => null,
                'nota2_TI' => null,
                'nota1' => $notaOriginal, // Guardar nota original
                'nota2' => null,
                'nota_total' => null,
                'estado_apro_desa' => $estado_apro_desa,
                'id_pdfprimeranota' => DB::raw('IFNULL(id_pdfprimeranota, '.$idPdfPrimerNota.')'),
                'estado_ingreso' => null,
                'orden_de_merito' => null
            ]
        );
    }
    $nota1 = 1;
    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'SE REGISTRÓ LA PRIMERA NOTA CON ÉXITO.')->with(compact('nota1'));
}



public function segundayterceranota(Request $request)
{
    // Obtener los datos del array 'vacantes' del request
    $vacantes = $request->input('vacantes');

    // Iterar sobre cada vacante y procesar solo la segunda y tercera nota
    foreach ($vacantes as $vacante) {
        // Obtener las notas
        $nota1 = $vacante['nota1']; // Suponemos que esta ya existe
        $nota2_1 = $vacante['nota2_1'] ?? null; // Nota 2_1
        $nota2_2 = $vacante['nota2_2'] ?? null; // Nota 2_2
        $nota2_3 = $vacante['nota2_3'] ?? null; // Nota 2_3

        $nota2 = $nota2_1 + $nota2_2 + $nota2_3;


        // Calcular la nota total solo si se proporcionan las tres notas
        $nota_total = (!is_null($nota1) && !is_null($nota2))
            ? round(($nota1 + $nota2)/5,2)
            : null;


        // Actualizar o insertar los resultados
        DB::table('resultados')->updateOrInsert(
            // Condición para encontrar el registro existente
            ['idinscripcion' => $vacante['idincripcion']],
            // Datos para insertar o actualizar
            [
                'nota2_cola' => $nota2_1, //
                'nota2_pensa'=> $nota2_2,
                'nota2_TI' =>  $nota2_3,
                'nota2' => $nota2,
                'nota_total' => $nota_total,

            ]
        );
    }
    $nota23 = 23;
    return redirect()->back()->with('success', 'SE ACTUALIZARON LAS SEGUNDA Y TERCERA NOTAS CON ÉXITO.')->with(compact('nota23'));
}




public function generaringresantes(Request $request) {
    // Consulta inicial para obtener los datos de resultados filtrados por estado_apro_desa = 'Aprobó'
    $resultados = DB::select('
        SELECT resultados.idinscripcion, resultados.nota_total, resultados.estado_ingreso,
               resultados.orden_de_merito, vacantes.cantidad_vacantes, vacantes.idvacantes,
               resultados.estado_apro_desa
        FROM resultados
        INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
        INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
        WHERE vacantes.idprocesos = ? AND resultados.estado_apro_desa = ?
    ', [$request->idproceso, 'Aprobó']);



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
            // Obtener la nota mínima del proceso de este postulante
            $notaMinima = DB::table('inscripcion')
                ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
                ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
                ->where('inscripcion.idinscripcion', $postulante->idinscripcion)
                ->value('procesos.nota_min_apro');

            // Si no hay una nota mínima registrada, usar 11 por defecto
            $notaMinima = $notaMinima ?? 11;

            // Determinar si el postulante alcanza la vacante
           if ($vacantesDisponibles > 0 && $postulante->nota_total >= $notaMinima) {
    $estadoIngreso = 'Alcanzó vacante';
    $vacantesDisponibles--;

    

    // Obtener el idcarreras desde la tabla vacantes
    $idCarrera = DB::table('inscripcion')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->where('inscripcion.idinscripcion', $postulante->idinscripcion)
        ->value('vacantes.idcarreras');

    // Buscar la malla curricular más reciente en mysql_segunda
    $idMallaCurricular = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->where('carrera_malla', $idCarrera)
        ->orderByDesc('año_de_inicio')
        ->value('idmalla_curricular');

    // Obtener el idpostulante usando la inscripción
    $idPostulante = DB::table('inscripcion')
        ->where('idinscripcion', $postulante->idinscripcion)
        ->value('idpostulante');

        // Buscar el DNI del postulante
$dni = DB::table('postulante')
    ->where('idpostulante', $idPostulante)
    ->value('idpostulante');

// Buscar el ID del usuario en la tabla users
$idUser = DB::connection('mysql')
    ->table('users')
    ->where('dni', $dni)
    ->value('id');


// Actualizar el rol del usuario si existe
if ($idUser) {
    DB::table('model_has_roles')
        ->where('model_id', $idUser)
        ->update([
            'role_id' => 4,
            'model_type' => 'App\\Models\\User', // Asegúrate que este namespace sea el correcto
        ]);
}

    // Actualizar el campo id_malla en la tabla postulante
    if ($idPostulante && $idMallaCurricular) {
        DB::table('postulante')
            ->where('idpostulante', $idPostulante)
            ->update(['id_malla' => $idMallaCurricular]);
    }
} else {
    $estadoIngreso = 'No Alcanzó Vacante';
}

            // Actualizar el estado de ingreso, orden de mérito y PDF
            DB::table('resultados')
                ->where('idinscripcion', $postulante->idinscripcion)
                ->update([
                    'estado_ingreso' => $estadoIngreso,
                    'orden_de_merito' => $ordenMerito++,
                    'id_pdfingresantes' => $idPdfIngresantes,
                ]);
        }

    }

    // Redirigir con mensaje de éxito
    $notagen = 33;
    return redirect()->back()->with('success', 'SE GENERARON LOS INGRESANTES CON ÉXITO.')->with('notagen', $notagen);
}

public function indexingre(){
        $postulante = DB::select('SELECT * FROM postulante');
        return view('resultados.ingreindex')->with('postulante',$postulante );
    }

}
