<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class procesoscontro extends Controller
{
    public function index(){
        $procesos = DB::select('SELECT * FROM procesos');
        return view('procesos.index')->with('procesos',$procesos);
    }

    public function agregarprocesos(request $request){
        try { DB::beginTransaction(); $agregarpostulante = DB::insert("INSERT INTO `procesos`(`nombre_proceso`, `año_admision`, `nota_min_apro`, `periodo_aca`,`rd_proceso`, `aprobacion_metas`, `fecha_inscri`, `fecha_cierre_inscri`, `fecha_publi_resul`) VALUES (?,?,?,?,?,?,?,?,?);", [
             $request->nombreproceso,
              $request->añoadmision,
              $request->notamin,
               $request->periacade,
               $request->rd_proceso_form,
               $request->aprobacion_metas_form,
                $request->fechaini,
                $request->fechafin,
                $request->fecharesu, ]);
                DB::commit();
                return redirect()->route('procesos.index')->with('success', 'Proceso Agregado con exito');
            } catch (\Exception $e)
            { DB::rollBack();
                return redirect()->route('procesos.index')->with('error', 'Error al insertar datos: ' . $e->getMessage()); }

    }


    public function editarprocesos(request $request){
        
        
        try {
            DB::beginTransaction();

            // Consulta para actualizar los datos
            $actualizarproceso = DB::update("UPDATE `procesos` SET
                `nombre_proceso` = ?,
                `año_admision` = ?,
                `nota_min_apro` = ?,
                `periodo_aca` = ?,
                `rd_proceso` = ?,
                `aprobacion_metas` = ?,                
                `fecha_inscri` = ?,
                `fecha_cierre_inscri` = ?,
                `fecha_publi_resul` = ?
                WHERE `idprocesos` = ?", [
                $request->nombre,
                $request->año,
                $request->nota,
                $request->periacade,
                $request->rd,
                $request->metas,
                $request->inicio,
                $request->cierre,
                $request->publicacion,
                $request->id
            ]);

            DB::commit();
            return redirect()->route('procesos.index')->with('success', 'Proceso Actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('procesos.index')->with('error', 'Error al actualizar datos: ' . $e->getMessage());
        }


    }

    public function cambiarEstado(Request $request)
{
    try {
        $id = $request->id;
        $estado = $request->estado;

        $nuevoEstado = $estado == 1 ? 0 : 1;

        DB::table('procesos')
            ->where('idprocesos', $id)
            ->update(['estado_proceso' => $nuevoEstado]);

        return response()->json(['success' => true, 'nuevo_estado' => $nuevoEstado]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
}

public function eliminarProceso($id)
{
    try {
        DB::table('procesos')->where('idprocesos', $id)->delete();
        return redirect()->route('procesos.index')->with('success', 'Proceso eliminado correctamente.');
    } catch (\Exception $e) {
        return redirect()->route('procesos.index')->with('error', 'Error al eliminar proceso: ' . $e->getMessage());
    }
}




    public function indexvacantes(){
        $procesos = DB::select('SELECT * FROM procesos where estado_proceso = 1');
        $modalidad = DB::select('SELECT * FROM modalidad');
        $carreras = DB::select('SELECT * FROM carreras');
        $vacantes = DB::select('SELECT * FROM `vacantes` INNER JOIN procesos ON vacantes.idprocesos = procesos.idprocesos INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras');
        return view('procesos.vacantes')->with('procesos',$procesos)->with('carreras',$carreras)->with('vacantes',$vacantes)->with('modalidad',$modalidad);
    }

    public function agregarvacantes(Request $request)
    {
        // dd($request);
        // Validación de datos (opcional)
        $request->validate([
            'proceso' => 'required|integer',
            'modalidad' => 'required|integer', // Validación para modalidad
            'inicio' => 'required|date', // Validación para fecha de inicio
            'cierre' => 'required|date', // Validación para fecha de cierre
            'vacantes.*.idcarrera' => 'required|integer',
            'vacantes.*.cantivacantes' => 'required|integer',
        ]);

        // Obtener el proceso del request
        $proceso = $request->input('proceso');
        $modalidad = $request->input('modalidad'); // Captura del valor de modalidad
        $inicio = $request->input('inicio'); // Captura del valor de la fecha de inicio
        $cierre = $request->input('cierre'); // Captura del valor de la fecha de cierre

        // Verificar si ya existen vacantes para el mismo proceso
        $existente = DB::table('vacantes')
            ->where('idprocesos', $proceso)
            ->where('idmodalidad', $modalidad)
            ->exists();

        if ($existente) {
            return redirect()->back()->with('error', 'Proceso duplicado. Ya existen vacantes para este proceso.');
        }

        // Obtener los datos de vacantes
        $vacantesData = $request->input('vacantes');
        foreach ($vacantesData as $vacante) {
            DB::table('vacantes')->insert([
                'idcarreras' => $vacante['idcarrera'],
                'cantidad_vacantes' => $vacante['cantivacantes'],
                'idprocesos' => $proceso,
                'idmodalidad' => $modalidad,
                'Fecha_de_inicio' => $inicio,
                'fecha_de_fin' =>$cierre
            ]);
        }

        return redirect()->back()->with('success', 'Vacantes guardadas exitosamente.');
    }



    public function editarvacantes(Request $request)
{
    // dd($request);
    try {
        DB::beginTransaction();

        // Consulta para actualizar los datos
        $actualizarvacantes = DB::update("UPDATE `vacantes` SET
            `cantidad_vacantes` = ?
            WHERE `idvacantes` = ?", [
            $request->cantiva,
            $request->idvaca
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'Vacante Actualizados con exitosamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('procesos.indexvacantes')->with('error', 'Error al actualizar datos: ' . $e->getMessage());
    }
}




}
