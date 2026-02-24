<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class semesAcademicoController extends Controller
{
    public function index()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('admin.semesAcade.index', compact('nom_usu'));
    }

    public function savesemestreac(Request $request)
    {
        // dd($request);
        $request->validate([
            'anio' => 'required|integer|digits:4|min:2020|max:2900',
            'periodo' => 'required|string',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'iniciom' => 'required|date',
            'finm' => 'required|date',
        ], [
            'anio.required' => 'El campo año es obligatorio',
            'anio.integer' => 'Debe ser tipo número',
            'anio.digits' => 'Ingrese 4 caracteres',
            'anio.min' => 'Año minimo 2020',
            'anio.max' => 'Año máximo 2900',
            'periodo.required' => 'Seleccione un tipo',

            'inicio.required' => 'El campo Fecha de inicio es obligatorio',
            'fin.required' => 'El campo Fecha de cierre es obligatorio',
            'iniciom.required' => 'El campo Fecha de inicio es obligatorio',
            'finm.required' => 'El campo Fecha límite es obligatorio',
        ]);

        $existe = DB::connection('mysql_segunda')->table('semestre_academico')
            ->where('año', '=', $request->anio)
            ->where('periodo', '=', $request->periodo)->exists();

        if (!$existe) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                if ($request->periodo == 'I') {
                    $tipoCicl = 1;
                } else if ($request->periodo == 'II') {
                    $tipoCicl = 2;
                } else if ($request->periodo == 'Extraordinario') {
                    $tipoCicl = 3;
                }

                //pone todo en estado 0
                $updateEstTodos = DB::connection('mysql_segunda')->table('semestre_academico')->where('estado', '=', 1)->update(['estado' => 0]);
                $updateEstTodosMAtri = DB::connection('mysql_segunda')->table('semestre_academico')->where('estado_matricula', '=', 1)->update(['estado_matricula' => 0]);

                $save = DB::connection('mysql_segunda')->table('semestre_academico')
                    ->insert([
                        'año' => $request->anio,
                        'periodo' => $request->periodo,
                        'fecha_inicio' => $request->inicio,
                        'fecha_fin' => $request->fin,
                        'estado' => 1,
                        'tipo_ciclo' => $tipoCicl,
                        'fecha_ini_matricula' => $request->iniciom,
                        'fecha_fin_matricula' => $request->finm,
                        'estado_matricula' => 1,
                        'fech_inicio_asis' => $request->fech_inicio_asis,
                        'fech_fin_asis' => $request->fech_fin_asis,
                    ]);
                DB::connection('mysql_segunda')->commit();
                return response()->json(['msm' => 'Registrado con éxito', 'icon' => 'success', 'title' => 'Registrado!']);
            } catch (\Throwable $th) {
                DB::connection('mysql_segunda')->rollBack();
                return response()->json(['msm' => 'Hubo un error al momento de registrar el semestre académico.', 'icon' => 'error', 'title' => 'Error!']);
            }
        } else {
            return response()->json(['msm' => 'El año y periodo ingresado, ya a sido registrado anteriormente', 'icon' => 'info', 'title' => 'Alerta!']);
        }
    }

    public function listsemes()
    {
        $semestresaca = DB::connection('mysql_segunda')->table('semestre_academico')->orderBy('idsemestre_academico', 'DESC')->get();
        $data = [];
        foreach ($semestresaca as $value) {
            $data[] = [
                'idsemestre_academico' => $value->idsemestre_academico,
                'año' => $value->año,
                'periodo' => $value->periodo,
                'fecha_inicio' => $value->fecha_inicio,
                'fecha_fin' => $value->fecha_fin,
                'estado' => $value->estado,

                'fecha_iniciom' => $value->fecha_ini_matricula,
                'fecha_finm' => $value->fecha_fin_matricula,
                'estadom' => $value->estado_matricula,

                'fech_inicio_asis' => $value->fech_inicio_asis,
                'fech_fin_asis' => $value->fech_fin_asis,

                'tipo_ciclo' => $value->tipo_ciclo,
                'acciones' => view('admin.semesAcade.butons', [
                    'idsemestre_academico' => $value->idsemestre_academico,
                ])->render()
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function actuEstado(Request $request, $id)
    {
        try {
            DB::connection('mysql_segunda')->beginTransaction();
            $cambiar = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $id)->update([
                'estado' => $request->estado
            ]);
            DB::connection('mysql_segunda')->commit();
            return response()->json(['icon' => 'success', 'msm' => 'Estado cambiado de SEMESTRE ACADEMICO..']);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json(['icon' => 'error', 'msm' => 'Error al cambiar el estado de SEMESTRE ACADEMICO..']);
        }
    }

    public function actuEstadomatricu(Request $request, $id)
    {
        try {
            DB::connection('mysql_segunda')->beginTransaction();
            $cambiar = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $id)->update([
                'estado_matricula' => $request->estadom
            ]);
            DB::connection('mysql_segunda')->commit();
            return response()->json(['icon' => 'success', 'msm' => 'Estado de MATRICULA cambiado..']);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json(['icon' => 'error', 'msm' => 'Error al cambiar el estado de MATRICULA..']);
        }
    }

    public function eliminarSemes($id)
    {

        // dd($delete[0]->matri);
        try {
            $elimi = false;
            DB::connection('mysql_segunda')->beginTransaction();
            $cantDatos = DB::connection('mysql_segunda')->select('
                            SELECT COUNT(idmatricula) AS matri FROM matricula AS m
                            INNER JOIN semestre_academico AS sa ON m.idsemestre_academico = sa.idsemestre_academico
                            WHERE sa.idsemestre_academico = ?;', [$id]);
            if ($cantDatos[0]->matri === 0) {
                $delete = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $id)->delete();
                $elimi = true;
            }
            DB::connection('mysql_segunda')->commit();
            if ($elimi === true) {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'Eliminado!',
                    'msm' => 'Eliminado con éxito'
                ]);
            } elseif ($elimi === false) {
                return response()->json([
                    'icon' => 'info',
                    'title' => 'Alerta!',
                    'msm' => 'El semestre académico no se puede eliminar, porque tiene ' . $cantDatos[0]->matri . ' matriculas asignadas'
                ]);
            }
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json([
                'icon' => 'error',
                'title' => 'Error!',
                'msm' => 'Error al eliminar...'
            ]);
        }
    }

    public function verEditarSemes($id)
    {
        $verEditSe = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $id)->get();
        return response()->json(['verEditSe' => $verEditSe]);
    }

    public function actualizarSemes(Request $request)
    {
        // dd($request);
        $message = [
            'anio2.required' => 'El campo año es obligatorio',
            'anio2.integer' => 'Debe ser tipo número',
            'anio2.digits' => 'Ingrese 4 caracteres',
            'anio2.min' => 'Año minimo 2020',
            'anio2.max' => 'Año máximo 2900',
            'periodo2.required' => 'Seleccione un tipo',

            'inicio2.required' => 'El campo Fecha de inicio es obligatorio',
            'fin2.required' => 'El campo Fecha de cierre es obligatorio',
            'iniciom2.required' => 'El campo Fecha de inicio es obligatorio',
            'finm2.required' => 'El campo Fecha límite es obligatorio',
        ];
        $request->validate([
            'anio2' => 'required|integer|digits:4|min:2020|max:2900',
            'periodo2' => 'required|string',
            'inicio2' => 'required|date',
            'fin2' => 'required|date',
            'iniciom2' => 'required|date',
            'finm2' => 'required|date',
        ], $message);

        try {
            DB::connection('mysql_segunda')->beginTransaction();
            if ($request->periodo2 == 'I') {
                $tipoCicl = 1;
            } else if ($request->periodo2 == 'II') {
                $tipoCicl = 2;
            } else if ($request->periodo2 == 'Extraordinario') {
                $tipoCicl = 3;
            }
            $save = DB::connection('mysql_segunda')->table('semestre_academico')->where('idsemestre_academico', $request->var_ididsemestre_academico)
                ->update([
                    'año' => $request->anio2,
                    'periodo' => $request->periodo2,
                    'fecha_inicio' => $request->inicio2,
                    'fecha_fin' => $request->fin2,
                    'tipo_ciclo' => $tipoCicl,
                    'fecha_ini_matricula' => $request->iniciom2,
                    'fecha_fin_matricula' => $request->finm2,

                    'fech_inicio_asis' => $request->fech_inicio_asise,
                    'fech_fin_asis' => $request->fech_fin_asise,

                ]);
            DB::connection('mysql_segunda')->commit();
            return response()->json(['msm' => 'Datos actualizados']);
        } catch (\Throwable $th) {
            DB::connection('mysql_segunda')->rollBack();
            return response()->json(['msm' => 'Error al actualizar']);
        }
    }
}
