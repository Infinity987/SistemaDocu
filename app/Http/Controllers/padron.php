<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class padron extends Controller
{
    public function index()   {    
        $padron = DB::select('SELECT padron.id_padron, procesos.nombre_proceso, modalidad.nombre_modalidad, padron.cantidad_postulantes, padron.numero_de_aulas, padron.fecha FROM `padron` INNER JOIN procesos ON padron.id_proceso = procesos.idprocesos INNER JOIN modalidad ON modalidad.idmodalidad = padron.id_modalidad;');   

        return view('padron.index')->with('padron',$padron);
    }


    public function generaraulas(Request $request)
{
    $fechaActual = date('Y-m-d');

    try {
        DB::beginTransaction();

        // Verificar si ya existe un padrón con el mismo proceso y modalidad
        $existePadron = DB::table('padron')
            ->where('id_proceso', $request->idproceso)
            ->where('id_modalidad', $request->modalidad)
            ->exists();

        if ($existePadron) {
            return redirect()->route('padron.index')->with('error', 'Ya existe un padrón para este proceso y modalidad. No se pueden duplicar los datos.');
        }

        // Insertar nuevo padrón
        $idPadron = DB::table('padron')->insertGetId([
            'fecha' => $fechaActual,
            'id_proceso' => $request->idproceso,
            'id_modalidad' => $request->modalidad,
            'cantidad_postulantes' => $request->numerototalpostu,
            'numero_de_aulas' => $request->numerodeaula,
        ]);

        // Actualizar inscripciones con el nuevo padrón
        DB::table('inscripcion')
            ->where('proceso_distin', $request->idproceso)
            ->where('modalidad_distin', $request->modalidad)
            ->update(['id_padron' => $idPadron]);

        // Obtener inscritos válidos
        $inscritos = DB::table('inscripcion')
            ->where('proceso_distin', $request->idproceso)
            ->where('modalidad_distin', $request->modalidad)
            ->orderBy('idinscripcion') // Opcional: para mantener orden
            ->get();

        // Asignar aulas de forma cíclica
        $contadorAula = 1;
        foreach ($inscritos as $inscrito) {
            DB::table('inscripcion')
                ->where('idinscripcion', $inscrito->idinscripcion)
                ->update(['idaula' => $contadorAula]);

            $contadorAula = ($contadorAula % $request->numerodeaula) + 1;
        }

        DB::commit();
        return redirect()->route('padron.index')->with('success', 'Padrón creado y aulas asignadas correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('padron.index')->with('error', 'Error al procesar los datos: ' . $e->getMessage());
    }
}

public function eliminar(Request $request)
{
    try {
        DB::beginTransaction();

        $idPadron = $request->idpadron;

        // Desvincular inscripciones
        DB::table('inscripcion')
            ->where('id_padron', $idPadron)
            ->update(['id_padron' => null, 'idaula' => 0]);

        // Eliminar padrón
        DB::table('padron')->where('id_padron', $idPadron)->delete();

        DB::commit();
        return redirect()->route('padron.index')->with('success', 'Padrón eliminado correctamente y las inscripciones fueron desvinculadas.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('padron.index')->with('error', 'Error al eliminar el padrón: ' . $e->getMessage());
    }
}


}
