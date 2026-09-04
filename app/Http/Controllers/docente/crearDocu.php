<?php

namespace App\Http\Controllers\docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\Datatables;
use App\Events\DocumentoRecibido;
use App\Events\editarDocumento;

class crearDocu extends Controller
{
    public function creardocu()
    {
        try {
            $usuario = Auth::user()->dni;
            $id_depen = session('dependencia_id');
            // dd($id_depen);
            $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();
            $id_usuTrabajador = Auth::user()->id;

            $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);
            $dependencias = DB::connection('mysql_documentario')->table('dependencias')->get();
            $detalle_tramite = DB::connection('mysql_documentario')->table('detalle_tramite')->get();
            return view('docente.documentario.indexCrear', compact('dependencias', 'detalle_tramite', 'id_depen', 'rol', 'id_usuTrabajador', 'cont_est'));
        } catch (\Throwable $th) {
            return view('docente.documentario.indexCrear', compact('dependencias'));
        }
    }

    public function emitidos($idtipo_docu, $emisor)
    {
        $id_user = Auth::user()->id;
        $query_emitidos = DB::connection('mysql_documentario')->select('SELECT
            documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto,
            documentos.folio, movi_est.id_estado, dependencias.nombre_dependencia
            FROM documentos
            INNER JOIN (SELECT movimiento.iddocumentos as movi_iddocu, MAX(idestado) AS id_estado, movimiento.iddependencias_receptor AS iddependencias_receptor
            FROM `movimiento` GROUP BY movi_iddocu, iddependencias_receptor) AS movi_est ON documentos.iddocumentos = movi_est.movi_iddocu
            left join dependencias ON movi_est.iddependencias_receptor = dependencias.iddependencias
            WHERE idtipo_documento = ? AND emisor = ? AND est_firma = ? AND id_user = ?
            GROUP BY documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto, documentos.folio, movi_est.id_estado, dependencias.nombre_dependencia ORDER BY documentos.iddocumentos DESC;', [
            $idtipo_docu,
            $emisor,
            1,
            $id_user
        ]);

        if (request()->ajax()) {
            return datatables::of($query_emitidos)->addColumn('btn', 'docente.documentario.butons')->rawColumns(['btn'])->toJson();
        }
    }

    public function showEmitido($id)
    {
        // dd($id);
        $usuario = Auth::user();
        $id_depen = session('dependencia_id');
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();
        try {
            DB::beginTransaction();
            $dependencias = DB::connection('mysql_documentario')->table('dependencias')->get();
            $detalle_tramite = DB::connection('mysql_documentario')->table('detalle_tramite')->get();

            $queryDoc = DB::connection('mysql_documentario')->table('documentos AS d')
                ->leftJoin('usuario AS u', 'd.idusuario', '=', 'u.idusuario')
                ->leftJoin('entidades_externas AS e', 'd.id_entidad_externa', '=', 'e.id')
                ->where('iddocumentos', '=', $id)
                ->select('d.*', 'u.nombres as nombre_persona', 'e.razon_social_nombre as nombre_juridico')
                ->first();
            // dd($queryDoc);

            $queryMovi = DB::connection('mysql_documentario')->table('movimiento')
                ->select('idmovimiento', 'iddependencias_emior', 'iddependencias_receptor', 'fecha_de_recepcion', 'idestado')
                ->where('iddocumentos', '=', $id)
                ->first();

            $queryRecepDepenFech = DB::connection('mysql_documentario')->table('movimiento')
                ->select('iddependencias_receptor', 'nombre_dependencia', 'fecha_de_recepcion', 'idestado')
                ->join('dependencias', 'movimiento.iddependencias_receptor', '=', 'dependencias.iddependencias')
                ->where('iddocumentos', '=', $id)
                ->get();

            $estadoMayor = DB::connection('mysql_documentario')->table('movimiento')
                ->selectRaw('MAX(idestado) as idestado')
                ->where('iddocumentos', $id)
                ->groupBy('iddocumentos')
                ->first();
            $pdfdocumento = DB::connection('mysql_documentario')->table('documenpdf')
                ->where('iddocumentos', $id)
                ->get();


            $dependenciasSelect = DB::connection('mysql_documentario')->table('movimiento')
                ->where('iddocumentos', $id)
                ->pluck('iddependencias_receptor')
                ->toArray();

            if (in_array(2, $dependenciasSelect)) {
                $listDocentes = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $id)
                    ->pluck('id_user_receptor')
                    ->toArray();

                $docentesSeleccionados = DB::connection('mysql_segunda')
                    ->table('userprofile') // o la tabla que uses para nombres
                    ->whereIn('id_users', $listDocentes)
                    ->select('id_users as id', 'nombre as text')
                    ->get();
            } else {
                $docentesSeleccionados = collect();
            }

            $usu = DB::connection('mysql_documentario')->table('usuario')
                ->where('idusuario', '=', $queryDoc->idusuario)
                ->first();
            DB::commit();
            return view('docente.documentario.showEmitido')->with('dependencias', $dependencias)->with('detalle_tramite', $detalle_tramite)->with('queryDoc', $queryDoc)->with('queryMovi', $queryMovi)->with('dependenciasSelect', $dependenciasSelect)->with('estadoMayor', $estadoMayor)->with('queryRecepDepenFech', $queryRecepDepenFech)->with('usu', $usu)->with('rol', $rol)->with('id_depen', $id_depen)->with('cont_est', $cont_est)->with('pdfdocumento', $pdfdocumento)->with('docentesSeleccionados', $docentesSeleccionados);
        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th);
            return view('docente.documentario.index');
        }
    }

    public function updateDocuEmi(Request $request, $iddocumentos)
    {
        // dd($request);
        $rules = [
            'tipo_documento' => 'required|not_in:0',
            'folio'          => 'required|numeric|min:1',
            'asunto'         => 'required|string|min:1',
            'para_su'        => 'required|not_in:0',
            'reemplazar_pdf.*' => 'nullable|file|mimes:pdf|max:5120',
        ];

        if (!$request->filled('docentes_especificos')) {
            $rules['dependencia_enviar'] = 'required|array|min:1';
        }

        $messages = [
            'tipo_documento.not_in' => 'Debe seleccionar un tipo de documento válido.',
            'para_su.not_in'        => 'Debe seleccionar una opción en "Para su".',
            'dependencia_enviar.required' => 'Debe seleccionar al menos una dependencia si no hay docentes seleccionados.',
            'reemplazar_pdf.*.mimes' => 'El archivo debe ser un formato PDF válido.',
        ];

        $request->validate($rules, $messages);

        $id_users = Auth::user()->id;

        // Valores por defecto (para otras oficinas)
        $idusuario = null;
        $id_entidad_externa = null;
        $numero_documento_externo = null;

        // Solo si es Mesa de Partes evaluamos el formulario especial
        if (session('dependencia_id') == 24) {
            if ($request->tipo_remitente_m === 'natural') {
                $idusuario = $request->usuario_m;
            } elseif ($request->tipo_remitente_m === 'juridica') {
                $id_entidad_externa = $request->id_entidad_m_externa;
                $numero_documento_externo = $request->numero_documento_externo_m;
            }
        }

        try {
            DB::beginTransaction();
            $updateDocuEmi = DB::connection('mysql_documentario')
                ->update('UPDATE documentos SET numero_de_exp = ?, asunto = ?, folio = ?, idtipo_documento = ?, iddetalle_tramite = ?, recomendacion = ?, anexos_fisicos = ?, idusuario = ?,id_entidad_externa = ?,numero_documento_externo = ? WHERE iddocumentos  = ?', [
                    $request->num_expe,
                    $request->asunto,
                    $request->folio,
                    $request->tipo_documento,
                    $request->para_su,
                    $request->Recomendaciones,
                    $request->detalle_anexos_fisicos,
                    $idusuario,
                    $id_entidad_externa,
                    $numero_documento_externo,
                    $iddocumentos
                ]);

            if ($request->filled('docentes_especificos')) {
                // para eliminar en caso ague el cambio de dependencia a docentes
                $verificar = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)
                    ->where('iddependencias_receptor', '>=', 6)
                    ->exists();

                if ($verificar) {
                    $dependencias_eliminar = DB::connection('mysql_documentario')->table('movimiento')
                        ->where('iddocumentos', $iddocumentos)
                        ->where('iddependencias_receptor', '>=', 6)
                        ->select('idmovimiento', 'iddependencias_receptor')
                        ->get()
                        ->toArray();

                    foreach ($dependencias_eliminar as $id_depen) {
                        $movimientos_doce = DB::connection('mysql_documentario')->table('movimiento')
                            ->where('idmovimiento', $id_depen->idmovimiento)
                            ->delete();

                        $cont_estadosre = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen->iddependencias_receptor]);

                        event(new editarDocumento($id_depen->iddependencias_receptor, $cont_estadosre, 'dependencia'));
                    }
                }

                // para docentes
                $docenteMovi = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $request->iddocumentos)
                    ->where('iddependencias_receptor', 2)
                    ->pluck('id_user_receptor')
                    ->toArray();

                $quito = array_diff($docenteMovi, $request->docentes_especificos);
                $agrego = array_diff($request->docentes_especificos, $docenteMovi);

                if (!empty($quito)) {
                    foreach ($quito as $depe) {
                        DB::connection('mysql_documentario')->delete('DELETE FROM movimiento WHERE iddocumentos = ? AND iddependencias_receptor = ? AND id_user_receptor = ? AND idestado = ?', [$request->iddocumentos, 2, $depe, 1]);

                        $cont_estadosre = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [2, $depe]);

                        event(new editarDocumento($depe, $cont_estadosre, 'personal'));
                    }
                }

                if (!empty($agrego)) {
                    foreach ($agrego as $depa) {
                        DB::connection('mysql_documentario')->insert('INSERT INTO movimiento (iddocumentos, iddependencias_emior, iddependencias_receptor, id_user_receptor, fecha_de_envio, idestado) values (?, ?, ?, ?, ?, ?)', [$request->iddocumentos, $request->emisor, 2, $depa, now(), 1]);

                        $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [2, $depa]);

                        event(new DocumentoRecibido($depa, $cont_estados, 'personal'));
                    }
                }

                if (empty($agrego) && empty($quito)) {
                    foreach ($docenteMovi as $depa) {
                        $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [2, $depa]);

                        event(new DocumentoRecibido($depa, $cont_estados, 'personal'));
                    }
                }
            } elseif ($request->filled('dependencia_enviar')) {
                // para eliminar en caso ague el cambio de docentes a dependencias
                $verificar = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)
                    ->where('iddependencias_receptor', 2)
                    ->exists();

                if ($verificar) {
                    $docentes_eliminar = DB::connection('mysql_documentario')->table('movimiento')
                        ->where('iddocumentos', $iddocumentos)
                        ->where('iddependencias_receptor', 2)
                        ->select('idmovimiento', 'id_user_receptor')
                        ->get()
                        ->toArray();

                    foreach ($docentes_eliminar as $id_user) {
                        $movimientos_doce = DB::connection('mysql_documentario')->table('movimiento')
                            ->where('idmovimiento', $id_user->idmovimiento)
                            ->delete();

                        $cont_estadosre = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [2, $id_user->id_user_receptor]);

                        event(new editarDocumento($id_user->id_user_receptor, $cont_estadosre, 'personal'));
                    }
                }

                // para dependencias
                $moviConEseDoc = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $request->iddocumentos)
                    ->pluck('iddependencias_receptor')
                    ->toArray();

                // Caso 2: Se seleccionó dependencia
                $quito = array_diff($moviConEseDoc, $request->dependencia_enviar);
                $agrego = array_diff($request->dependencia_enviar, $moviConEseDoc);

                if (!empty($quito)) {
                    foreach ($quito as $depe) {
                        DB::connection('mysql_documentario')->delete('DELETE FROM movimiento WHERE iddocumentos = ? AND iddependencias_receptor = ? AND idestado = ?', [$request->iddocumentos, $depe, 1]);

                        $cont_estadosre = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depe]);

                        event(new editarDocumento($depe, $cont_estadosre, 'dependencia')); //lo q tenia
                    }
                }

                if (!empty($agrego)) {
                    foreach ($agrego as $depa) {
                        DB::connection('mysql_documentario')->insert('INSERT INTO movimiento (iddocumentos, iddependencias_emior, iddependencias_receptor, fecha_de_envio, idestado) values (?, ?, ?, ?, ?)', [$request->iddocumentos, $request->emisor, $depa, now(), 1]);

                        $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depa]);

                        event(new DocumentoRecibido($depa, $cont_estados, 'dependencia')); //lo q va tener
                    }
                }

                if (empty($agrego) && empty($quito)) {
                    foreach ($moviConEseDoc as $depa) {
                        $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depa]);

                        event(new DocumentoRecibido($depa, $cont_estados, 'dependencia')); //lo q va tener
                    }
                }
            }

            //para pdf actualizar
            // Verificamos si se enviaron archivos para reemplazar
            if ($request->hasFile('reemplazar_pdf')) {

                foreach ($request->file('reemplazar_pdf') as $idPdf => $nuevoArchivo) {

                    if ($nuevoArchivo->isValid()) {

                        // 1. Buscamos los datos del PDF actual en la BD para saber el nombre del archivo viejo
                        $pdfActual = DB::connection('mysql_documentario')
                            ->table('documenpdf')
                            ->where('iddocumenpdf', $idPdf)
                            ->first();

                        if ($pdfActual) {
                            $rutaCarpeta = public_path('documentos/documentos_director_pdf/');
                            $nombreArchivoViejo = $pdfActual->nombre_del_documento;

                            // 2. Borramos el archivo físico antiguo si existe
                            if (file_exists($rutaCarpeta . $nombreArchivoViejo)) {
                                unlink($rutaCarpeta . $nombreArchivoViejo);
                            }

                            // 3. Preparamos el nuevo nombre (evitamos duplicados con time())
                            $extension = $nuevoArchivo->getClientOriginalExtension();
                            $nuevoNombre = $id_users . '_' . session('dependencia_id') . '_' . time() . '_' . uniqid() . '.' . $extension;


                            // 4. Movemos el archivo a la carpeta del servidor
                            $nuevoArchivo->move($rutaCarpeta, $nuevoNombre);

                            // 5. Actualizamos el nombre en la base de datos
                            DB::connection('mysql_documentario')
                                ->table('documenpdf')
                                ->where('iddocumenpdf', $idPdf)
                                ->update([
                                    'nombre_del_documento' => $nuevoNombre,
                                    'fecha_actualizacion' => now(),
                                ]);
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Actualizado con éxito');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar los datos');
        }
    }
}
