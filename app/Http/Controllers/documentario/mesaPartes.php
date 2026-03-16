<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use setasign\Fpdi\Fpdi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\Datatables;
use Illuminate\Support\Facades\Auth;
use App\Events\DocumentoRecibido;
use App\Events\editarDocumento;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\select;

class mesaPartes extends Controller
{
    public function index()
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
            return view('mesaPartes.index', compact('dependencias', 'detalle_tramite', 'id_depen', 'rol', 'id_usuTrabajador', 'cont_est'));
        } catch (\Throwable $th) {
            return view('mesaPartes.index', compact('dependencias'));
        }
    }

    public function emitidos($idtipo_docu, $emisor)
    {
        $query_emitidos = DB::connection('mysql_documentario')->select('SELECT documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto, documentos.folio, movi_est.id_estado FROM documentos
        INNER JOIN (SELECT movimiento.iddocumentos as movi_iddocu, MAX(idestado) AS id_estado FROM `movimiento` GROUP BY movi_iddocu ) AS movi_est ON documentos.iddocumentos = movi_est.movi_iddocu
        WHERE idtipo_documento = ? AND emisor = ? GROUP BY documentos.iddocumentos, documentos.numero_de_exp, documentos.fecha_ingreso, documentos.asunto, documentos.folio, movi_est.id_estado ORDER BY documentos.iddocumentos DESC;', [
            $idtipo_docu,
            $emisor
        ]);

        if (request()->ajax()) {
            return datatables::of($query_emitidos)->addColumn('btn', 'mesaPartes.butons')->rawColumns(['btn'])->toJson();
        }
    }

    public function num_tipo_documento_expe($id_tipo_docu, $emisor)
    {
        try {
            DB::beginTransaction();
            $filtraTipoDocu = DB::connection('mysql_documentario')->table('documentos')
                ->select('iddocumentos', 'numero_de_exp', 'fecha_ingreso', 'emisor',)
                ->where('idtipo_documento', $id_tipo_docu)
                ->where('emisor', $emisor)
                ->orderBy('numero_de_exp', 'DESC')
                ->first();
            DB::commit();
            return response()->json($filtraTipoDocu);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function buscarUsuario(Request $request)
    {
        $busqueda = $request->get('q');

        $usu = DB::connection('mysql_documentario')->table('usuario')
            ->where('dniusuario', 'like', "%{$busqueda}%")
            ->orWhere('nombres', 'like', "%{$busqueda}%")
            ->limit(10)
            ->get(['idusuario', 'nombres as text', 'dniusuario']);

        return response()->json($usu);
    }

    public function traerDepen()
    {
        $usuario = Auth::user();
        $id_depen = session('dependencia_id');
        $depens = DB::connection('mysql_documentario')->table('dependencias')->whereNotIn('iddependencias', [1, $id_depen])->get();
        return response()->json($depens);
    }

    public function registrarDocu(Request $request)
    {
        // dd($request);

        // dd($request->dependencia_enviar);
        // $usuario = Auth::user();
        // $depen = session('dependencia_id');
        // $rol = DB::table('dependencias')->where('iddependencias', $depen)->first();
        // $tokenUsuario = DB::table('dependencia_user')
        //     ->where('user_id', $request->id_usuTrabajador)
        //     ->where('dependencia_id', $depen)
        //     ->value('token');
        // $paginaFirma = intval($request->pagina_firma); // Convertimos en entero para más seguridad


        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $firmasJson = json_decode($request->firmas_json, true);
        $usuarioFirmante = DB::table('users')->where('id', $request->id_usuTrabajador)->first();
        //         dd($usuarioFirmante);
        // $nombre = $usuarioFirmante->name;
        $dni = $usuarioFirmante->dni;
        $motivo = "Autor del documento";
        $fecha = now()->format('d/m/Y H:i:s') . ' -0500';
        $pathFirma = public_path('firma_pdf/logo_firma_pedagogico.png');

        $reglas = [
            'tipo_documento' => 'required|not_in:0',
            'asunto' => 'required|string',
            'folio' => 'required|not_in:0',
        ];

        //'usuario' => 'required|not_in:0',
        //'usuario.required' => 'El campo usuario es obligatorio.',
        //'usuario.not_in' => 'Debes seleccionar un usuario.',


        //'para_su' => 'required|not_in:0',
        // 'para_su.required' => 'El campo es obligatorio.',
        // 'para_su.not_in' => 'Debes seleccionar un campo.',

        $mensajes = [
            'tipo_documento.required' => 'El campo tipo de documento es obligatorio.',
            'tipo_documento.not_in' => 'Debes seleccionar un tipo de documento',

            'asunto.required' => 'El campo asunto es obligatorio.',
            'folio.required' => 'Es obligatorio.',
            'folio.not_in' => 'Debe ser diferente de 0.',

            'dependencia_enviar.required' => 'La dependencia de envío es obligatoria.',
            'dependencia_enviar.not_in' => 'Debes seleccionar una dependencia.',
        ];

        if ($request->has('todasDepenSelects')) { //si selec varias depen
            $validarData = $request->validate($reglas, $mensajes);

            try {
                DB::beginTransaction();
                $query_ultimo_iddocu = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                    'numero_de_exp' => $request->num_expe,
                    'fecha_ingreso' =>  $request->fecha_actual,
                    'asunto' => $request->asunto,
                    'folio' =>  $request->folio,
                    'idtipo_documento' => $request->tipo_documento,
                    'emisor' => $request->emisor,
                    'id_user' => $request->id_usuTrabajador,
                    'iddetalle_tramite' => $request->para_su,
                    'idusuario' => $request->usuario,
                    'recomendacion' => $request->Recomendaciones,
                    'estado_actu' => 1,
                ]);

                $todasDepens = DB::connection('mysql_documentario')->table('dependencias')->select('iddependencias')->get();

                foreach ($todasDepens as $id_depenss) {
                    if (($id_depenss->iddependencias != 1) && ($id_depenss->iddependencias != $request->emisor)) {
                        DB::connection('mysql_documentario')->table('movimiento')->insert([
                            'iddocumentos' => $query_ultimo_iddocu,
                            'iddependencias_emior' => $request->emisor,
                            'iddependencias_receptor' => $id_depenss->iddependencias,
                            'fecha_de_envio' => $request->fecha_actual,
                            'idestado' => 1
                        ]);
                    }
                }
                DB::commit();

                foreach ($todasDepens as $id_depenss) {
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                            FROM estado
                                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                            AND movimiento.iddependencias_receptor = ?
                                            WHERE estado.idestado IN (1,2,3)
                                            GROUP BY estado.idestado;', [$id_depenss->iddependencias]);

                    // \Log::info('Voy a emitir el evento para dependencia: ' . $id_depenss->iddependencias, $cont_estados);
                    event(new DocumentoRecibido($id_depenss->iddependencias, $cont_estados));
                }
                $nombreArchivo = null;
                $rutaPDF = null;

                if ($request->entregaOpciones == 1) {
                    $nombreArchivo = 'documento_presencial_' . time() . '.pdf';
                    $rutaPDF = public_path("documentos/documentos_director_pdf/$nombreArchivo");

                    $pdf = new Fpdi();
                    $pdf->AddPage();
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->MultiCell(0, 10, "Expediente N° $request->num_expe\nDocumento entregado físicamente.");
                    $pdf->Output('F', $rutaPDF);
                } elseif ($request->hasFile('archivo_virtual')) {
                    $archivo = $request->file('archivo_virtual');
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $rutaPDF = public_path("documentos/documentos_director_pdf/$nombreArchivo");
                    move_uploaded_file($archivo->getPathname(), $rutaPDF);
                }

                // if ($rutaPDF && file_exists($rutaPDF)) {
                //     $nombreFirmado = $this->firmarYGuardarPDF($rutaPDF, $firmasJson, $pathFirma, $nombre, $dni, $motivo, $fecha);

                //     if ($nombreFirmado) {
                //         DB::connection('mysql_documentario')->table('documenpdf')->insert([
                //             'nombre_del_documento' => $nombreFirmado,
                //             'fecha_subida' => now(),
                //             'iddocumentos' => $query_ultimo_iddocu,
                //             'token_pdf' => Str::uuid(),
                //             'tipo_pdf' => $request->entregaOpciones,
                //             'usuario_id' => $request->id_usuTrabajador
                //         ]);
                //     }
                // }


                if ($request->ajax()) {
                    return response()->json(['success' => 'Registrado con éxito!']);
                }
                return redirect()->route('mesapar.index')->with('success', 'Registrado con éxito!');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->route('mesapar.index')->with('error', 'Error al registrar');
            }
            ////////////////////////////////////////////////////////////////////
        } else {

            $reglas['dependencia_enviar'] = 'required|not_in:0';
            $validarData = $request->validate($reglas, $mensajes);

            try {
                DB::beginTransaction();
                $query_ultimo_iddocu = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                    'numero_de_exp' => $request->num_expe,
                    'fecha_ingreso' =>  $request->fecha_actual,
                    'asunto' => $request->asunto,
                    'folio' =>  $request->folio,
                    'idtipo_documento' => $request->tipo_documento,
                    'emisor' => $request->emisor,
                    'id_user' => $request->id_usuTrabajador,
                    'iddetalle_tramite' => $request->para_su,
                    'idusuario' => $request->usuario,
                    'recomendacion' => $request->Recomendaciones,
                    'estado_actu' => 1,
                ]);

                // dd($query_ultimo_iddocu);

                foreach ($request->dependencia_enviar as $id_depenss) {
                    if (($id_depenss != 1) && ($id_depenss != $request->emisor)) {
                        DB::connection('mysql_documentario')->table('movimiento')->insert([
                            'iddocumentos' => $query_ultimo_iddocu,
                            'iddependencias_emior' => $request->emisor,
                            'iddependencias_receptor' => $id_depenss,
                            'fecha_de_envio' => $request->fecha_actual,
                            'idestado' => 1
                        ]);
                    }
                }
                DB::commit();
                $documento = DB::connection('mysql_documentario')->table('documentos')
                    ->where('iddocumentos', $query_ultimo_iddocu)
                    ->first();

                foreach ($request->dependencia_enviar as $depp) {
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                            FROM estado
                                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                            AND movimiento.iddependencias_receptor = ?
                                            WHERE estado.idestado IN (1,2,3)
                                            GROUP BY estado.idestado;', [$depp]);

                    // \Log::info('Voy a emitir el evento para dependencia: ' . $request->dependencia_enviar, $cont_estados);
                    event(new DocumentoRecibido($depp, $cont_estados));
                }
                $nombreArchivo = null;
                $rutaPDF = null;

                if ($request->entregaOpciones == 1) {
                    $nombreArchivo = 'documento_presencial_' . time() . '.pdf';
                    $rutaPDF = public_path("documentos/documentos_director_pdf/$nombreArchivo");

                    $pdf = new Fpdi();
                    $pdf->AddPage();
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->MultiCell(0, 10, "Expediente N° $request->num_expe\nDocumento entregado físicamente.");
                    $pdf->Output('F', $rutaPDF);
                } elseif ($request->hasFile('archivo_virtual')) {
                    $archivo = $request->file('archivo_virtual');
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $rutaPDF = public_path("documentos/documentos_director_pdf/$nombreArchivo");
                    move_uploaded_file($archivo->getPathname(), $rutaPDF);
                }

                // if ($rutaPDF && file_exists($rutaPDF)) {
                //     $nombreFirmado = $this->firmarYGuardarPDF($rutaPDF, $firmasJson, $pathFirma, $nombre, $dni, $motivo, $fecha);

                //     if ($nombreFirmado) {
                //         DB::connection('mysql_documentario')->table('documenpdf')->insert([
                //             'nombre_del_documento' => $nombreFirmado,
                //             'fecha_subida' => now(),
                //             'iddocumentos' => $query_ultimo_iddocu,
                //             'token_pdf' => Str::uuid(),
                //             'tipo_pdf' => $request->entregaOpciones,
                //             'usuario_id' => $request->id_usuTrabajador
                //         ]);
                //     }
                // }


                if ($request->ajax()) {
                    return response()->json(['success' => 'Registrado con éxito!']);
                }
                return redirect()->route('documentario.mesapar.index')->with('success', 'Registrado con éxito!');
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->route('documentario.mesapar.index')->with('error', 'Error al registrar');
            }
        }
    }


    public function showEmitido($id)
    {
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

            $queryDoc = DB::connection('mysql_documentario')->table('documentos')
                ->where('iddocumentos', '=', $id)
                ->first();

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

            $usu = DB::connection('mysql_documentario')->table('usuario')
                ->where('idusuario', '=', $queryDoc->idusuario)
                ->first();
            DB::commit();
            return view('mesaPartes.showEmitido')->with('dependencias', $dependencias)->with('detalle_tramite', $detalle_tramite)->with('queryDoc', $queryDoc)->with('queryMovi', $queryMovi)->with('dependenciasSelect', $dependenciasSelect)->with('estadoMayor', $estadoMayor)->with('queryRecepDepenFech', $queryRecepDepenFech)->with('usu', $usu)->with('rol', $rol)->with('id_depen', $id_depen)->with('cont_est', $cont_est)->with('pdfdocumento', $pdfdocumento);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return view('mesaPartes.index');
        }
    }

    public function updateDocuEmi(Request $request)
    {
        try {
            DB::beginTransaction();
            $updateDocuEmi = DB::connection('mysql_documentario')->update('UPDATE documentos SET numero_de_exp = ?,  asunto = ?, folio = ?, idtipo_documento = ?, iddetalle_tramite = ?, idusuario = ?, recomendacion = ? WHERE iddocumentos  = ?', [
                $request->num_expe,
                $request->asunto,
                $request->folio,
                $request->tipo_documento,
                $request->para_su,
                $request->usuario,
                $request->Recomendaciones,
                $request->iddocumentos
            ]);

            $moviConEseDoc = DB::connection('mysql_documentario')->table('movimiento')
                ->where('iddocumentos', $request->iddocumentos)
                ->pluck('iddependencias_receptor')
                ->toArray();

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

                    event(new editarDocumento($depe, $cont_estadosre)); //lo q tenia
                }
            }

            if (!empty($agrego)) {
                foreach ($agrego as $depa) {
                    DB::connection('mysql_documentario')->insert('INSERT INTO movimiento (iddocumentos, iddependencias_emior, iddependencias_receptor, fecha_de_envio, idestado) values (?, ?, ?, ?, ?)', [$request->iddocumentos, $request->iddependencias_emior, $depa, now(), 1]);

                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                    FROM estado
                    LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                    AND movimiento.iddependencias_receptor = ?
                    WHERE estado.idestado IN (1,2,3)
                    GROUP BY estado.idestado;', [$depa]);

                    event(new DocumentoRecibido($depa, $cont_estados)); //lo q va tener
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

                    event(new DocumentoRecibido($depa, $cont_estados)); //lo q va tener
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


    private function firmarYGuardarPDF(string $rutaPDFOriginal, array $firmasJson, string $pathFirma, string $nombre, string $dni, string $motivo, string $fecha): ?string
    {
        if (!file_exists($rutaPDFOriginal)) {
            \Log::warning("⚠️ Archivo original no encontrado: $rutaPDFOriginal");
            return null;
        }

        try {
            $pdf = new \setasign\Fpdi\Fpdi();
            $pdf->SetAutoPageBreak(false);

            $pageCount = $pdf->setSourceFile($rutaPDFOriginal);
            $nombreFirmado = str_replace('.pdf', '_firmado.pdf', basename($rutaPDFOriginal));
            $rutaFirmada = dirname($rutaPDFOriginal) . '/' . $nombreFirmado;

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pdf->AddPage();
                $templateId = $pdf->importPage($pageNo);
                $pdf->useTemplate($templateId, 0, 0, 210);

                $clavePagina = (string)$pageNo;
                if (isset($firmasJson[$clavePagina])) {
                    foreach ($firmasJson[$clavePagina] as $firmaCoord) {
                        $x = floatval($firmaCoord['x']);
                        $y = floatval($firmaCoord['y']);

                        $pdf->SetFillColor(255, 255, 255); // blanco suave (casi invisible)
                        $pdf->Rect($x, $y, 90, 30, 'F');

                        // Dibujar borde visible
                        $pdf->SetDrawColor(100, 100, 100); // gris oscuro
                        $pdf->SetLineWidth(0.3); // grosor del borde
                        $pdf->Rect($x, $y, 90, 30, 'D'); // solo borde, sin relleno

                        if (file_exists($pathFirma)) {
                            $pdf->Image($pathFirma, $x + 2, $y + 2, 20);
                        } else {
                            \Log::warning("⚠️ Imagen de firma no encontrada en: $pathFirma");
                        }

                        $pdf->SetFont('Helvetica', '', 7);
                        $pdf->SetTextColor(50, 50, 50); // gris oscuro
                        $pdf->SetXY($x + 24, $y + 2);
                        $pdf->MultiCell(65, 4, utf8_decode("Firmado digitalmente por:\n$nombre\nDNI: $dni\n$motivo\n$fecha"));
                    }
                }
            }

            $pdf->Output('F', $rutaFirmada);
            \Log::info("✅ PDF firmado guardado en: $rutaFirmada");
            unlink($rutaPDFOriginal); // Elimina el original sin firmar
            return $nombreFirmado; // Devuelve el nombre final
        } catch (\Throwable $e) {
            \Log::error("❌ Error al firmar PDF: " . $e->getMessage());
            return null;
        }
    }
}

