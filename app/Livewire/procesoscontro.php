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
        try { DB::beginTransaction(); $agregarpostulante = DB::insert("INSERT INTO `procesos`(`nombre_proceso`, `año_admision`, `nota_min_apro`, `periodo_aca`, `fecha_inscri`, `fecha_cierre_inscri`, `fecha_publi_resul`) VALUES (?,?,?,?,?,?,?);", [
             $request->nombreproceso,
              $request->añoadmision, 
              $request->notamin,
               $request->periacade,
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
                `fecha_inscri` = ?, 
                `fecha_cierre_inscri` = ?, 
                `fecha_publi_resul` = ? 
                WHERE `idprocesos` = ?", [
                $request->nombre,
                $request->año,
                $request->nota,
                $request->periodo,
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





    public function indexvacantes(){
        $procesos = DB::select('SELECT * FROM procesos');
        $carreras = DB::select('SELECT * FROM carreras');
        $vacantes = DB::select('SELECT * FROM `vacantes` INNER JOIN procesos ON vacantes.idprocesos = procesos.idprocesos INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras');
        return view('procesos.vacantes')->with('procesos',$procesos)->with('carreras',$carreras)->with('vacantes',$vacantes);
    }

    public function agregarvacantes(Request $request)
    {
        // Validación de datos (opcional)
        $request->validate([
            'proceso' => 'required|integer',
            'vacantes.*.idcarrera' => 'required|integer',
            'vacantes.*.cantivacantes' => 'required|integer',
        ]);
    
        // Obtener el proceso del request
        $proceso = $request->input('proceso');
    
        // Verificar si ya existen vacantes para el mismo proceso
        $existente = DB::table('vacantes')
            ->where('idprocesos', $proceso)
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
                'idmodalidad' => 0
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
