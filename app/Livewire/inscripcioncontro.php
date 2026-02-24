<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class inscripcioncontro extends Controller
{
    public function index(){
        $postulante = DB::select('SELECT * FROM postulante');
        return view('inscripcion.index')->with('postulante',$postulante );
    }

    public function agregarInscripcion(Request $request) {
        try {
            // Buscar si ya existe una inscripción con el mismo postulante y vacante
            $existeInscripcion = DB::table('inscripcion')
                ->where('idpostulante', $request->postulante)
                ->where('proceso_distin', $request->idproceso)
                ->exists();
    
            if ($existeInscripcion) {
                return redirect()->route('inscripcion.index')->with('error', 'Usted ya está inscrito a este proceso.');
            }
    
            DB::beginTransaction();
    
            $agregarInscrito = DB::insert("INSERT INTO `inscripcion`(`idpostulante`, `idvacantes`, `proceso_distin`, `idaula`) VALUES (?,?,?,0);", [
                $request->postulante,
                $request->carrera,
                $request->idproceso,
            ]);
    
            DB::commit();
            return redirect()->route('inscripcion.index')->with('success', 'Agregado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inscripcion.index')->with('error', 'Error al insertar datos: ' . $e->getMessage());
        }
    }
    
    
}
