<?php

namespace App\Http\Controllers\documentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\Datatables;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\DocumentoRecibido;


class enviardocumentos extends Controller
{
    public function solucionar($id)
    {
        // dd($id);
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::connection('mysql_documentario')->select('SELECT * FROM dependencias');
        $detalledocumento = DB::connection('mysql_documentario')->select('SELECT * FROM detalle_tramite');

        $id_usuTrabajador = $usuario->id;
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depen]);
        $cont_fecha = DB::connection('mysql_documentario')->select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
            SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
            SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo FROM movimiento
            WHERE iddependencias_receptor = ? AND idestado= 1', [$depen]);

        $nombreArchivo = null;
        $rutaPDF = null;
        try {
            $actualizarestado = DB::connection('mysql_documentario')->update("
                UPDATE `movimiento`
                SET `idestado` = 3,
                `fecha_finalizacion`= now()
                WHERE `idmovimiento` = ?
            ", [$id]);

            // Redirige a la vista deseada si todo va bien
            return redirect()->back()->with('success', 'Documento atentido, finalizado');

            // return view('mesaPartes.recibidos', compact('depen', 'usuario', 'rol', 'cont_est', 'cont_fecha', 'dependencias', 'detalledocumento'))->with('success', 'Documento respondido correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            // return view('mesaPartes.recibidos')->with('error', 'Error al actualizar el documento');
            return redirect()->back()->with('error', 'Error al recibir documento');

        }
    }

    public function responder(Request $request)
    {
        // dd($request);
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $id_depen = session('dependencia_id');
        $rol = DB::table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::select('SELECT * FROM dependencias');
        $detalledocumento = DB::select('SELECT * FROM detalle_tramite');

        $id_usuTrabajador = $usuario->id;
        $cont_est = DB::select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depen]);
        $cont_fecha = DB::select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
                                                  SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
                                                  SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo FROM movimiento
                                                  WHERE iddependencias_receptor = ? AND idestado= 1', [$depen]);

        $firmasJson = json_decode($request->firmas_json, true);
        $usuarioFirmante = DB::table('users')->where('id', $request->id_usuTrabajador)->first();
        $nombre = $usuarioFirmante->name;
        $dni = $usuarioFirmante->dni;
        $motivo = "Autor del documento";
        $fecha = now()->format('d/m/Y H:i:s') . ' -0500';
        $pathFirma = public_path('firma_pdf/logo_firma_pedagogico.png');



        try {
            Log::info('Iniciando transacción para responder documento', [
                'request_data' => $request->all(),
            ]);
            DB::beginTransaction();

            // Obtener el último número de expediente asociado al emisor
            $ultimoExpediente = DB::table('documentos')
                ->where('emisor', $request->dependencia)
                ->orderBy('idDocumentos', 'desc')
                ->value('numero_de_exp');

            // Generar el siguiente número de expediente
            $nuevoExpediente = $ultimoExpediente ? str_pad($ultimoExpediente + 1, 3, '0', STR_PAD_LEFT) : '001';
            Log::info('Insert realizado en documentos con expediente: ' . $nuevoExpediente);


            // Insertar el nuevo documento en la base de datos
            try {
                // Insertar el nuevo documento y obtener el ID generado
                $ultimoIdDocumento = DB::table('documentos')->insertGetId([
                    'numero_de_exp' => $nuevoExpediente,
                    'fecha_ingreso' => now(),
                    'asunto' => $request->asunto,
                    'idtipo_documento' => null,
                    'emisor' => $request->dependencia,
                    'iddetalle_tramite' => $request->tramite_documento,
                    'idusuario' => null,
                    'recomendacion' => $request->recomendaciones,
                    'iddocumento_referencia' => $request->iddocument,
                    'folio' => $request->folio,
                ]);

                Log::info("✅ Documento insertado correctamente. ID generado: $ultimoIdDocumento");
            } catch (\Throwable $e) {
                Log::error('❌ Error al insertar en la tabla documentos', [
                    'mensaje' => $e->getMessage(),
                    'linea' => $e->getLine(),
                    'archivo' => $e->getFile()
                ]);
                DB::rollBack();
                return back()->with('error', 'Error al crear el documento: ' . $e->getMessage());
            }

            // Actualizar estado del documento anterior
            DB::update("
    UPDATE `movimiento`
    SET `idestado` = 3 , `fecha_finalizacion` = now()
    WHERE `iddocumentos` = ?
", [
                $request->iddocument
            ]);

            DB::update("
    UPDATE `documentos`
    SET fecha_finalizacion = now()
    WHERE `iddocumentos` = ?
", [
                $request->iddocument
            ]);

            // Insertar nuevo movimiento
            DB::insert("
    INSERT INTO `movimiento`(
        `iddocumentos`,
        `iddependencias_emior`,
        `iddependencias_receptor`,
        `fecha_de_envio`,
        `fecha_de_recepcion`,
        `idestado`
    ) VALUES (?, ?, ?, ?, ?, ?)
", [
                $ultimoIdDocumento,
                $request->dependencia,
                $request->oficina_destino,
                now(),
                null,
                1
            ]);
            // Obtener el último `iddocumentos` creado (por el emisor)


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

            if ($rutaPDF && file_exists($rutaPDF)) {
                $nombreFirmado = $this->firmarYGuardarPDF($rutaPDF, $firmasJson, $pathFirma, $nombre, $dni, $motivo, $fecha);

                if ($nombreFirmado) {
                    DB::table('documenpdf')->insert([
                        'nombre_del_documento' => $nombreFirmado,
                        'fecha_subida' => now(),
                        'iddocumentos' => $ultimoIdDocumento,
                        'token_pdf' => Str::uuid(),
                        'tipo_pdf' => $request->entregaOpciones,
                        'usuario_id' => $request->id_usuTrabajador
                    ]);
                }
            }

            DB::commit();
            return view('mesaPartes.recibidos', compact('depen', 'usuario', 'rol', 'cont_est', 'cont_fecha', 'dependencias', 'detalledocumento', 'id_depen'))->with('success', 'Documento agregado exitosamente con número de expediente ' . $nuevoExpediente);
        } catch (\Exception $e) {
            Log::error('Excepción detectada', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollBack();
            return view('mesaPartes.recibidos', compact('depen', 'usuario', 'rol', 'cont_est', 'cont_fecha', 'dependencias', 'detalledocumento', 'id_depen'))->with('error', 'Error al insertar datos: ' . $e->getMessage());
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

                        $pdf->SetFillColor(255, 230, 255);
                        $pdf->Rect($x, $y, 90, 30, 'F');

                        if (file_exists($pathFirma)) {
                            $pdf->Image($pathFirma, $x + 2, $y + 2, 20);
                        } else {
                            \Log::warning("⚠️ Imagen de firma no encontrada en: $pathFirma");
                        }

                        $pdf->SetFont('Helvetica', '', 7);
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


    public function responderDocumento($iddocument)
    {
        $usuario = Auth::user();
        $depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depen)->first();
        $dependencias = DB::connection('mysql_documentario')->select('SELECT * FROM dependencias');
        $detalledocumento = DB::connection('mysql_documentario')->select('SELECT * FROM detalle_tramite');

        // Traer el documento específico
        $documento = DB::connection('mysql_documentario')->table('documentos')->where('iddocumentos', $iddocument)->first();

        // Conteos (igual que en index/solucionar)
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$depen]);

        $cont_fecha = DB::connection('mysql_documentario')->select('SELECT SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 0 THEN 1 ELSE 0 END) AS verde,
                                     SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) = 1 THEN 1 ELSE 0 END) AS amarillo,
                                     SUM(CASE WHEN DATEDIFF(NOW(), fecha_de_envio) > 1 THEN 1 ELSE 0 END) AS rojo
                              FROM movimiento
                              WHERE iddependencias_receptor = ? AND idestado= 1', [$depen]);

        $id_depen = session('dependencia_id');

return view('mesaPartes.responderdocumento', compact(
    'usuario','depen','id_depen','rol','dependencias','detalledocumento','documento','cont_est','cont_fecha'
));
}

public function derivarDirector($id)
{
    try {
        // 1. Constantes del sistema
        $idDependenciaDirector = 7; // ID de la dependencia del Director
        $idMesaPartes = 24;
        $idTipoFUT = 1;

        // 2. Obtener documento original
        $docOriginal = DB::connection('mysql_documentario')->table('documentos')
            ->where('iddocumentos', $id)
            ->first();

        if (!$docOriginal) {
            return back()->with('error', 'El documento no existe o ya fue procesado.');
        }

        // =================================================================
        // 🛡️ 3. VALIDACIÓN DE SEGURIDAD: ¿El Director tiene exactamente 1 jefe activo?
        // =================================================================
        $jefesCount = DB::connection('mysql_documentario')->table('dependencia_user')
            ->where('dependencia_id', $idDependenciaDirector)
            ->where('tipo_usuario', 2) // 2 = Jefe
            ->where('estado', 1)       // 1 = Activo
            ->count();

        if ($jefesCount === 0) {
            return back()->with('error', "No se puede derivar: La dependencia del Director no tiene un jefe activo asignado. Regularice esto en el módulo de usuarios.");
        }

        if ($jefesCount > 1) {
            return back()->with('error', "No se puede derivar: La dependencia del Director tiene MÚLTIPLES jefes activos. Por seguridad, el sistema bloquea la derivación hasta que se regularice.");
        }

        // Si llegamos aquí, obtenemos el ID del usuario (jefe) para registrar a quién se le envía realmente
        $id_user_receptor_director = DB::connection('mysql_documentario')->table('dependencia_user')
            ->where('dependencia_id', $idDependenciaDirector)
            ->where('tipo_usuario', 2)
            ->where('estado', 1)
            ->value('user_id');

        // =================================================================
        // 4. INICIO DE TRANSACCIÓN
        // =================================================================
        DB::connection('mysql_documentario')->beginTransaction();

        // Lógica de correlativo (se mantiene tu lógica original de FUT vs General)
        $queryUltimo = DB::connection('mysql_documentario')->table('documentos')
            ->where('emisor', $idMesaPartes)
            ->where('est_firma', 0);

        if ($docOriginal->idtipo_documento == $idTipoFUT) {
            $ultimoExpediente = $queryUltimo->where('idtipo_documento', $idTipoFUT)->max('numero_de_exp');
        } else {
            $ultimoExpediente = $queryUltimo->where('idtipo_documento', '!=', $idTipoFUT)->max('numero_de_exp');
        }

        $nuevoExpediente = $ultimoExpediente ? (int)$ultimoExpediente + 1 : 1;

        // Insertar nuevo documento (La derivación)
        $nuevoIdDoc = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
            'numero_de_exp'           => $nuevoExpediente,
            'fecha_ingreso'           => now(),
            'asunto'                  => $docOriginal->asunto,
            'idtipo_documento'        => $docOriginal->idtipo_documento,
            'emisor'                  => $idMesaPartes,
            'idusuario'               => Auth::id(),
            'iddetalle_tramite'       => $docOriginal->iddetalle_tramite,
            'recomendacion'           => "Derivado por Mesa de Partes: " . $docOriginal->recomendacion,
            'iddocumento_referencia'  => $id,
            'folio'                   => $docOriginal->folio,
            'est_firma'               => 0,
        ]);

        // =================================================================
        // 🕵️ 5. TRAZABILIDAD: Actualizar movimiento original con usuario y fecha
        // =================================================================
        DB::connection('mysql_documentario')->table('movimiento')
            ->where('iddocumentos', $id)
            ->where('iddependencias_receptor', $idMesaPartes)
            ->where('idestado', 2) // Asumiendo que 2 es "En Proceso" o "Recibido"
            ->update([
                'idestado'           => 3, // 3 = Finalizado / Derivado
                'fecha_finalizacion' => now(),
                'id_user_finali'     => Auth::id() // 👈 ¡AQUÍ QUEDA REGISTRADO QUIÉN DERIVÓ!
            ]);

        // =================================================================
        // 6. Crear nuevo movimiento hacia el Director
        // =================================================================
        DB::connection('mysql_documentario')->table('movimiento')->insert([
            'iddocumentos'            => $nuevoIdDoc,
            'iddependencias_emior'    => $idMesaPartes,
            'iddependencias_receptor' => $idDependenciaDirector,
            'id_user_receptor'        => $id_user_receptor_director, // 👈 ¡MEJORA! Ahora sabemos a qué usuario específico llegó
            'fecha_de_envio'          => now(),
            'idestado'                => 1
        ]);

        // Obtener conteos para el evento Real-Time
        $cont_estados = DB::connection('mysql_documentario')->select('
            SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
            FROM estado
            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
            AND movimiento.iddependencias_receptor = ?
            WHERE estado.idestado IN (1,2,3)
            GROUP BY estado.idestado
        ', [$idDependenciaDirector]);

        DB::connection('mysql_documentario')->commit();

        $tipoMsj = ($docOriginal->idtipo_documento == $idTipoFUT) ? "FUT" : "General";
        
        // Disparamos el evento (usando el ID de la dependencia para consistencia)
        event(new DocumentoRecibido($idDependenciaDirector, $cont_estados, 'dependencia'));

        return back()->with('success', "Derivado al Director con éxito. Nuevo Exp. $tipoMsj: $nuevoExpediente");

    } catch (\Exception $e) {
        DB::connection('mysql_documentario')->rollBack();
        
        // Es buena práctica registrar el error en los logs de Laravel para auditoría técnica
        \Illuminate\Support\Facades\Log::error('Error al derivar al Director (Doc ID: ' . $id . '): ' . $e->getMessage());
        
        return back()->with('error', 'Error al derivar: ' . $e->getMessage());
    }
}
}
