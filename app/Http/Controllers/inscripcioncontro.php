<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\InscritosExport;
use Maatwebsite\Excel\Facades\Excel;


class inscripcioncontro extends Controller
{
    public function index()
    {
        $postulante = DB::select('SELECT * FROM postulante order by apellidos_pater_postulante ASC');
        return view('inscripcion.index')->with('postulante', $postulante);
    }

    public function agregarInscripcion(Request $request)
    {

        //         $request->validate([
        //     'postulante' => 'required|integer|exists:postulante,idpostulante',
        //     'carrera' => 'required|integer|exists:vacantes,idvacantes',
        //     'idproceso' => 'required|integer',
        //     'modalidad' => 'required|integer',
        //     'boleta' => 'required|string|max:50',
        // ]);
        $documentoDni = $request->has('documento_dni') ? 1 : 0;
        $documentoCertificado = $request->has('documento_certificado') ? 1 : 0;

        try {
            // Buscar si ya existe una inscripción con el mismo postulante y vacante
            $existeInscripcion = DB::table('inscripcion')
                ->where('idpostulante', $request->postulante)
                ->where('proceso_distin', $request->idproceso)
                ->where('modalidad_distin', $request->modalidad)
                ->exists();

            if ($existeInscripcion) {
                return redirect()->route('inscripcion.index')->with('error', 'Usted ya está inscrito a este proceso.');
            }

            DB::beginTransaction();

            $agregarInscrito = DB::insert("INSERT INTO `inscripcion`
    (`idpostulante`, `idvacantes`, `proceso_distin`, `modalidad_distin`, `idaula`, `Fecha_inscripcion`, `documento_dni`, `documento_certificado`, `boleta`)
    VALUES (?,?,?,?,0,?,?,?,?)", [
                $request->postulante,
                $request->carrera,
                $request->idproceso,
                $request->modalidad,
                now(),
                $documentoDni,
                $documentoCertificado,
                $request->boleta
            ]);


            DB::commit();
            return redirect()->route('inscripcion.index')->with('success', 'Agregado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inscripcion.index')->with('error', 'Error al insertar datos: ' . $e->getMessage());
        }
    }

    public function cambiar(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();
            $query = DB::update('update inscripcion set idvacantes = ? where idinscripcion = ?', [
                $request->carrera,
                $request->id_inscrip,
            ]);
            // dd($proceso);
            DB::commit();
            return redirect()->back()->with('success', 'Postulante cambiado con éxito');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Postulante no cambiado');
        }
    }

    public function eliminarInscrip(Request $request)
    {
        try {
            DB::beginTransaction();
                $elimiInscri = DB::table('inscripcion')->where('idinscripcion', '=', $request->id_inscrip)->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Postulante Eliminado de la inscripcion');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->back()->with('error', 'Postulante no eliminado');
        }
    }

    public function buscarPostul(Request $request)
    {
        $busqueda = $request->get('q');

        if (!empty($busqueda)) {
            $postu = DB::table('postulante')
                ->select('idpostulante', DB::raw("CONCAT(idpostulante, ' - ', apellidos_pater_postulante, ' ', apellidos_mater_postulante, ' ', nombres_postulante) AS texto"))
                ->where(function ($query) use ($busqueda) {
                    $query->where('idpostulante', 'LIKE', "%{$busqueda}%")
                        ->orWhereRaw("CONCAT(apellidos_pater_postulante, ' ', apellidos_mater_postulante, ' ', nombres_postulante) LIKE ?", ["%{$busqueda}%"]);
                })
                ->get();
        } else {
            $postu = collect();
        }
        return response()->json($postu);
    }


    public function exportarExcel($idProceso, $idModalidad)
    {
        return Excel::download(new InscritosExport($idProceso, $idModalidad), 'inscritos.xlsx');
    }
}
