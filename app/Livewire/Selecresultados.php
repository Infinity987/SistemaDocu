<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Exports\ResultadosHistoricosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ResultadosHistoricosImport;





class Selecresultados extends Component
{       
    use WithFileUploads;
    public $procesos = [];
    public $modalidad = [];
    public $carreras = [];
    public $archivoNotas;
    public $archivoNotasFase2;
    public $idproceso = null;
    public $segundaNotaHabilitada = false;
    public $mostrarDesaprobados = false;
    public $archivoHistorico;

public $desaprobados = [];
public $desempates = [];
public $vacantesRestantes = 0;
public $mostrarDesempate = false;
public $seleccionadosDesempate = [];
public $idPdfIngresantes;
public $desempatesPorCarrera = [];





    

    public $selectedProceso = null;
    public $selectedModalidad = null;
    public $selectedCarrera = null;



    public function cargarPrimeraNota()
{
     \Log::info('🔍 Entró al método cargarPrimeraNota desde Livewire');

    if (empty($this->carreras)) {
        session()->flash('error', 'No hay postulantes para procesar.');
        return;
    }
    // dd($this->carreras);

    $fechaActual = date('Y-m-d');
    $idPdfPrimerNota = DB::table('pdf_primeranota')->insertGetId([
        'fecha' => $fechaActual,
    ]);

    foreach ($this->carreras as $index => $carrera) {
        $notaMinima = DB::table('inscripcion')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->where('inscripcion.idinscripcion', $carrera->idinscripcion)
            ->value('procesos.nota_min_apro') ?? 11;

        $nota1_1 = $carrera->nota1_1 ?? 0;
        $nota1_2 = $carrera->nota1_2 ?? 0;
        $nota1_3 = $carrera->nota1_3 ?? 0;

        $notaOriginal = $nota1_1 + $nota1_2 + $nota1_3;
        $notaDividida = $notaOriginal / 2.5;

        $asistencia = empty($nota1_1) ? 'No se presentó' : 'Se presentó';
        $estado_apro_desa = ($notaDividida < $notaMinima) ? 'Desaprobó' : 'Aprobó';

        DB::table('resultados')->updateOrInsert(
            ['idinscripcion' => $carrera->idinscripcion],
            [
                'asistencia' => $asistencia,
                'nota1_mate' => $nota1_1,
                'nota1_comu' => $nota1_2,
                'nota1_demo' => $nota1_3,
                'nota1' => $notaOriginal,
                'nota2_cola' => null,
                'nota2_pensa' => null,
                'nota2_TI' => null,
                'nota2' => null,
                'nota_total' => null,
                'estado_apro_desa' => $estado_apro_desa,
                'id_pdfprimeranota' => DB::raw('IFNULL(id_pdfprimeranota, '.$idPdfPrimerNota.')'),
                'estado_ingreso' => null,
                'orden_de_merito' => null
            ]
        );
    }

    session()->flash('success', 'Primera nota registrada con éxito.');
     $this->handleModalidadChange($this->selectedModalidad);

}

public function cargarSegundaYTerceraNota()
{
    \Log::info('📥 Iniciando carga de segunda y tercera nota desde Livewire');

    if (empty($this->carreras)) {
        session()->flash('error', 'No hay postulantes para procesar.');
        return;
    }

    foreach ($this->carreras as $carrera) {
        $nota1 = $carrera->nota1 ?? null;
        $nota2_1 = $carrera->nota2_1 ?? null;
        $nota2_2 = $carrera->nota2_2 ?? null;
        $nota2_3 = $carrera->nota2_3 ?? null;

        $nota2 = $nota2_1 + $nota2_2 + $nota2_3;

        $nota_total = (!is_null($nota1) && !is_null($nota2))
            ? round(($nota1 + $nota2) / 5, 2)
            : null;

        DB::table('resultados')->updateOrInsert(
            ['idinscripcion' => $carrera->idinscripcion],
            [
                'nota2_cola' => $nota2_1,
                'nota2_pensa' => $nota2_2,
                'nota2_TI' => $nota2_3,
                'nota2' => $nota2,
                'nota_total' => $nota_total,
            ]
        );
    }

    // Refrescar datos en tiempo real
    $this->handleModalidadChange($this->selectedModalidad);

    // Confirmación visual opcional
    $this->dispatch('segunda-tercera-nota-cargada');

    session()->flash('success', '✅ Segunda y tercera nota registradas con éxito.');
}


public function generarIngresantes()
{
    \Log::info('🚀 Iniciando generación de ingresantes');

    $resultados = DB::select('
        SELECT 
            resultados.idinscripcion,
            resultados.nota_total,
            resultados.estado_ingreso,
            resultados.orden_de_merito,
            resultados.estado_apro_desa,
            vacantes.cantidad_vacantes,
            vacantes.idvacantes,
            carreras.nombre_de_carrera AS carrera,
            postulante.nombres_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante
        FROM resultados
        INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
        INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
        INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
        INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
        WHERE vacantes.idprocesos = ? AND resultados.estado_apro_desa = ?
    ', [$this->idproceso, 'Aprobó']);

    $agrupados = collect($resultados)->groupBy('idvacantes');
    $this->idPdfIngresantes = DB::table('pdf_ingresantes')->insertGetId(['fecha' => now()]);
    $this->desempatesPorCarrera = [];

    foreach ($agrupados as $idVacante => $postulantes) {
        $postulantes = $postulantes->sortByDesc('nota_total')->values();
        $vacantesDisponibles = $postulantes[0]->cantidad_vacantes;

        $notaCorte = $postulantes[$vacantesDisponibles - 1]->nota_total ?? null;
        $empatados = $postulantes->filter(fn($p) => $p->nota_total == $notaCorte);

        $puestosConEsaNota = $postulantes->filter(fn($p) => $p->nota_total >= $notaCorte)->count();
        $vacantesRestantes = $vacantesDisponibles - ($puestosConEsaNota - $empatados->count());

        if ($empatados->count() > 1 && $vacantesRestantes < $empatados->count()) {
            $this->desempatesPorCarrera[$idVacante] = [
                'carrera' => $postulantes[0]->carrera,
                'empatados' => $empatados,
                'vacantesRestantes' => $vacantesRestantes,
            ];

            $postulantesSinEmpate = $postulantes->filter(fn($p) => $p->nota_total > $notaCorte);
            $ordenMerito = 1;

            foreach ($postulantesSinEmpate as $postulante) {
                $this->actualizarIngresoConMallaYRol($postulante, 'Alcanzó vacante', $ordenMerito++);
            }

            continue;
        }

        $ordenMerito = 1;
        foreach ($postulantes as $postulante) {
            $estadoIngreso = ($vacantesDisponibles-- > 0) ? 'Alcanzó vacante' : 'No Alcanzó Vacante';
            $this->actualizarIngresoConMallaYRol($postulante, $estadoIngreso, $ordenMerito++);
        }
    }

    if (!empty($this->desempatesPorCarrera)) {
        $this->mostrarDesempate = true;
        session()->flash('warning', '⚠️ Se detectaron empates en una o más carreras.');
        return;
    }

    $this->handleModalidadChange($this->selectedModalidad);
    session()->flash('success', '✅ Ingresantes generados con éxito.');
}

public function resolverDesempate()
{
    \Log::info('📋 Estado de seleccionadosDesempate:', ['seleccionadosDesempate' => $this->seleccionadosDesempate]);

    foreach ($this->desempatesPorCarrera as $idVacante => $grupo) {
        $ordenMerito = DB::table('resultados')
            ->where('id_pdfingresantes', $this->idPdfIngresantes)
            ->max('orden_de_merito') + 1;

        foreach ($grupo['empatados'] as $postulante) {
            $seleccionados = array_keys(array_filter(
                $this->seleccionadosDesempate[$idVacante] ?? []
            ));

            $estadoIngreso = in_array($postulante->idinscripcion, $seleccionados)
                ? 'Alcanzó vacante'
                : 'No Alcanzó Vacante';

            $this->actualizarIngresoConMallaYRol(
                $postulante,
                $estadoIngreso,
                $estadoIngreso === 'Alcanzó vacante' ? $ordenMerito++ : null
            );
        }
    }

    $this->mostrarDesempate = false;
    $this->handleModalidadChange($this->selectedModalidad);
    session()->flash('success', '✅ Desempates resueltos correctamente.');
}

/**
 * 🔧 Función auxiliar para actualizar ingreso + malla + rol
 */
private function actualizarIngresoConMallaYRol($postulante, $estadoIngreso, $ordenMerito = null)
{
    // Actualizar tabla resultados
    DB::table('resultados')
        ->where('idinscripcion', $postulante->idinscripcion)
        ->update([
            'estado_ingreso'   => $estadoIngreso,
            'orden_de_merito'  => $ordenMerito,
            'id_pdfingresantes'=> $this->idPdfIngresantes,
        ]);

    // Solo si alcanzó vacante, actualizamos malla y rol
    if ($estadoIngreso === 'Alcanzó vacante') {
        // Obtener carrera del postulante
        $idCarrera = DB::table('inscripcion')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->where('inscripcion.idinscripcion', $postulante->idinscripcion)
            ->value('vacantes.idcarreras');

        // Buscar malla curricular más reciente
        $idMallaCurricular = DB::connection('mysql_segunda')
            ->table('malla_curricular')
            ->where('carrera_malla', $idCarrera)
            ->orderByDesc('año_de_inicio')
            ->value('idmalla_curricular');

        // Obtener postulante
        $idPostulante = DB::table('inscripcion')
            ->where('idinscripcion', $postulante->idinscripcion)
            ->value('idpostulante');

        // Buscar DNI
        $dni = DB::table('postulante')
            ->where('idpostulante', $idPostulante)
            ->value('idpostulante'); 
            
        // Buscar usuario en tabla users
        $idUser = DB::connection('mysql')
            ->table('users')
            ->where('dni', $dni)
            ->value('id');

        // Actualizar rol
        if ($idUser) {
            DB::table('model_has_roles')
                ->where('model_id', $idUser)
                ->update([
                    'role_id' => 4,
                    'model_type' => 'App\\Models\\User',
                ]);
        }

        // Actualizar malla
        if ($idPostulante && $idMallaCurricular) {
            DB::table('postulante')
                ->where('idpostulante', $idPostulante)
                ->update(['id_malla' => $idMallaCurricular]);
        }
    }
}


    public function guardarDesaprobados()
{
    if (empty($this->desaprobados)) {
        session()->flash('error', 'No hay desaprobados para actualizar.');
        return;
    }

    // Insertar registro en tabla pdf_primeranota
    $fechaActual = date('Y-m-d');
    $idPdfPrimerNota = DB::table('pdf_primeranota')->insertGetId([
        'fecha' => $fechaActual,
    ]);

    foreach ($this->desaprobados as $desaprobado) {
        // 🔹 Convertimos a array si viene como objeto
        if (is_object($desaprobado)) {
            $desaprobado = (array) $desaprobado;
        }

        // Obtener nota mínima aprobatoria del proceso
        $notaMinima = DB::table('inscripcion')
            ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->where('inscripcion.idinscripcion', $desaprobado['idinscripcion'])
            ->value('procesos.nota_min_apro');

        $notaMinima = $notaMinima ?? 11;

        // Notas ingresadas desde el formulario
        $nota1_mate = $desaprobado['nota1_mate'] ?? 0;
        $nota1_comu = $desaprobado['nota1_comu'] ?? 0;
        $nota1_demo = $desaprobado['nota1_demo'] ?? 0;

        $notaOriginal = $nota1_mate + $nota1_comu + $nota1_demo;
        $notaDividida = $notaOriginal / 2.5;

        $asistencia = (empty($nota1_mate) && empty($nota1_comu) && empty($nota1_demo))
            ? 'No se presentó'
            : 'Se presentó';

        $estado_apro_desa = ($notaDividida < $notaMinima) ? 'Desaprobó' : 'Aprobó';

        // Actualizar/insertar en resultados
        DB::table('resultados')->updateOrInsert(
            ['idinscripcion' => $desaprobado['idinscripcion']],
            [
                'asistencia' => $asistencia,
                'nota1_mate' => $nota1_mate,
                'nota1_comu' => $nota1_comu,
                'nota1_demo' => $nota1_demo,
                'nota1'      => $notaOriginal,
                'estado_apro_desa' => $estado_apro_desa,
                'id_pdfprimeranota' => DB::raw('IFNULL(id_pdfprimeranota, '.$idPdfPrimerNota.')'),
                'nota2_cola' => null,
                'nota2_pensa' => null,
                'nota2_TI' => null,
                'nota2' => null,
                'nota_total' => null,
                'estado_ingreso' => null,
                'orden_de_merito' => null
            ]
        );
    }

    // Refrescar la lista de desaprobados
    $this->verDesaprobados();
    $this->handleModalidadChange($this->selectedModalidad); // actualiza la vista principal
session()->flash('success', 'Notas de desaprobados actualizadas con éxito.');


    session()->flash('success', 'Notas de desaprobados actualizadas con éxito.');
}


 public function verDesaprobados()
{
    if (!$this->idproceso || !$this->selectedModalidad) {
        return; // si no hay proceso o modalidad seleccionada, no hacemos nada
    }

    $this->desaprobados = DB::table('resultados')
        ->join('inscripcion', 'resultados.idinscripcion', '=', 'inscripcion.idinscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->select(
            'resultados.idresultados',
            'resultados.idinscripcion',
            'postulante.apellidos_pater_postulante',
            'postulante.apellidos_mater_postulante',
            'postulante.nombres_postulante',
            'carreras.nombre_de_carrera',
            'resultados.nota1_mate',
            'resultados.nota1_comu',
            'resultados.nota1_demo',
            'resultados.nota1',
            'resultados.estado_apro_desa'
        )
        ->where('vacantes.idprocesos', $this->idproceso)
        ->where('vacantes.idmodalidad', $this->selectedModalidad) 
        ->where('resultados.estado_apro_desa', 'Desaprobó')
        ->get();

    $this->mostrarDesaprobados = true;
}

public function cerrarDesaprobados()
{
    $this->mostrarDesaprobados = false;
    $this->desaprobados = [];
}


    public function updatedArchivoNotas()
{
    $this->procesarArchivoNotas();
}
public function procesarArchivoNotas()
{
    $contenido = file_get_contents($this->archivoNotas->getRealPath());

    $lineas = explode(PHP_EOL, $contenido);

    foreach ($lineas as $linea) {
        $datos = str_getcsv($linea); // DNI, nota1

        if (count($datos) >= 4) {
            $dni = trim($datos[0]);
            $nota1_comu = trim($datos[1]);
            $nota1_mate = trim($datos[2]);
            $nota1_demo = trim($datos[3]);
            

            foreach ($this->carreras as $carrera) {
                if ($carrera->idpostulante == $dni) {
                    $carrera->nota1_1 = $nota1_comu;
                    $carrera->nota1_2 = $nota1_mate;
                    $carrera->nota1_3 = $nota1_demo;
                    break;

                }
            }
            
        }
    }
}
public function updatedArchivoNotasFase2()
{
    $this->procesarArchivoNotasFase2();
}

public function procesarArchivoNotasFase2()
{
    $contenido = file_get_contents($this->archivoNotasFase2->getRealPath());

    $lineas = explode(PHP_EOL, $contenido);

    foreach ($lineas as $linea) {
        $datos = str_getcsv($linea); // DNI, nota2_1, nota2_2, nota2_3

        if (count($datos) >= 4) {
            $dni = trim($datos[0]);
            $nota2_1 = trim($datos[1]);
            $nota2_2 = trim($datos[2]);
            $nota2_3 = trim($datos[3]);

            foreach ($this->carreras as $carrera) {
                if ($carrera->idpostulante == $dni && $carrera->estado_apro_desa == 'Aprobó') {
                    $carrera->nota2_1 = $nota2_1;
                    $carrera->nota2_2 = $nota2_2;
                    $carrera->nota2_3 = $nota2_3;
                    break;
                }
            }
        }
    }
}


    public function mount()
    {
         $this->procesos = DB::table('vacantes')
            ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
            ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
            ->select('vacantes.idprocesos', 'procesos.nombre_proceso')
             ->where('procesos.estado_proceso', 1) 
            ->distinct()
            ->get();
    }

    public function handleProcesoChange($proceso)
    {
        \Log::info("Proceso seleccionado: {$proceso}");
        $this->modalidad = DB::table('vacantes')
            ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')        
            ->select('vacantes.idmodalidad',                 
                     'modalidad.nombre_modalidad',)
            ->where('vacantes.idprocesos', $proceso)
            ->distinct()
            ->get();
        \Log::info("Modalidad seleccionada: " . json_encode($this->modalidad));
        $this->selectedModalidad  = null;
          $this->idproceso  = $proceso;
    }

    public function handleModalidadChange($modalidad)
    {
        \Log::info("Proceso seleccionado: {$modalidad}");
        $this->carreras = DB::table('inscripcion')
    ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
    ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
    ->join('modalidad', 'vacantes.idmodalidad', '=', 'modalidad.idmodalidad')  
    ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
    ->join('procesos', 'vacantes.idprocesos', '=', 'procesos.idprocesos')
    ->leftjoin('resultados', 'resultados.idinscripcion', '=', 'inscripcion.idinscripcion')
    ->select('inscripcion.idinscripcion',
               'modalidad.nombre_modalidad',   
            'procesos.nombre_proceso', 
             'procesos.idprocesos', 
             'inscripcion.idpostulante', 
             'postulante.apellidos_pater_postulante', 
             'postulante.apellidos_mater_postulante', 
             'postulante.nombres_postulante', 
             'carreras.idcarreras',
             'carreras.nombre_de_carrera',
             'resultados.idresultados',
             'resultados.estado_apro_desa',
             'resultados.nota1',
             'resultados.nota2',             
             'resultados.nota_total')
    ->where('vacantes.idmodalidad', $modalidad)
    ->where('vacantes.idprocesos',$this->idproceso)
    ->orderby('carreras.nombre_de_carrera', 'DESC')
    ->orderby('postulante.apellidos_pater_postulante', 'ASC')
    ->distinct()
    ->get();
    
    

    // dd($this->carreras);

    // dd($this->carreras);

        $this->selectedCarrera = null;
       

        
    }

    

    public function handleCarreraChange($carrera)
    {
        \Log::info("Carrera seleccionada: {$carrera}");
    }

    public function getAllNotasTotalesPresentProperty()
    {
        // Si $this->carreras contiene objetos
        return collect($this->carreras)->every(function ($carrera) {
            return !is_null($carrera->nota_total); // Cambia 'nota_total' si es un atributo diferente
        });
    }
    

    public function generarExcelHistorico()
{
    if (!$this->idproceso || !$this->selectedModalidad) {
        session()->flash('error', 'Debe seleccionar proceso y modalidad.');
        return;
    }

    // Crear registros PDF
    $idPdfPrimerNota = DB::table('pdf_primeranota')->insertGetId(['fecha' => now()]);
    $idPdfIngresantes = DB::table('pdf_ingresantes')->insertGetId(['fecha' => now()]);

    // Obtener inscritos
    $inscritos = DB::table('inscripcion')
        ->join('postulante', 'inscripcion.idpostulante', '=', 'postulante.idpostulante')
        ->join('vacantes', 'inscripcion.idvacantes', '=', 'vacantes.idvacantes')
        ->join('carreras', 'vacantes.idcarreras', '=', 'carreras.idcarreras')
        ->select(
            'inscripcion.idinscripcion',
            'postulante.nombres_postulante',
            'postulante.apellidos_pater_postulante',
            'postulante.apellidos_mater_postulante',
            'carreras.nombre_de_carrera'
        )
        ->where('vacantes.idprocesos', $this->idproceso)
        ->where('vacantes.idmodalidad', $this->selectedModalidad)
        ->get();

    // Preparar datos para Excel
    $datos = $inscritos->map(function ($i) use ($idPdfPrimerNota, $idPdfIngresantes) {
        return [
            'idinscripcion' => $i->idinscripcion,
            'nombres' => "{$i->apellidos_pater_postulante} {$i->apellidos_mater_postulante} {$i->nombres_postulante}",
            'carrera' => $i->nombre_de_carrera,
            'nota1_mate' => null,
            'nota1_comu' => null,
            'nota1_demo' => null,
            'nota1' => null,
            'nota2_cola' => null,
            'nota2_pensa' => null,
            'nota2_TI' => null,
            'nota2' => null,
            'nota_total' => null,
            'estado_apro_desa' => null,
            'estado_ingreso' => null,
            'orden_de_merito' => null,
            'id_pdfprimeranota' => $idPdfPrimerNota,
            'id_pdfingresantes' => $idPdfIngresantes,
        ];
    });

    // Aquí iría la lógica para generar el archivo Excel (puedo ayudarte con eso si usas Laravel Excel)

    return Excel::download(new ResultadosHistoricosExport($datos), 'resultados_historicos.xlsx');


    session()->flash('success', '📄 Excel generado con éxito. Puedes editarlo y subirlo para registrar datos históricos.');
}



public function procesarArchivoHistorico()
{
    if (!$this->archivoHistorico) {
        session()->flash('error', 'Debe subir un archivo válido.');
        return;
    }

    $importador = new ResultadosHistoricosImport();
    Excel::import($importador, $this->archivoHistorico);

    foreach ($importador->filas as $fila) {
        $id = $fila['idinscripcion'] ?? null;

        if (!$id || !DB::table('inscripcion')->where('idinscripcion', $id)->exists()) {
            continue;
        }

        DB::table('resultados')->updateOrInsert(
            ['idinscripcion' => $id],
            [
                'asistencia' => $fila['asistencia'] ?? null,
                'nota1_mate' => $fila['nota1_mate'] ?? null,
                'nota1_comu' => $fila['nota1_comu'] ?? null,
                'nota1_demo' => $fila['nota1_demo'] ?? null,
                'nota1' => $fila['nota1'] ?? null,
                'nota2_cola' => $fila['nota2_cola'] ?? null,
                'nota2_pensa' => $fila['nota2_pensa'] ?? null,
                'nota2_TI' => $fila['nota2_ti'] ?? null,
                'nota2' => $fila['nota2'] ?? null,
                'nota_total' => $fila['nota_total'] ?? null,
                'estado_apro_desa' => $fila['estado_apro_desa'] ?? null,
                'estado_ingreso' => $fila['estado_ingreso'] ?? null,
                'orden_de_merito' => $fila['orden_de_merito'] ?? null,
                'id_pdfprimeranota' => $fila['id_pdfprimeranota'] ?? null,
                'id_pdfingresantes' => $fila['id_pdfingresantes'] ?? null,
            ]
        );
    }

    session()->flash('success', '✅ Resultados históricos registrados correctamente.');
}
   
    public function render()
    {
        return view('livewire.selecresultados');
    }
}
