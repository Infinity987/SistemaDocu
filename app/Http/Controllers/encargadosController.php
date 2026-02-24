<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class encargadosController extends Controller
{
    public function index()
    {
        $docentes = DB::connection('mysql_segunda')->table('userprofile')->orderBy('id_users', 'desc')->get();
        return view('admin.encargados.index', compact('docentes'));
    }

    public function guardarEncargado(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->hasFile('logo')) {
                // El usuario subió una nueva logo
                $firma = $request->file('logo');
                $nombreArchivo = $request->año_logo . '.png';
                $rutaDestino = public_path('logos/' . $nombreArchivo);

                if (file_exists($rutaDestino)) {
                    unlink($rutaDestino);
                }

                $firma->move(public_path('logos'), $nombreArchivo);
                $firmaPath = 'logos/' . $nombreArchivo;
            } else {
                // No subió nueva firma, usar la actual
                $firmaPath = $request->firma_actual;
            }

            $updateEstados = DB::connection('mysql_segunda')->table('encargados')->where('estado', 1)->update(['estado' => 0]);
            $insert = DB::connection('mysql_segunda')->table('encargados')
                ->insert([
                    'iduserProfile_direc' => $request->iduserProfile_direc,
                    'reso_direc' => $request->reso_direc,
                    'iduserProfile_secre' => $request->iduserProfile_secre,
                    'año_logo' => $request->año_logo,
                    'logo' => $firmaPath,
                    'estado' => true
                ]);

            DB::commit();
            return redirect()->back()->with('alert', [
                'icon' => 'success',
                'title' => 'Encargados guardados correctamente',
                'text' => 'La información fue registrada sin errores.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('alert', [
                'icon' => 'error',
                'title' => 'Error al guardar encargados',
                'text' => 'Ocurrió un problema al registrar los datos.'
            ]);
        }
    }

    public function ajaxEncargados()
    {
        $ajaxencar = DB::connection('mysql_segunda')->select('SELECT
            e.idencargados, upd.nombre as direc, e.reso_direc, e.reso_direc, ups.nombre as secre, e.año_logo, e.estado FROM encargados e
            INNER JOIN userprofile upd ON e.iduserProfile_direc = upd.iduserProfile
            INNER JOIN userprofile ups ON e.iduserProfile_secre = ups.iduserProfile ORDER BY e.idencargados DESC;');

        return DataTables::of($ajaxencar)->make(true);
    }

    public function ajaxactualizarEstado(Request $request, $estado)
    {
        if (!$request->id) {
            return response()->json(['error' => 'ID no recibido'], 400);
        }
        $estado = filter_var($estado, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        try {
            DB::beginTransaction();
            $update = DB::connection('mysql_segunda')->table('encargados')->where('idencargados', $request->id)->update([
                'estado' => $estado
            ]);
            DB::commit();
            return response()->json(['mensaje' => 'Estado actualizado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['mensaje' => 'Error al actualizar estado ...']);
        }
        // dd($request);
    }

    public function gedDataEncargado($id)
    {
        $getData = DB::connection('mysql_segunda')->table('encargados')->where('idencargados', $id)->first();
        if (!$getData) {
            return response()->json(['error' => 'Encargado no encontrado'], 404);
        }
        $getData->url = asset($getData->logo);
        return response()->json($getData);
    }

    public function editarEncargado(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->idencargado_edit;
            $nuevoAño = $request->año_logo_edit;
            $añoAntiguo = $request->año_edit_antigu;
            $logoAntiguo = $request->logo_edit_antigu;
            $nuevoNombreLogo = "logos/{$nuevoAño}.png";

            // Ruta completa
            $rutaNuevoLogo = public_path($nuevoNombreLogo);
            $rutaLogoAntiguo = public_path($logoAntiguo);

            // Si se subió un nuevo archivo
            if ($request->hasFile('logo_edit')) {
                // Eliminar logo anterior si existe
                if ($logoAntiguo && File::exists($rutaLogoAntiguo)) {
                    File::delete($rutaLogoAntiguo);
                }

                // Guardar nuevo logo con nombre basado en el año
                $request->file('logo_edit')->move(public_path('logos'), "{$nuevoAño}.png");
            } else {
                // Si solo cambió el año, renombrar el archivo
                if ($nuevoAño !== $añoAntiguo && File::exists($rutaLogoAntiguo)) {
                    File::move($rutaLogoAntiguo, $rutaNuevoLogo);
                }
            }

            // Actualizar en la base de datos
            DB::connection('mysql_segunda')->table('encargados')
                ->where('idencargados', $id)
                ->update([
                    'iduserProfile_direc' => $request->iduserProfile_direc_edit,
                    'reso_direc' => $request->reso_direc_edit,
                    'iduserProfile_secre' => $request->iduserProfile_secre_edit,
                    'año_logo' => $nuevoAño,
                    'logo' => $nuevoNombreLogo
                ]);

            DB::commit();

            return redirect()->back()->with('success', 'Encargado actualizado correctamente.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el encargado.');
        }
    }

    public function eliminarEncargados($id)
    {
        $encargado = DB::connection('mysql_segunda')->table('encargados')->where('idencargados', $id)->first();
        if (!$encargado) {
            return response()->json(['mensaje' => 'Encargado no encontrado'], 404);
        }

        // Eliminar logo si existe
        if ($encargado->logo && File::exists(public_path($encargado->logo))) {
            File::delete(public_path($encargado->logo));
        }

        DB::connection('mysql_segunda')->table('encargados')->where('idencargados', $id)->delete();

        return response()->json(['mensaje' => 'Encargado eliminado correctamente']);
    }
}
