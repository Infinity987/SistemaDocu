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

    public function buscarEntidad(Request $request)
{
    $query = $request->input('q');

    $entidades = DB::connection('mysql_documentario')->table('entidades_externas')
        ->where('razon_social_nombre', 'like', "%{$query}%")
        ->orWhere('ruc', 'like', "%{$query}%")
        ->orWhere('codigo_enti', 'like', "%{$query}%")
        ->limit(20)
        ->get();

    $results = $entidades->map(function ($entidad) {
        return [
            'id' => $entidad->id,
            'text' => $entidad->razon_social_nombre . ' - ID: ' . $entidad->id
        ];
    });

    return response()->json($results);
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



     public function generarWordBorrador(Request $request)
{
    // ... validaciones ...

    try {
        // CORRECCIÓN AQUÍ: Cambiamos 'borrador_crear.docx' por 'responder.docx'
        $templatePath = storage_path('app/templates/responder.docx');
        
        if (!file_exists($templatePath)) {
            return "Error: No se encuentra la plantilla en: " . $templatePath;
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Importante: Asegúrate de que los nombres de las variables 
        // coincidan con los de tu archivo responder.docx
        $tipoDocNombre = DB::connection('mysql_documentario')->table('tipo_documento')
            ->where('idtipo_documento', $request->tipo_documento)
            ->value('nombre_documento');

        // Usa los nombres que espera tu plantilla responder.docx
        $template->setValue('asunto', $request->asunto);
        $template->setValue('folio', $request->folio);
        $template->setValue('tipo_doc', $tipoDocNombre ?? 'Documento'); // o como se llame en el Word
        $template->setValue('fecha', now()->format('d/m/Y'));
        
        // Como es un registro nuevo, la referencia suele ir vacía
        $template->setValue('referencia', ''); 

        $fileName = 'Borrador_' . time() . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $template->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        return "Error en el servidor: " . $e->getMessage();
    }
}

   public function registrarDocu(Request $request)
{
    // dd($request);
    // 1. Datos base
    $usuario_id_sistema = Auth::user()->id;
    $emisor_id = $request->emisor;
    $fecha_actual = $request->fecha_actual;
// ... dentro de public function registrarDocu(Request $request)

$reglas = [
    'tipo_documento'   => 'required|not_in:0',
    'asunto'           => 'required|string',
    'folio'            => 'required|integer|min:1',
];

// Validación específica para Mesa de Partes (ID 19)
if ($request->emisor == 19) {
    if ($request->tipo_remitente == 'natural') {
        // Si es persona natural, SOLO validamos el usuario
        $reglas['usuario'] = 'required|not_in:0';
    } else {
        // Si es entidad, SOLO validamos los campos de entidad
        $reglas['id_entidad_externa'] = 'required|exists:entidades_externas,id';
        $reglas['numero_documento_externo'] = 'required|string|max:255';
    }
}

// Mensajes personalizados
$mensajes = [
            'tipo_documento.required' => 'El campo tipo de documento es obligatorio.',
            'tipo_documento.not_in' => 'Debes seleccionar un tipo de documento',

            'asunto.required' => 'El campo asunto es obligatorio.',
            'folio.required' => 'Es obligatorio.',
            'folio.not_in' => 'Debe ser diferente de 0.',

            'dependencia_enviar.required' => 'La dependencia de envío es obligatoria.',
            'dependencia_enviar.not_in' => 'Debes seleccionar una dependencia.',
        ];

$request->validate($reglas, $mensajes);

    try {
        DB::beginTransaction();

        // 3. Insertar Documento Principal
        $iddocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
            'numero_de_exp'            => $request->num_expe,
            'fecha_ingreso'            => $fecha_actual,
            'asunto'                   => $request->asunto,
            'folio'                    => $request->folio,
            'idtipo_documento'         => $request->tipo_documento,
            'emisor'                   => $emisor_id,
            'id_user'                  => $usuario_id_sistema,
            'iddetalle_tramite'        => $request->para_su,
            'idusuario'                => ($emisor_id == 24 && $request->tipo_remitente == 'natural') ? $request->usuario : null,
            'recomendacion'            => $request->Recomendaciones,
            'id_entidad_externa'       => ($emisor_id == 24 && $request->tipo_remitente == 'juridica') ? $request->id_entidad_externa : null,
'numero_documento_externo' => ($emisor_id == 24 && $request->tipo_remitente == 'juridica') ? $request->numero_documento_externo : null,
           
            'anexos_fisicos'   => $request->detalle_anexos_fisicos,
            'estado_actu'              => 1,
            'token'                    => ($emisor_id == 24) ? Str::uuid() : null,
        ]);

        // 4. Determinar Receptores (Todas o Selección)
        $receptores = [];
        if ($request->has('todasDepenSelects')) {
            $receptores = DB::connection('mysql_documentario')->table('dependencias')
                            ->where('iddependencias', '!=', 1)
                            ->where('iddependencias', '!=', $emisor_id)
                            ->pluck('iddependencias')
                            ->toArray();
        } else {
            $receptores = $request->dependencia_enviar;
        }

        // 5. Insertar Movimientos y Emitir Eventos
        foreach ($receptores as $id_receptor) {
            DB::connection('mysql_documentario')->table('movimiento')->insert([
                'iddocumentos'            => $iddocumento,
                'iddependencias_emior'    => $emisor_id,
                'iddependencias_receptor' => $id_receptor,
                'fecha_de_envio'          => $fecha_actual,
                'idestado'                => 1
            ]);

            // Obtener conteos para el evento Real-Time
            $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                        FROM estado
                                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                        AND movimiento.iddependencias_receptor = ?
                                        WHERE estado.idestado IN (1,2,3)
                                        GROUP BY estado.idestado;', [$id_receptor]);

            event(new DocumentoRecibido($id_receptor, $cont_estados));
        }

        // 6. Gestión de Archivo PDF (Sin Firma Digital)
        if ($request->hasFile('archivo_pdf')) {
            $archivo = $request->file('archivo_pdf');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('documentos/documentos_director_pdf'), $nombreArchivo);

            DB::connection('mysql_documentario')->table('documenpdf')->insert([
                'nombre_del_documento' => $nombreArchivo,
                'fecha_subida'         => now(),
                'iddocumentos'         => $iddocumento,
                'token_pdf'            => Str::uuid(),
                'tipo_pdf'             => 1, // 1 para virtual
                'usuario_id'           => $usuario_id_sistema
            ]);
        }

        DB::commit();

        return response()->json(['success' => '¡Documento registrado y enviado con éxito!']);

    } catch (\Throwable $th) {
        DB::rollBack();
        Log::error("Error en registro: " . $th->getMessage());
        return response()->json(['error' => 'Error al registrar: ' . $th->getMessage()], 500);
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


    
}

