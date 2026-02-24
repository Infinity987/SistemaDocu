<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlantillaNotasExport;
use Illuminate\Support\Facades\Log;





use function Laravel\Prompts\select;

class matricula_proceso extends Controller
{
   public function index()
    {

        $mallas = DB::connection('mysql_segunda')->select('SELECT idmalla_curricular, nombre_malla_curricular, año_de_inicio FROM malla_curricular');
        $semestres = DB::connection('mysql_segunda')->select('SELECT idsemestre_academico,periodo FROM semestre_academico');
        $carrera = DB::connection('mysql')->select('SELECT idcarreras,nombre_de_carrera FROM carreras');

        return view('matricula.matricula_proceso.index')->with('mallas', $mallas)->with('carrera', $carrera)->with('semestres', $semestres);
    }

  



public function semestrenotaspdf(Request $request)
{
    // 1. Obtener postulantes según malla seleccionada
    $postulantes = DB::connection('mysql')->select("
        SELECT idpostulante
        FROM postulante
        WHERE id_malla = ?
    ", [$request->id_malla]);

    $postulanteIds = collect($postulantes)->pluck('idpostulante')->unique()->toArray();

    // 2. Obtener cursos inscritos
    $alumnoscurso = DB::connection('mysql_segunda')->select("
    SELECT DISTINCT 
        cursos.idcursos, 
        cursos.nombre_curso, 
        incripcion_curso.credito,
        userprofile.nombre AS nombre_docente,
        userprofile.num_celualr AS celular_docente,
        userprofile.correo AS correo_docente,
        docente.especialidad AS especialidad_docente
    FROM matricula
    INNER JOIN incripcion_curso ON incripcion_curso.idmatricula = matricula.idmatricula
    INNER JOIN docente_curso ON incripcion_curso.id_docente_curso = docente_curso.iddocente_curso
    INNER JOIN cursos ON docente_curso.idcursos = cursos.idcursos
    INNER JOIN docente ON docente_curso.id_docente = docente.iddocente
    INNER JOIN userprofile ON docente.id_users = userprofile.id_users
    WHERE matricula.id_alumno IN (" . implode(',', $postulanteIds) . ")
      AND matricula.idsemestre_academico = ?
      AND matricula.ciclo_matricula = ?
      AND matricula.idtipo_matricula = ?
      AND matricula.id_turno = ?
", [
    $request->id_semestre,
    $request->id_ciclo,
    $request->id_tipo_matricula,
    $request->id_turno
]);

    // 3. Obtener información global del PDF
   $datospdfglobal = DB::connection('mysql_segunda')->select("
    SELECT semestre_academico.periodo,
            semestre_academico.año,
           malla_curricular.nombre_malla_curricular,
           turno.nombre_turno,
           ciclos.nombre_ciclo,
           COUNT(DISTINCT matricula.id_alumno) AS cantidad_alumnos
    FROM matricula
    INNER JOIN semestre_academico ON matricula.idsemestre_academico = semestre_academico.idsemestre_academico
    INNER JOIN incripcion_curso ON incripcion_curso.idmatricula = matricula.idmatricula
    INNER JOIN docente_curso ON incripcion_curso.id_docente_curso = docente_curso.iddocente_curso
    INNER JOIN cursos ON docente_curso.idcursos = cursos.idcursos
    INNER JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos
    INNER JOIN malla_curricular ON plan_de_estudio.malla_curricular_idmalla_curricular = malla_curricular.idmalla_curricular
    INNER JOIN turno ON matricula.id_turno = turno.idturno
    INNER JOIN ciclos ON plan_de_estudio.idciclos = ciclos.idciclos
    WHERE matricula.id_alumno IN (" . implode(',', $postulanteIds) . ")
      AND matricula.idsemestre_academico = ?
      AND matricula.ciclo_matricula = ?
      AND matricula.idtipo_matricula = ?
      AND matricula.id_turno = ?
    GROUP BY semestre_academico.periodo,
    semestre_academico.año,
             malla_curricular.nombre_malla_curricular,
             turno.nombre_turno,
             ciclos.nombre_ciclo
", [
    $request->id_semestre,
    $request->id_ciclo,
    $request->id_tipo_matricula,
    $request->id_turno
]);

    // 4. Obtener notas
   $matriculas = DB::connection('mysql_segunda')->select("
    SELECT 
        matricula.id_alumno, 
        incripcion_curso.total, 
        cursos.nombre_curso, 
        incripcion_curso.credito,
        licencia.resolucion_licencia
    FROM matricula
    INNER JOIN incripcion_curso ON incripcion_curso.idmatricula = matricula.idmatricula
    INNER JOIN docente_curso ON incripcion_curso.id_docente_curso = docente_curso.iddocente_curso
    INNER JOIN cursos ON docente_curso.idcursos = cursos.idcursos
    LEFT JOIN licencia ON licencia.idmatricula = matricula.idmatricula
    WHERE matricula.id_alumno IN (" . implode(',', $postulanteIds) . ")
      AND matricula.idsemestre_academico = ?
      AND matricula.ciclo_matricula = ?
      AND matricula.idtipo_matricula = ?
      AND matricula.id_turno = ?
", [
    $request->id_semestre,
    $request->id_ciclo,
    $request->id_tipo_matricula,
    $request->id_turno
]);

    // 5. Obtener nombres
    $alumnos = DB::connection('mysql')->select("
        SELECT idpostulante, apellidos_pater_postulante, apellidos_mater_postulante, nombres_postulante
        FROM postulante
        WHERE idpostulante IN (" . implode(',', $postulanteIds) . ")
    ");

    // 6. Combinar datos
    $matriculas = collect($matriculas);
    $alumnos = collect($alumnos);

    $alumnosnotas = $matriculas->map(function ($matricula) use ($alumnos) {
        $alumno = $alumnos->firstWhere('idpostulante', $matricula->id_alumno);
        return (object) [
            'id_alumno' => $matricula->id_alumno,
            'apellidos_pater_postulante' => $alumno->apellidos_pater_postulante ?? null,
            'apellidos_mater_postulante' => $alumno->apellidos_mater_postulante ?? null,
            'nombres_postulante' => $alumno->nombres_postulante ?? null,
            'nombre_curso' => $matricula->nombre_curso,
            'credito' => $matricula->credito,
            'total' => $matricula->total,
            'resolucion_licencia' => $matricula->resolucion_licencia ?? null,
        ];
    });

    $notasAgrupadas = $alumnosnotas
    ->sortBy('apellidos_pater_postulante')
    ->groupBy('id_alumno')
    ->map(function ($items) {
        $alumno = $items->first();
        return (object)[
            'id_alumno' => $alumno->id_alumno,
            'apellidos_pater_postulante' => $alumno->apellidos_pater_postulante,
            'apellidos_mater_postulante' => $alumno->apellidos_mater_postulante,
            'nombres_postulante' => $alumno->nombres_postulante,
            'resolucion_licencia' => $alumno->resolucion_licencia,

            'cursos' => $items->map(function ($item) {
                return (object)[
                    'nombre_curso' => $item->nombre_curso,
                    'credito' => $item->credito,
                    'total' => $item->total,
                ];
            }),
        ];
    });

///6.1 cantidad de cursos para vista 
$cantidadCursos = DB::connection('mysql_segunda')->selectOne("
    SELECT COUNT(DISTINCT cursos.idcursos) AS total_cursos
    FROM matricula
    INNER JOIN incripcion_curso ON incripcion_curso.idmatricula = matricula.idmatricula
    INNER JOIN docente_curso ON incripcion_curso.id_docente_curso = docente_curso.iddocente_curso
    INNER JOIN cursos ON docente_curso.idcursos = cursos.idcursos
    WHERE matricula.id_alumno IN (" . implode(',', $postulanteIds) . ")
      AND matricula.idsemestre_academico = ?
      AND matricula.ciclo_matricula = ?
      AND matricula.idtipo_matricula = ?
      AND matricula.id_turno = ?
", [
    $request->id_semestre,
    $request->id_ciclo,
    $request->id_tipo_matricula,
    $request->id_turno
]);

    // 7. Generar PDF
    $pdf = Pdf::loadView('pdf.semestrenotasreporte', [
        'alumnosnotas'     => $alumnosnotas,
        'alumnoscurso'     => $alumnoscurso,
        'notasAgrupadas'   => $notasAgrupadas,
        'datospdfglobal'   => $datospdfglobal,
        'cantidadCursos'   => $cantidadCursos,
        'tipoMatricula'    => $request->id_tipo_matricula,
    ])->setPaper('A4', 'landscape');  

    $dompdf = $pdf->getDomPDF();
$dompdf->render();
$canvas = $dompdf->get_canvas();
$font = $dompdf->getFontMetrics()->getFont("Helvetica", "normal");
$canvas->page_text(746, 532, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 7, [0, 0, 0]);
return $dompdf->stream('reporte_semestral.pdf', ['Attachment' => false]);   
  
}

public function generarPdfPorSemestre(Request $request, $idSemestre)
{
    $dni = $request->input('dni');

    // Buscar al alumno por DNI
    $alumno = DB::connection('mysql')
        ->table('postulante')
        ->where('idpostulante', $dni)
        ->select(
            'idpostulante',
            'apellidos_pater_postulante',
            'apellidos_mater_postulante',
            'nombres_postulante'
        )
        ->first();

    if (! $alumno) {
        abort(404, 'Alumno no encontrado');
    }

    // Buscar todas las matrículas del alumno en ese semestre
    $matriculas = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $alumno->idpostulante)
        ->where('idsemestre_academico', $idSemestre)
        ->get();

    if ($matriculas->isEmpty()) {
        abort(404, 'No hay matrículas registradas en ese semestre');
    }

    // Obtener datos comunes (carrera, ciclo, sección) desde la primera matrícula
    $firstMatricula = $matriculas->first();

    $carrera = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->where('idmalla_curricular', $firstMatricula->idmalla)
        ->select('nombre_malla_curricular')
        ->first();

    $ciclo = DB::connection('mysql_segunda')
        ->table('ciclos')
        ->where('idciclos', $firstMatricula->ciclo_matricula)
        ->select('nombre_ciclo')
        ->first();

    $seccion = DB::connection('mysql_segunda')
        ->table('seccion')
        ->where('idseccion', $firstMatricula->idseccion)
        ->select('nom_seccion')
        ->first();

    $semestre = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', $idSemestre)
        ->first();

    // Consolidar cursos por matrícula
    $cursosRegulares = collect();
    $cursosSubsanacion = collect();

    foreach ($matriculas as $matricula) {
        $cursos = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('ic.idmatricula', $matricula->idmatricula)
            ->select(
                'c.nombre_curso',
                'c.credito',
                'c.horas',
                'ic.idCalificaciones1',
                'ic.recomendacion_nota1',
                'ic.idCalificaciones2',
                'ic.recomendacion_nota2',
                'ic.idCalificaciones3',
                'ic.recomendacion_nota3',
                'ic.total',
                'ic.estado_nota'
            )
            ->get();

        if ($matricula->idtipo_matricula == 2) {
            $cursosSubsanacion = $cursosSubsanacion->merge($cursos);
        } else {
            $cursosRegulares = $cursosRegulares->merge($cursos);
        }
    }

    // Generar PDF
    $pdf = Pdf::loadView('pdf.constancia-matricula', [
        'alumno'             => $alumno,
        'carrera'            => $carrera,
        'ciclo'              => $ciclo,
        'seccion'            => $seccion,
        'semestre'           => $semestre,
        'dni'                => $dni,
        'cursosRegulares'    => $cursosRegulares,
        'cursosSubsanacion'  => $cursosSubsanacion,
    ]);

    return $pdf->stream('Constancia_Matricula_'.$dni.'.pdf');
}

public function semestrepdf(Request $request)
{
    
    //    dd($request);

    // 1. Obtener postulantes según malla seleccionada
    $postulantes = DB::connection('mysql')->select("
        SELECT idpostulante
        FROM postulante
        WHERE id_malla = ?
    ", [$request->id_malla]);

    $postulanteIds = collect($postulantes)->pluck('idpostulante')->unique()->toArray();
     

    if (empty($postulanteIds)) {
        return back()->with('error', 'No se encontraron postulantes para la malla seleccionada.');
    }

    $idsString = implode(',', array_map('intval', $postulanteIds));

    // 2. Obtener información global del PDF
    $datospdfglobal = DB::connection('mysql_segunda')->select("
        SELECT semestre_academico.periodo,
               semestre_academico.año,
               malla_curricular.nombre_malla_curricular,
               malla_curricular.idmalla_curricular,
               turno.nombre_turno,
               ciclos.nombre_ciclo,
               seccion.nom_seccion,
               COUNT(DISTINCT matricula.id_alumno) AS cantidad_alumnos
        FROM matricula
        JOIN semestre_academico ON matricula.idsemestre_academico = semestre_academico.idsemestre_academico
        JOIN incripcion_curso ON incripcion_curso.idmatricula = matricula.idmatricula
        JOIN docente_curso ON incripcion_curso.id_docente_curso = docente_curso.iddocente_curso
        JOIN cursos ON docente_curso.idcursos = cursos.idcursos
        JOIN plan_de_estudio ON plan_de_estudio.idcursos = cursos.idcursos
        JOIN malla_curricular ON plan_de_estudio.malla_curricular_idmalla_curricular = malla_curricular.idmalla_curricular
        JOIN turno ON matricula.id_turno = turno.idturno
        JOIN seccion ON matricula.idseccion = seccion.idseccion
        JOIN ciclos ON plan_de_estudio.idciclos = ciclos.idciclos
        WHERE matricula.id_alumno IN ($idsString)
          AND matricula.idsemestre_academico = ?
          AND matricula.ciclo_matricula = ?
          AND matricula.idtipo_matricula = ?
          AND matricula.id_turno = ?
        
        GROUP BY semestre_academico.periodo,
                 semestre_academico.año,
                 malla_curricular.nombre_malla_curricular,
                 malla_curricular.idmalla_curricular,
                 turno.nombre_turno,
                 ciclos.nombre_ciclo,
                 seccion.nom_seccion
    ", [$request->id_semestre, $request->id_ciclo, $request->id_tipo_matricula, $request->id_turno]);

    // 3. Obtener matrículas únicas por alumno
    $matriculas = DB::connection('mysql_segunda')->select("
        SELECT DISTINCT matricula.id_alumno
        FROM matricula
        WHERE matricula.id_alumno IN ($idsString)
          AND matricula.idsemestre_academico = ?
          AND matricula.ciclo_matricula = ?
          AND matricula.idtipo_matricula = ?
          AND matricula.id_turno = ?
    ", [$request->id_semestre, $request->id_ciclo, $request->id_tipo_matricula,$request->id_turno ]);

    $matriculaIds = collect($matriculas)->pluck('id_alumno')->unique()->toArray();



    // 4. Obtener datos del alumno
    $alumnos = DB::connection('mysql')->select("
        SELECT idpostulante, apellidos_pater_postulante, apellidos_mater_postulante,
               nombres_postulante, genero_postulante, edad_postulante, fecha_de_nacimiento_postu
        FROM postulante
        WHERE idpostulante IN (" . implode(',', $matriculaIds) . ")
    ");

    // 5. Agrupar por alumno
    $alumnosAgrupados = collect($alumnos)->map(function ($alumno) {
        return (object)[
            'idpostulante' => $alumno->idpostulante,
            'apellidos_pater_postulante' => $alumno->apellidos_pater_postulante,
            'apellidos_mater_postulante' => $alumno->apellidos_mater_postulante,
            'nombres_postulante' => $alumno->nombres_postulante,
            'genero_postulante' => $alumno->genero_postulante,
            'edad_postulante' => $alumno->edad_postulante,
            'fecha_de_nacimiento_postu' => $alumno->fecha_de_nacimiento_postu,
        ];
    });

    // 6. Generar PDF
    $pdf = Pdf::loadView('pdf.semestrereporte', [
        'alumnos' => $alumnosAgrupados,
        'datospdfglobal' => $datospdfglobal,
    ])->setPaper('A4', 'portrait');

    return $pdf->stream('reporte_semestral.pdf');
}



public function generarPdfPorSemestresubsanacion(Request $request, $idSemestre)
{
    $dni = $request->input('dni');

    // Buscar al alumno por DNI
    $alumno = DB::connection('mysql')
        ->table('postulante')
        ->where('idpostulante', $dni)
        ->select(
            'idpostulante',
            'apellidos_pater_postulante',
            'apellidos_mater_postulante',
            'nombres_postulante'
        )
        ->first();

    if (! $alumno) {
        abort(404, 'Alumno no encontrado');
    }

    // Buscar todas las matrículas del alumno en ese semestre
    $matriculas = DB::connection('mysql_segunda')
        ->table('matricula')
        ->where('id_alumno', $alumno->idpostulante)
        ->where('idsemestre_academico', $idSemestre)
        ->get();

    if ($matriculas->isEmpty()) {
        abort(404, 'No hay matrículas registradas en ese semestre');
    }

    // Obtener datos comunes (carrera, ciclo, sección) desde la primera matrícula
    $firstMatricula = $matriculas->first();

    $carrera = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->where('idmalla_curricular', $firstMatricula->idmalla)
        ->select('nombre_malla_curricular')
        ->first();

    $ciclo = DB::connection('mysql_segunda')
        ->table('ciclos')
        ->where('idciclos', $firstMatricula->ciclo_matricula)
        ->select('nombre_ciclo')
        ->first();

    $seccion = DB::connection('mysql_segunda')
        ->table('seccion')
        ->where('idseccion', $firstMatricula->idseccion)
        ->select('nom_seccion')
        ->first();

    $semestre = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('idsemestre_academico', $idSemestre)
        ->first();

    // Consolidar cursos por matrícula
    $cursosRegulares = collect();
    $cursosSubsanacion = collect();

    foreach ($matriculas as $matricula) {
        $cursos = DB::connection('mysql_segunda')
            ->table('incripcion_curso as ic')
            ->join('matricula as matri', 'matri.idmatricula','=', 'ic.idmatricula')
            ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
            ->join('docente as docen','dc.id_docente','=','docen.iddocente')
            ->join('userprofile as datos_docente','docen.id_users','=','datos_docente.id_users')
            ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
            ->where('ic.idmatricula', $matricula->idmatricula)
            ->select(
                'datos_docente.nombre',
                'c.nombre_curso',
                'matri.ciclo_matricula',
                'c.credito',
                'c.horas',
                'ic.idCalificaciones1',
                'ic.recomendacion_nota1',
                'ic.idCalificaciones2',
                'ic.recomendacion_nota2',
                'ic.idCalificaciones3',
                'ic.recomendacion_nota3',
                'ic.total',
                'ic.estado_nota'
            )
            ->get();

        if ($matricula->idtipo_matricula == 2) {
            $cursosSubsanacion = $cursosSubsanacion->merge($cursos);
        } else {
            $cursosRegulares = $cursosRegulares->merge($cursos);
        }
    }

    // Generar PDF
   $pdf = Pdf::loadView('pdf.documentosubsanacion', [
    'alumno'             => $alumno,
    'carrera'            => $carrera,
    'ciclo'              => $ciclo,
    'seccion'            => $seccion,
    'semestre'           => $semestre,
    'dni'                => $dni,
    'cursosRegulares'    => $cursosRegulares,
    'cursosSubsanacion'  => $cursosSubsanacion,
])->setPaper('a4', 'landscape');

    return $pdf->stream('Constancia_Matricula_'.$dni.'.pdf');
}

public function generarPdfMemorando(Request $request)
{


    $titulo     = $request->input('memorando');
    $referencia = $request->input('referencia');
    $docente    = $request->input('docente');
    $periodo    = $request->input('periodo');
    $idDocente  = $request->input('idDocente');

    $semestreActivo = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('estado_matricula', 1)
        ->first();

    $registros = DB::connection('mysql_segunda')
        ->table('incripcion_curso as ic')
        ->join('docente_curso as dc', 'ic.id_docente_curso', '=', 'dc.iddocente_curso')
        ->join('matricula as m', 'ic.idmatricula', '=', 'm.idmatricula')
        ->join('ciclos as ciclo','ciclo.idciclos','=','m.ciclo_matricula')
        ->join('cursos as c', 'dc.idcursos', '=', 'c.idcursos')
        ->join('malla_curricular as mc', 'm.idmalla', '=', 'mc.idmalla_curricular')
        ->where('dc.id_docente', $idDocente)
        ->where('dc.tipodocente_curso', 2)
        ->where('dc.idsemestre_academico', $semestreActivo->idsemestre_academico)
        ->select(
            'm.id_alumno',
            'c.nombre_curso',
            'ciclo.nombre_ciclo',
            'm.ciclo_matricula',
            'mc.carrera_malla'
        )
        ->get();

    $alumnos = $registros->map(function ($r) {
        $alumno = DB::connection('mysql')
            ->table('postulante')
            ->where('idpostulante', $r->id_alumno)
            ->select('nombres_postulante', 'apellidos_pater_postulante', 'apellidos_mater_postulante')
            ->first();

        $carrera = DB::connection('mysql')
            ->table('carreras')
            ->where('idcarreras', $r->carrera_malla)
            ->select('nombre_de_carrera')
            ->first();

        return (object) [
            'nombre' => "{$alumno->apellidos_pater_postulante} {$alumno->apellidos_mater_postulante}, {$alumno->nombres_postulante}",
            'curso' => $r->nombre_curso,
            'ciclo' => $r->nombre_ciclo,
            'carrera' => $carrera->nombre_de_carrera ?? '—',
        ];
    });

    $pdf = Pdf::loadView('pdf.memorando_subsanacion', compact(
        'titulo', 'referencia', 'docente', 'periodo', 'alumnos'
    ))->setPaper('a5', 'landscape');

    return $pdf->stream('Memorando_Subsanacion.pdf');
}



public function historialalumnoindex()
{
    $mallas = DB::connection('mysql_segunda')
        ->table('malla_curricular')
        ->select('idmalla_curricular', 'nombre_malla_curricular')
        ->get();

    return view('matricula.matricula_proceso.historialindex', compact('mallas'));
}

public function agregaralumnohistorial(Request $request)
{
    DB::connection('mysql')->beginTransaction();

    try {
        // 1. Crear usuario
        $userId = DB::connection('mysql')->table('users')->insertGetId([
            'dni' => $request->dni,
            'password' => bcrypt('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Asignar rol
        DB::connection('mysql')->table('model_has_roles')->insert([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => $userId,
        ]);

        // 3. Crear postulante
        DB::connection('mysql')->table('postulante')->insert([
             'idpostulante' => $request->dni,
    'nombres_postulante' => $request->nombres,
    'genero_postulante' => 1,
    'apellidos_pater_postulante' => $request->apellidos_paterno,
    'apellidos_mater_postulante' => $request->apellidos_materno,
    'id_malla' => $request->id_malla,
    'idubigeo_nacimiento' => 110402,
    'idubigeo_domicilio' => 110402,
    'idubigeo_colegio' => 110402,
    'lengua_mater' => 1,
    'id_identidad_etnica' => 1,
    'id_est_civil' => 1,
    'con_beca' => 1,
    'con_hijos' => 1,
    'con_estudios' => 1,
    'con_trabajo' => 1,
    'idtipo_colegio' => 2,
    'tipodocumento' => 1,
    'nacionalidad' => 'peruana',
    'fecha_inscripcion' => now(),

        ]);

        DB::connection('mysql')->commit();

        return redirect()->back()->with('success', 'Alumno registrado correctamente');

    } catch (\Exception $e) {
        DB::connection('mysql')->rollBack();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}

public function generarPlantillaNotas(Request $request)
{
    $semestreId = $request->input('semestre_id');
    $ciclo = $request->input('ciclo');
    $mallaId = $request->input('malla_id');
    $tipoMatricula = $request->input('tipo_matricula'); // ← esta línea faltaba

    // Validaciones básicas
    if (! $semestreId || ! $ciclo || ! $mallaId || ! $tipoMatricula) {
        return back()->with('error', '❌ Faltan parámetros para generar la plantilla.');
    }

    // Nombre del archivo
    $nombreArchivo = "Plantilla_Notas_S{$semestreId}_C{$ciclo}_M{$mallaId}_T{$tipoMatricula}.xlsx";

    // Exportar usando clase personalizada
    return Excel::download(
        new PlantillaNotasExport($semestreId, $ciclo, $mallaId, $tipoMatricula),
        $nombreArchivo
    );
}

public function procesarExcel(Request $request)
{

    $cursosRegistrados = [];
$alumnosProcesados = [];

    $archivo = $request->file('archivo_excel');

    if (! $archivo) {
        return back()->with('error', '❌ No se recibió ningún archivo.');
    }

    $datos = Excel::toCollection(null, $archivo)[0];
    $filas = $datos->slice(2); // Ignorar leyenda y encabezados

    $insertados = 0;
    $omitidos = 0;
    $errores = 0;

    DB::beginTransaction();
    try {
        foreach ($filas as $fila) {
            $semestreId = $fila[0];
            $tipoMatricula = $fila[1];
            $dni = trim($fila[2]);
            $nombre = trim($fila[3]);

            if (! $semestreId || ! $tipoMatricula || ! $dni) {
                $errores++;
                continue;
            }

            $alumno = DB::connection('mysql')
                ->table('postulante')
                ->where('idpostulante', $dni)
                ->first();

            if (! $alumno) {
                $errores++;
                continue;
            }

            $mallaId = $alumno->id_malla;

            $ciclo = DB::connection('mysql_segunda')
                ->table('plan_de_estudio')
                ->where('malla_curricular_idmalla_curricular', $mallaId)
                ->whereExists(function ($q) use ($fila) {
                    $q->select(DB::raw(1))
                      ->from('cursos')
                      ->whereColumn('cursos.idcursos', 'plan_de_estudio.idcursos')
                      ->where('cursos.nombre_curso', 'like', '%' . trim($fila[4]) . '%');
                })
                ->value('idciclos');

            $idMatricula = DB::connection('mysql_segunda')
                ->table('matricula')
                ->where('id_alumno', $dni)
                ->where('idsemestre_academico', $semestreId)
                ->where('idtipo_matricula', $tipoMatricula)
                ->where('ciclo_matricula', $ciclo)
                ->where('idmalla', $mallaId)
                ->value('idmatricula');

            if (! $idMatricula) {
                $idMatricula = DB::connection('mysql_segunda')
                    ->table('matricula')
                    ->insertGetId([
                        'id_alumno' => $dni,
                        'idsemestre_academico' => $semestreId,
                        'idestado_matricula' => 1,
                        'idtipo_matricula' => $tipoMatricula,
                        'fecha_matricula' => now(),
                        'total_credito' => 0,
                        'credito_alumno' => 0,
                        'ciclo_matricula' => $ciclo,
                        'id_turno' => 1,
                        'idseccion' => 1,
                        'idmalla' => $mallaId,
                        'id_reporte_matricula' => $tipoMatricula,
                    ]);
            }

            for ($i = 4; $i < count($fila); $i += 2) {
                $nota = is_numeric($fila[$i]) ? (int) $fila[$i] : null;
                $estadoTexto = strtolower(trim($fila[$i + 1]));
                $estado = match ($estadoTexto) {
                    'aprobado' => 1,
                    'desaprobado' => 0,
                    default => null
                };

                $cursosRegistrados[] = trim(str_replace(['(Nota)', '(Estado: Aprobado/Desaprobado)'], '', $nombreCurso));
$alumnosProcesados[] = $dni;

                $nombreCurso = $datos[1][$i]; // encabezado
                $cursoId = DB::connection('mysql_segunda')
                    ->table('cursos')
                    ->where('nombre_curso', 'like', '%' . trim(str_replace(['(Nota)', '(Estado: Aprobado/Desaprobado)'], '', $nombreCurso)) . '%')
                    ->value('idcursos');

                if (! $cursoId || $nota === null || $estado === null) {
                    $errores++;
                    continue;
                }

                $docenteCurso = DB::connection('mysql_segunda')
                    ->table('docente_curso')
                    ->where('idcursos', $cursoId)
                    ->where('idsemestre_academico', $semestreId)
                    ->where('tipodocente_curso', $tipoMatricula)
                    ->first();

                if (! $docenteCurso) {
                    $errores++;
                    continue;
                }

                $yaExiste = DB::connection('mysql_segunda')
                    ->table('incripcion_curso')
                    ->where('idmatricula', $idMatricula)
                    ->where('id_docente_curso', $docenteCurso->iddocente_curso)
                    ->exists();

                if ($yaExiste) {
                    $omitidos++;
                    continue;
                }

                DB::connection('mysql_segunda')->table('incripcion_curso')->insert([
                    'idmatricula' => $idMatricula,
                    'id_docente_curso' => $docenteCurso->iddocente_curso,
                    'credito' => 0,
                    'idCalificaciones1' => 0,
                    'idCalificaciones2' => 0,
                    'idCalificaciones3' => 0,
                    'total' => $nota,
                    'estado_nota' => $estado,
                ]);

                $insertados++;
            }
        }

     DB::commit();

$cursosRegistrados = array_unique($cursosRegistrados);
$alumnosProcesados = array_unique($alumnosProcesados);

$resumen = [
    'insertados' => $insertados,
    'omitidos' => $omitidos,
    'errores' => $errores,
    'cursos' => $cursosRegistrados,
    'alumnos' => $alumnosProcesados,
];
  
        return back()->with('success', "✅ Carga completada. Insertados: {$insertados}, Omitidos: {$omitidos}, Errores: {$errores}")   ->with('resumen', $resumen);

       
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Error en carga masiva: " . $e->getMessage());
        return back()->with('error', '❌ Error al procesar el archivo.');
    }
}


public function semestrenotasexcel(Request $request)
{
   $semestreActivo = DB::connection('mysql_segunda')
        ->table('semestre_academico')
        ->where('estado_matricula', 1)
        ->first();

    return Excel::download(new \App\Exports\ReporteNotasExcelExport($semestreActivo->idsemestre_academico), 'reporte_notas.xlsx');

}


}
