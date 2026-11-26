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
use Yajra\DataTables\Facades\DataTables;
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
            // dd($rol);
            $id_usuTrabajador = Auth::user()->id;

            $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);
            $dependencias = DB::connection('mysql_documentario')->table('dependencias')->whereNotIn('iddependencias', [1, 6])->get();
            $detalle_tramite = DB::connection('mysql_documentario')->table('detalle_tramite')->get();
            return view('mesaPartes.index', compact('dependencias', 'detalle_tramite', 'id_depen', 'rol', 'id_usuTrabajador', 'cont_est'));
        } catch (\Throwable $th) {
            return view('mesaPartes.index', compact('dependencias'));
        }
    }

    public function emitidos($idtipo_docu, $emisor)
    {
        $bdPrincipal = DB::connection('mysql')->getDatabaseName();         // donde viven users y postulante
        $bdSia       = DB::connection('mysql_segunda')->getDatabaseName(); // userprofile (gamnielb_sia)

        $q1 = "SELECT
                    d.iddocumentos,
                    d.numero_de_exp,
                    d.fecha_ingreso,
                    d.asunto,
                    COALESCE(
                        userprofilew.nombre,
                        CONCAT_WS(' ', p.apellidos_pater_postulante, p.apellidos_mater_postulante, p.nombres_postulante)
                    ) AS nombre,
                    dependencias.nombre_dependencia AS nombre_dependencia,
                    d.folio,
                    MAX(m.idestado) OVER (PARTITION BY d.iddocumentos) AS id_estado,
                    m.iddependencias_receptor AS iddependencias_receptor,
                    m.id_user_receptor AS id_user_receptor
                FROM movimiento m
                    INNER JOIN documentos d ON m.iddocumentos = d.iddocumentos
                    LEFT JOIN {$bdSia}.userprofile AS userprofilew ON m.id_user_receptor = userprofilew.id_users
                    LEFT JOIN {$bdPrincipal}.users u ON m.id_user_receptor = u.id
                    LEFT JOIN {$bdPrincipal}.postulante p ON u.dni = p.idpostulante
                    LEFT JOIN dependencias ON m.iddependencias_receptor = dependencias.iddependencias
                WHERE d.idtipo_documento = ? AND d.emisor = ? AND d.est_firma = ?";

        if (session('dependencia_id') == 2 || session('dependencia_id') == 4) {
            // dd("holaa");
            $qf1 = $q1 . ' AND d.id_user = ? ORDER BY d.numero_de_exp DESC';
            $query_emitidos = DB::connection('mysql_documentario')->select($qf1, [$idtipo_docu, $emisor, 1, Auth::user()->id]);
        }

        if (session('dependencia_id') != 2 and session('dependencia_id') != 4) {
            $qf1 = $q1 . ' ORDER BY d.numero_de_exp DESC';
            $query_emitidos = DB::connection('mysql_documentario')->select($qf1, [$idtipo_docu, $emisor, 1]);
        }

        if (request()->ajax()) {
            return datatables::of($query_emitidos)->addColumn('btn', 'mesaPartes.butons')->rawColumns(['btn'])->toJson();
        }
    }

    public function emitidos_m($idtipo_docu, $emisor)
    {
        $operador = ($idtipo_docu == 1) ? '=' : '>';
        $valorFiltro = 1;

        // $query_emitidos = DB::connection('mysql_documentario')->select("SELECT
        //     d.iddocumentos,
        //     d.numero_de_exp,
        //     d.fecha_ingreso,
        //     d.asunto,
        //     userprofilew.nombre AS nombre,
        //     dependencias.nombre_dependencia AS nombre_dependencia,
        //     d.folio,
        //     tipo_documento.nombre_documento,
        //     MAX(m.idestado) OVER (PARTITION BY d.iddocumentos) AS id_estado,
        //     m.iddependencias_receptor AS id_user_receptor
        //     FROM movimiento m
        //         INNER JOIN documentos d ON m.iddocumentos = d.iddocumentos
        //         INNER JOIN tipo_documento ON d.idtipo_documento = tipo_documento.idtipo_documento
        //         left JOIN gamnielb_sia.userprofile as userprofilew ON m.id_user_receptor = userprofilew.id_users
        //         left join dependencias ON m.iddependencias_receptor = dependencias.iddependencias
        //         WHERE d.idtipo_documento $operador ? AND d.emisor = ? AND d.est_firma = ?
        //         ORDER BY d.numero_de_exp DESC;
        //     ", [
        //     $valorFiltro, // Siempre evaluamos contra el ID 1 (FUT)
        //     $emisor,
        //     0
        // ]);

        // if (request()->ajax()) {
        //     return datatables::of($query_emitidos)
        //         ->addColumn('btn', 'mesaPartes.butons')
        //         ->rawColumns(['btn'])
        //         ->toJson();
        // }

        ///////////////////////////////////////
        $bdPrincipal = DB::connection('mysql')->getDatabaseName();         // donde viven users y postulante
        $bdSia       = DB::connection('mysql_segunda')->getDatabaseName(); // userprofile (gamnielb_sia)

        $q1 = "SELECT
                    d.iddocumentos,
                    d.numero_de_exp,
                    d.fecha_ingreso,
                    d.asunto,
                    COALESCE(
                        userprofilew.nombre,
                        CONCAT_WS(' ', p.apellidos_pater_postulante, p.apellidos_mater_postulante, p.nombres_postulante)
                    ) AS nombre,
                    dependencias.nombre_dependencia AS nombre_dependencia,
                    d.folio,
                    tipo_documento.nombre_documento,
                    MAX(m.idestado) OVER (PARTITION BY d.iddocumentos) AS id_estado,
                    m.iddependencias_receptor AS iddependencias_receptor,
                    m.id_user_receptor AS id_user_receptor
                FROM movimiento m
                    INNER JOIN documentos d ON m.iddocumentos = d.iddocumentos
                    INNER JOIN tipo_documento ON d.idtipo_documento = tipo_documento.idtipo_documento
                    LEFT JOIN {$bdSia}.userprofile AS userprofilew ON m.id_user_receptor = userprofilew.id_users
                    LEFT JOIN {$bdPrincipal}.users u ON m.id_user_receptor = u.id
                    LEFT JOIN {$bdPrincipal}.postulante p ON u.dni = p.idpostulante
                    LEFT JOIN dependencias ON m.iddependencias_receptor = dependencias.iddependencias
                WHERE d.idtipo_documento $operador ? AND d.emisor = ? AND d.est_firma = ?";

        if (session('dependencia_id') != 2 and session('dependencia_id') != 4) {
            $qf1 = $q1 . ' ORDER BY d.numero_de_exp DESC';
            $query_emitidos = DB::connection('mysql_documentario')->select($qf1, [$valorFiltro, $emisor, 0]);
        }

        if (request()->ajax()) {
            return datatables::of($query_emitidos)->addColumn('btn', 'mesaPartes.butons')->rawColumns(['btn'])->toJson();
        }
    }

    public function num_tipo_documento_expe($id_tipo_docu, $emisor, $iddocumento = null)
    {
        if (session('dependencia_id') == 24) {
            if ($iddocumento !== null) {
                $estadoFirma = DB::connection('mysql_documentario')
                    ->table('documentos')
                    ->where('iddocumentos', $iddocumento)
                    ->limit(1)
                    ->get();
                // dd($estadoFirma);
                if ($estadoFirma[0]->est_firma == 0) {
                    $query = DB::connection('mysql_documentario')
                        ->table('documentos')
                        ->where('emisor', session('dependencia_id'))
                        ->where('est_firma', 0);

                    // Lógica de segmentación de contadores
                    if ($id_tipo_docu == 1) {
                        // Contador exclusivo para FUT
                        $query->where('idtipo_documento', 1);
                        $ultimo = $query->max('numero_de_exp');
                    } else {
                        // Contador compartido para Oficios(2), Informes(3), Requerimientos(4), etc.
                        if ($estadoFirma[0]->idtipo_documento == 1) {
                            // dd("aqui");
                            $query->where('idtipo_documento', '>', 1);
                            $ultimo = $query->max('numero_de_exp');
                        } else {
                            $query->where('idtipo_documento', '>', 1);
                            $ultimo = $query->max('numero_de_exp') - 1;
                        }
                    }
                } else {
                    $que = DB::connection('mysql_documentario')
                        ->table('documentos')
                        ->where('emisor', session('dependencia_id'))
                        ->where('idtipo_documento', $id_tipo_docu)
                        ->where('est_firma', 1);
                    $ultimo = $que->max('numero_de_exp');
                }
            } else {
                $query = DB::connection('mysql_documentario')
                    ->table('documentos')
                    ->where('emisor', session('dependencia_id'))
                    ->where('est_firma', 1)
                    ->where('idtipo_documento', $id_tipo_docu);
                $ultimo = $query->max('numero_de_exp');
            }
        } else if (session('dependencia_id') == 2 || session('dependencia_id') == 4 || session('dependencia_id') == 5) {
            $ultimo = DB::connection('mysql_documentario')
                ->table('documentos')
                ->where('idtipo_documento', $id_tipo_docu)
                ->where('emisor', session('dependencia_id'))
                ->where('id_user', Auth::id())
                ->where('est_firma', 1);

            $ultimo = $ultimo->max('numero_de_exp');
        } else {
            $ultimo = DB::connection('mysql_documentario')
                ->table('documentos')
                ->where('idtipo_documento', $id_tipo_docu)
                ->where('emisor', $emisor)
                ->where('est_firma', 1)
                ->max('numero_de_exp');
        }
        return response()->json([
            'numero_de_exp' => $ultimo ?? 0
        ]);
    }

    public function num_tipo_documento_expe_m($id_tipo_docu, $emisor)
    {
        $query = DB::connection('mysql_documentario')
            ->table('documentos')
            ->where('emisor', session('dependencia_id'))
            ->where('est_firma', 0);

        // Lógica de segmentación de contadores
        if ($id_tipo_docu == 1) {
            // Contador exclusivo para FUT
            $query->where('idtipo_documento', 1);
        } else {
            // Contador compartido para Oficios(2), Informes(3), Requerimientos(4), etc.
            $query->where('idtipo_documento', '>', 1);
        }

        $ultimo = $query->max('numero_de_exp');

        return response()->json([
            'numero_de_exp' => $ultimo ?? 0
        ]);
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

    public function traerDepen(Request $request)
    {
        // dd($request);
        $busqueda = $request->get('q');

        //mesa de partes
        if (session('dependencia_id') == 24) {
            $depens = DB::connection('mysql_documentario')->table('dependencias')
                ->where('nombre_dependencia', 'like', "%{$busqueda}%")
                ->whereNotIn('iddependencias', [1, 3, 4, 5, 6])
                ->get();
            return response()->json($depens);
        }

        //docente
        if (session('dependencia_id') == 2) {
            $depens = DB::connection('mysql_documentario')->table('dependencias')
                ->where('nombre_dependencia', 'like', "%{$busqueda}%")
                ->whereNotIn('iddependencias', [1, 3, 6])
                ->get();
            return response()->json($depens);
        }

        //alumno
        if (session('dependencia_id') == 4) {
            $depens = DB::connection('mysql_documentario')->table('dependencias')
                ->where('nombre_dependencia', 'like', "%{$busqueda}%")
                ->whereNotIn('iddependencias', [1, 3, 4, 5, 6])
                ->get();
            return response()->json($depens);
        }

        //egresado
        if (session('dependencia_id') == 5) {
            $depens = DB::connection('mysql_documentario')->table('dependencias')
                ->where('nombre_dependencia', 'like', "%{$busqueda}%")
                ->whereNotIn('iddependencias', [1, 3, 4, 6])
                ->get();
            return response()->json($depens);
        }

        //todas las dependencias
        $id_depen = session('dependencia_id');
        $depens = DB::connection('mysql_documentario')->table('dependencias')
            ->where('nombre_dependencia', 'like', "%{$busqueda}%")
            ->whereNotIn('iddependencias', [1, 3, $id_depen, 6])
            ->get();
        return response()->json($depens);
    }

    public function traerDepen_m(Request $request)
    {
        $busqueda = $request->get('q');

        //mesa de partes
        if (session('dependencia_id') == 24) {
            $id_depen = session('dependencia_id');
            $depens = DB::connection('mysql_documentario')->table('dependencias')
                ->where('nombre_dependencia', 'like', "%{$busqueda}%")
                ->whereNotIn('iddependencias', [1, 3, 6, $id_depen])
                ->get();
            return response()->json($depens);
        }
    }

    public function buscarDocentes(Request $request)
    {
        $busqueda = $request->get('q');
        $docentes = DB::table('users')
            ->join('gamnielb_sia.userprofile', 'users.id', '=', 'gamnielb_sia.userprofile.id_users')
            ->where('gamnielb_sia.userprofile.nombre', 'like', "%{$busqueda}%")
            ->select('gamnielb_sia.userprofile.id_users as id', 'gamnielb_sia.userprofile.nombre as text')
            ->limit(10)
            ->get();

        return response()->json($docentes);
    }

    public function buscarEgresados(Request $request)
    {
        $busqueda = $request->get('q');
        $egresado = DB::table('users')
            ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where(function ($query) use ($busqueda) {
                $query->where(DB::raw("CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante)"), 'like', "%{$busqueda}%")
                    ->orWhere('users.dni', 'like', "%{$busqueda}%");
            })
            ->where('model_has_roles.role_id', '=', 5)
            ->select(
                'users.id as id',
                DB::raw("CONCAT(users.dni, ' --- ',postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as text")
            )
            ->limit(10)
            ->get();


        return response()->json($egresado);
    }

    public function buscarAlumnos(Request $request)
    {
        $busqueda = $request->get('q');
        $alumno = DB::table('users')
            ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where(function ($query) use ($busqueda) {
                $query->where(DB::raw("CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante)"), 'like', "%{$busqueda}%")
                    ->orWhere('users.dni', 'like', "%{$busqueda}%");
            })
            ->where('model_has_roles.role_id', '=', 4) // rol alumno (verifica en la tabla roles)
            ->select(
                'users.id as id',
                DB::raw("CONCAT(users.dni, ' --- ',postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as text")
            )
            ->limit(10)
            ->get();

        return response()->json($alumno);
    }

    public function generarWordBorrador(Request $request)
    {
        try {
            $templatePath = storage_path('app/templates/responder.docx');

            if (!file_exists($templatePath)) {
                return "Error: No se encuentra la plantilla en: " . $templatePath;
            }

            $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);


            // --- 1. DATOS DE CONFIGURACIÓN (LOGO Y AÑO) ---
            $configuracion = DB::connection('mysql_segunda')
                ->table('encargados')
                ->where('estado', 1)
                ->first();

            if ($configuracion) {
                // Esta es la variable que pediste: el nombre oficial del año
                // En el Word usa: ${texto_anio_oficial}
                $template->setValue('texto_anio_oficial', $configuracion->nombre_año);

                // El resto de tus datos de configuración se mantienen igual
                $template->setValue('nombre_anio', $configuracion->reso_direc);

                $rutaLogo = public_path($configuracion->logo);
                if (file_exists($rutaLogo)) {
                    $template->setImageValue('logo', [
                        'path' => $rutaLogo,
                        'width' => 90,
                        'height' => 90,
                        'ratio' => true
                    ]);
                }
            }

            // 1. DATOS DEL EMISOR (Quien redacta: el usuario logueado)
            $id_depen_emisor = session('dependencia_id');
            $dependenciaEmisor = DB::connection('mysql_documentario')
                ->table('dependencias')
                ->where('iddependencias', $id_depen_emisor)
                ->first();

            // Datos personales del usuario (Conexión mysql_segunda)
            $perfilUsuario = DB::connection('mysql_segunda')
                ->table('userprofile')
                ->where('id_users', Auth::id())
                ->first();

            // ... (código anterior de emisor igual) ...

            // 2. DATOS DEL DESTINATARIO (Lógica de Roles y Perfiles)
            $id_depen_receptor = is_array($request->dependencia_enviar) ? $request->dependencia_enviar[0] : null;

            $nombreDestinatario = "___________________________";
            $cargoDestinatario = "___________________________";

            if ($id_depen_receptor) {
                // A. Buscamos el ID del usuario que tiene el rol de esa dependencia
                // Nota: model_id suele ser el ID del usuario en la tabla users
                $rolAsignado = DB::connection('mysql')
                    ->table('model_has_roles')
                    ->where('role_id', $id_depen_receptor)
                    ->first();

                if ($rolAsignado) {
                    // B. Buscamos el nombre en la otra base de datos usando el model_id
                    $perfilDestinatario = DB::connection('mysql_segunda')
                        ->table('userprofile')
                        ->where('id_users', $rolAsignado->model_id)
                        ->first();

                    if ($perfilDestinatario) {
                        $nombreDestinatario = $perfilDestinatario->nombre;
                    }
                }

                // C. El cargo sigue siendo el nombre de la dependencia
                $depReceptora = DB::connection('mysql_documentario')
                    ->table('dependencias')
                    ->where('iddependencias', $id_depen_receptor)
                    ->first();

                $cargoDestinatario = $depReceptora->nombre_dependencia ?? "DEPENDENCIA";
            }

            // --- CONTINUAR CON EL SETVALUE ---
            $template->setValue('destinatario_nombre', strtoupper($nombreDestinatario));
            $template->setValue('destinatario_cargo', strtoupper($cargoDestinatario));
            // ...

            // 3. LÓGICA DEL CORRELATIVO (Para el título: INFORME N° XXX-2026...)
            $tipoDocId = $request->tipo_documento;
            $tipoDocNombre = DB::connection('mysql_documentario')
                ->table('tipo_documento')
                ->where('idtipo_documento', $tipoDocId)
                ->value('nombre_documento');

            // Calculamos el siguiente número (puedes reusar tu lógica de est_firma aquí)
            $ultimoNro = DB::connection('mysql_documentario')->table('documentos')
                ->where('emisor', $id_depen_emisor)
                ->where('idtipo_documento', $tipoDocId)
                ->max('numero_de_exp');

            $nroCorrelativo = $ultimoNro ? (int)$ultimoNro + 1 : 1;
            // Formateamos a 3 o 4 dígitos (ej: 004)
            $nroFormateado = str_pad($nroCorrelativo, 3, "0", STR_PAD_LEFT);

            // --- ASIGNACIÓN DE VALORES A LA PLANTILLA WORD ---

            // Encabezado / Título
            $template->setValue('tipo_doc', strtoupper($tipoDocNombre ?? 'Documento'));
            $template->setValue('nro_doc', $nroFormateado);
            $template->setValue('anio', date('Y'));
            $template->setValue('siglas', $dependenciaEmisor->siglas ?? 'S/N');

            // Bloque de datos (SEÑOR / DE / ASUNTO)
            $template->setValue('destinatario_nombre', $nombreDestinatario);
            $template->setValue('destinatario_cargo', $cargoDestinatario);

            $template->setValue('usuario_nombre', $perfilUsuario->nombre ?? Auth::user()->name);
            $template->setValue('dependencia', $dependenciaEmisor->nombre_dependencia ?? 'Sin Dependencia');

            $template->setValue('asunto', $request->asunto);
            $template->setValue('folio', $request->folio);
            $template->setValue('fecha', now()->translatedFormat('d \d\e F \d\e\l Y')); // Ejemplo: 26 de marzo del 2026
            $template->setValue('referencia', '');

            // Generación del archivo temporal
            $fileName = 'Borrador_' . ($tipoDocNombre ?? 'Doc') . '_' . time() . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $template->saveAs($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return "Error en el servidor: " . $e->getMessage();
        }
    }

    public function registrarDocu(Request $request)
    {
        // 1. Verificar si el que intenta enviar es Asistente (tipo_usuario = 1)
        $es_jefe_o_usuario = 0;
        $esAsistente = DB::connection('mysql_documentario')->table('dependencia_user')
            ->where('user_id', Auth::id())
            ->where('dependencia_id', session('dependencia_id'))
            ->where('tipo_usuario', 1)
            ->where('estado', 1)
            ->exists();

        if ($esAsistente) {
            // 2. Si es asistente, verificar si el JEFE de esta dependencia tiene dar_permiso = 1
            $jefeDioPermiso = DB::connection('mysql_documentario')->table('dependencia_user')
                ->where('dependencia_id', session('dependencia_id'))
                ->where('tipo_usuario', 2) // Buscamos al Jefe
                ->where('dar_permiso', 1)
                ->where('estado', 1)
                ->exists();

            if (!$jefeDioPermiso) {
                // Bloquear la acción
                return response()->json(['error' => 'Acción denegada: El Jefe del Área no ha habilitado el permiso para que los asistentes envíen documentos.'], 500);
            } else {
                $es_jefe_o_usuario = 1;
                $idJefe = DB::connection('mysql_documentario')
                    ->table('dependencia_user')
                    ->where('dependencia_id', session('dependencia_id'))
                    ->where('tipo_usuario', 2)
                    ->where('estado', 1)
                    ->value('user_id');
            }
        }

        $reglas = [
            'tipo_documento' => 'required|integer|not_in:0',
            'folio'          => 'required|integer|min:1',
            'asunto'         => 'required|string|min:5|max:1000', // Mínimo 5 caracteres para evitar "hola"
            'para_su'        => 'required|integer|not_in:0',
            'archivo_pdf'    => 'required|file|mimes:pdf|max:10240', // Obligatorio, solo PDF, máx 10MB
        ];

        // Lógica condicional para el DESTINO del documento
        $tienePersonasEspecificas = $request->filled('docentes_especificos') ||
            $request->filled('egresados') ||
            $request->filled('alumnos');

        if ($tienePersonasEspecificas) {
            if ($request->filled('docentes_especificos')) {
                $reglas['docentes_especificos'] = 'required|array|min:1';
                $reglas['docentes_especificos.*'] = 'required|integer';
            }
            if ($request->filled('egresados')) {
                $reglas['egresados'] = 'required|array|min:1';
                $reglas['egresados.*'] = 'required|integer';
            }
            if ($request->filled('alumnos')) {
                $reglas['alumnos'] = 'required|array|min:1';
                $reglas['alumnos.*'] = 'required|integer';
            }
        } else {
            // Si NO hay personas específicas, la dependencia genérica es OBLIGATORIA
            $reglas['dependencia_enviar'] = 'required|array|min:1';
            $reglas['dependencia_enviar.*'] = 'required|integer';
        }

        // Lógica condicional para ANEXOS FÍSICOS
        // Si el checkbox está marcado, el detalle es obligatorio
        if ($request->has('con_anexos_fisicos')) {
            $reglas['detalle_anexos_fisicos'] = 'required|string|max:255';
        }

        // Mensajes personalizados
        $mensajes = [
            'tipo_documento.required'      => 'Debe seleccionar un tipo de documento.',
            'tipo_documento.not_in'        => 'El tipo de documento seleccionado no es válido.',
            'folio.required'               => 'El folio es obligatorio.',
            'folio.min'                    => 'El folio debe ser mayor a 0.',
            'asunto.required'              => 'El asunto del documento es obligatorio.',
            'asunto.min'                   => 'El asunto debe tener al menos 5 caracteres.',
            'para_su.required'             => 'Debe seleccionar la acción a realizar (Para su).',
            'para_su.not_in'               => 'La acción seleccionada no es válida.',
            'archivo_pdf.required'         => 'El documento digital (PDF) es obligatorio.',
            'archivo_pdf.file'             => 'El archivo debe ser un archivo válido.',
            'archivo_pdf.mimes'            => 'El archivo debe ser exclusivamente formato PDF.',
            'archivo_pdf.max'              => 'El archivo PDF no debe superar los 10 MB.',
            'dependencia_enviar.required'  => 'Debe seleccionar al menos una dependencia de envío.',
            'dependencia_enviar.array'     => 'El formato de las dependencias no es válido.',
            'dependencia_enviar.min'       => 'Debe seleccionar al menos una dependencia.',
            'docentes_especificos.required' => 'Debe seleccionar al menos un docente.',
            'egresados.required'           => 'Debe seleccionar al menos un egresado.',
            'alumnos.required'             => 'Debe seleccionar al menos un alumno.',
            'detalle_anexos_fisicos.required' => 'Debe especificar qué anexos físicos contiene.',
        ];

        $request->validate($reglas, $mensajes);

        // 1. Datos base
        if ($es_jefe_o_usuario == 1) {
            # code...
            $usuario_id_sistema = $idJefe;
        } else {
            $usuario_id_sistema = Auth::user()->id;
        }
        $emisor_id = $request->emisor;
        $fecha_actual = $request->fecha_actual;

        try {
            DB::connection('mysql_documentario')->beginTransaction();
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

                'anexos_fisicos'           => $request->detalle_anexos_fisicos,
                'estado_actu'              => 1,
                'token'                    => ($emisor_id == 24) ? Str::uuid() : null,
                'est_firma'                => $request->est_firma
            ]);

            $advertencias = [];
            $movimientos_insertados = 0;
            $respuesta_tipo = 'success';
            $respuesta_mensaje = '¡Documento registrado y enviado con éxito!';

            if ($request->has('docentes_especificos')) {
                $tot_docentes = $request->docentes_especificos;
                // dd($tot_docentes);
                foreach ($tot_docentes as $id_user_docente) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos'            => $iddocumento,
                        'iddependencias_emior'    => $emisor_id,
                        'iddependencias_receptor' => 2, //solo docente id 2
                        'id_user_receptor'        => $id_user_docente,
                        'fecha_de_envio'          => $fecha_actual,
                        'idestado'                => 1
                    ]);

                    // Obtener conteos para el evento Real-Time
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                        FROM estado
                                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                        AND movimiento.iddependencias_receptor = ?
                                        AND movimiento.id_user_receptor = ?
                                        WHERE estado.idestado IN (1,2,3)
                                        GROUP BY estado.idestado;', [2, $id_user_docente]);
                    //aqui el evento para docentes.
                    event(new DocumentoRecibido($id_user_docente, $cont_estados, 'personal'));
                }
            } elseif ($request->has('egresados')) {
                $tot_egresados = $request->egresados;
                // dd($request);
                foreach ($tot_egresados as $id_user_egresado) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos'            => $iddocumento,
                        'iddependencias_emior'    => $emisor_id,
                        'iddependencias_receptor' => 5, //solo egresado id 5
                        'id_user_receptor'        => $id_user_egresado,
                        'fecha_de_envio'          => $fecha_actual,
                        'idestado'                => 1
                    ]);

                    // Obtener conteos para el evento Real-Time
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                        FROM estado
                                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                        AND movimiento.id_user_receptor = ?
                                        WHERE estado.idestado IN (1,2,3)
                                        GROUP BY estado.idestado;', [$id_user_egresado]);

                    //aqui el evento para docentes.
                    event(new DocumentoRecibido($id_user_egresado, $cont_estados, 'personal'));
                }
            } elseif ($request->has('alumnos')) {
                foreach ($request->alumnos as $id_user_alumno) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos'            => $iddocumento,
                        'iddependencias_emior'    => $emisor_id,
                        'iddependencias_receptor' => 4,
                        'id_user_receptor'        => $id_user_alumno,
                        'fecha_de_envio'          => $fecha_actual,
                        'idestado'                => 1
                    ]);

                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.id_user_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_user_alumno]);

                    event(new DocumentoRecibido($id_user_alumno, $cont_estados, 'personal'));
                }
            } else {
                $receptores = [];
                if ($request->has('todasDepenSelects')) {
                    $idsExcluir = [1, 2, 3, 4, 5, 6, $emisor_id];
                    $receptores = DB::connection('mysql_documentario')->table('dependencias')
                        ->whereNotIn('iddependencias', $idsExcluir)
                        ->pluck('iddependencias')
                        ->toArray();
                } else {
                    $receptores = $request->dependencia_enviar;
                }

                foreach ($receptores as $id_receptor) {
                    // 1. Obtener el nombre de la dependencia para un mensaje claro al usuario
                    $nombre_dependencia = DB::connection('mysql_documentario')->table('dependencias')
                        ->where('iddependencias', $id_receptor)
                        ->value('nombre_dependencia');

                    // 2. Contar cuántos jefes activos tiene esta dependencia
                    $jefes_count = DB::connection('mysql_documentario')->table('dependencia_user')
                        ->where('dependencia_id', $id_receptor)
                        ->where('tipo_usuario', 2) // 2 = Jefe
                        ->where('estado', 1)       // 1 = Activo
                        ->count();

                    // 3. Evaluar los escenarios
                    if ($jefes_count === 0) {
                        $advertencias[] = "La dependencia '<b>{$nombre_dependencia}</b>' no tiene un jefe activo asignado. No se realizó el envío.";
                        continue; // Saltamos esta dependencia, no insertamos movimiento
                    }

                    if ($jefes_count > 1) {
                        $advertencias[] = "La dependencia '<b>{$nombre_dependencia}</b>' tiene múltiples jefes activos. Por seguridad, no se realizó el envío hasta que se regularice.";
                        continue; // Saltamos esta dependencia
                    }

                    // 4. Si llegamos aquí, hay EXACTAMENTE 1 jefe activo. Procedemos con normalidad.
                    $id_user_receptor = DB::connection('mysql_documentario')->table('dependencia_user')
                        ->where('dependencia_id', $id_receptor)
                        ->where('tipo_usuario', 2)
                        ->where('estado', 1)
                        ->value('user_id');

                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos'            => $iddocumento,
                        'iddependencias_emior'    => $emisor_id,
                        'iddependencias_receptor' => $id_receptor,
                        'id_user_receptor'        => $id_user_receptor,
                        'fecha_de_envio'          => $fecha_actual,
                        'idestado'                => 1
                    ]);

                    $movimientos_insertados++;

                    $cont_estados = DB::connection('mysql_documentario')->select('
                                        SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                        FROM estado
                                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                        AND movimiento.iddependencias_receptor = ?
                                        WHERE estado.idestado IN (1,2,3)
                                        GROUP BY estado.idestado
                                    ', [$id_receptor]);

                    event(new DocumentoRecibido($id_receptor, $cont_estados, 'dependencia'));
                }

                // 5. Decidir qué respuesta JSON enviar al final del proceso
                if ($movimientos_insertados === 0 && count($advertencias) > 0) {
                    DB::connection('mysql_documentario')->rollBack();
                    return response()->json([
                        'error' => 'No se pudo enviar el documento a ninguna dependencia. Revisa los motivos a continuación:',
                        'warnings' => $advertencias // <-- AQUÍ enviamos la lista completa
                    ], 422);
                }

                if (count($advertencias) > 0) {
                    $respuesta_tipo = 'warning';
                    $respuesta_mensaje = 'Documento registrado y enviado parcialmente.';
                }
            }

            ////////////////////////////// fin en caso envia a docentes

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
                    'usuario_id'           => Auth::user()->id
                ]);
            }

            DB::connection('mysql_documentario')->commit();

            if ($respuesta_tipo === 'warning') {
                return response()->json([
                    'success' => $respuesta_mensaje,
                    'warnings' => $advertencias
                ]);
            }

            return response()->json(['success' => $respuesta_mensaje]);

            // return response()->json(['success' => '¡Documento registrado y enviado con éxito!']);
        } catch (\Throwable $th) {
            DB::connection('mysql_documentario')->rollBack();
            Log::error("Error en registro: " . $th->getMessage());
            return response()->json(['error' => 'Error al registrar: ' . $th->getMessage()], 500);
        }
    }

    public function registrarDocu_m(Request $request)
    {
        // dd($request);
        // 1. Verificar si el que intenta enviar es Asistente (tipo_usuario = 1)
        $es_jefe_o_usuario = 0;
        $esAsistente = DB::connection('mysql_documentario')->table('dependencia_user')
            ->where('user_id', Auth::id())
            ->where('dependencia_id', session('dependencia_id'))
            ->where('tipo_usuario', 1)
            ->where('estado', 1)
            ->exists();

        if ($esAsistente) {
            // 2. Si es asistente, verificar si el JEFE de esta dependencia tiene dar_permiso = 1
            $jefeDioPermiso = DB::connection('mysql_documentario')->table('dependencia_user')
                ->where('dependencia_id', session('dependencia_id'))
                ->where('tipo_usuario', 2) // Buscamos al Jefe
                ->where('dar_permiso', 1)
                ->where('estado', 1)
                ->exists();

            if (!$jefeDioPermiso) {
                // Bloquear la acción
                return response()->json(['error' => 'Acción denegada: El Jefe del Área no ha habilitado el permiso para que los asistentes envíen documentos.'], 500);
            } else {
                $es_jefe_o_usuario = 1;
                $idJefe = DB::connection('mysql_documentario')
                    ->table('dependencia_user')
                    ->where('dependencia_id', session('dependencia_id'))
                    ->where('tipo_usuario', 2)
                    ->where('estado', 1)
                    ->value('user_id');
            }
        }

        if ($es_jefe_o_usuario == 1) {
            # code...
            $usuario_id_sistema = $idJefe;
        } else {
            $usuario_id_sistema = Auth::user()->id;
        }

        $emisor_id = $request->emisor;
        $fecha_actual = $request->fecha_actual_m;

        // 1. Definir las reglas de validación base (Campos obligatorios con *)
        $reglas = [
            'tipo_documento_m'     => 'required|not_in:0',
            'folio_m'              => 'required|integer|min:1',
            'tipo_remitente_m'     => 'required|in:natural,nuevo_natural,juridica',
            'dependencia_enviar_m' => 'required|array|min:1',
            'dependencia_enviar_m.*' => 'required|integer', // Asegura que sean IDs numéricos
            'asunto_m'             => 'required|string|min:5',
            'para_su_m'            => 'required|not_in:0',
            'archivo_pdf_m'        => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ];

        // if ($request->tipo_documento_m == 2) {
        //     $reglas['doc_referencia_m'] = 'required|string|max:255';
        // }

        // 2. Reglas condicionales según el Tipo de Remitente seleccionado
        if ($request->tipo_remitente_m === 'natural') {
            $reglas['usuario_m'] = 'required|not_in:0';
        } elseif ($request->tipo_remitente_m === 'nuevo_natural') {
            $reglas['nuevo_dni_m'] = 'required|digits:8|unique:mysql_documentario.usuario,dniusuario';
            $reglas['nuevo_nombres_m'] = 'required|string|max:100';
            $reglas['nuevo_apellidos_m'] = 'required|string|max:100';
            $reglas['nuevo_correo_m'] = 'nullable|email|max:100';
        } elseif ($request->tipo_remitente_m === 'juridica') {
            $reglas['id_entidad_m_externa'] = 'required|exists:mysql_documentario.entidades_externas,id';
            $reglas['numero_documento_externo_m'] = 'required|string|max:255';
            $reglas['correo_entidad_m'] = 'required|email|max:100';
            $reglas['celular_entidad_m'] = ['required', 'regex:/^9[0-9]{8}$/'];
        }


        // 3. Mensajes de error personalizados (para que coincidan con tu UI)
        $mensajes = [
            'tipo_documento_m.required'      => 'El tipo de documento es obligatorio.',
            'tipo_documento_m.not_in'        => 'Debe seleccionar un tipo de documento válido.',
            'folio_m.required'               => 'El folio es obligatorio.',
            'folio_m.min'                    => 'El folio debe ser mayor a 0.',
            'tipo_remitente_m.required'      => 'Debe seleccionar el tipo de remitente.',
            'dependencia_enviar_m.required'  => 'Debe seleccionar al menos una dependencia de envío.',
            'dependencia_enviar_m.array'     => 'El campo dependencia debe ser una lista válida.',
            'dependencia_enviar_m.min'       => 'Debe seleccionar al menos una dependencia.',
            'asunto_m.required'              => 'El asunto del documento es obligatorio.',
            'asunto_m.min'                   => 'El asunto debe tener al menos 5 caracteres.',
            'para_su_m.required'             => 'La acción a realizar (Para su) es obligatoria.',
            'para_su_m.not_in'               => 'Debe seleccionar una acción válida.',
            'archivo_pdf_m.required'         => 'El documento digital (PDF) es obligatorio.',
            'archivo_pdf_m.mimes'            => 'El archivo debe ser exclusivamente formato PDF.',
            'archivo_pdf_m.max'              => 'El archivo PDF no debe superar los 10 MB.',

            // Mensajes condicionales
            'usuario_m.required'             => 'Debe seleccionar un usuario existente.',
            'nuevo_dni_m.required'           => 'El DNI es obligatorio para registrar una nueva persona.',
            'nuevo_dni_m.digits'             => 'El DNI debe tener exactamente 8 dígitos.',
            'nuevo_dni_m.unique'             => 'Este DNI ya está registrado. Por favor, busque al usuario en "Persona Natural (Existente)".',
            'nuevo_nombres_m.required'       => 'Los nombres son obligatorios.',
            'nuevo_apellidos_m.required'     => 'Los apellidos son obligatorios.',
            'nuevo_correo_m.email'           => 'El formato del correo no es válido.',
            'id_entidad_m_externa.exists'    => 'La entidad seleccionada no es válida.',
            'numero_documento_externo_m.required' => 'El N° de documento externo es obligatorio.',
            'correo_entidad_m.email'   => 'El formato del correo electrónico no es válido.',
            'celular_entidad_m.regex'  => 'El celular debe tener 9 dígitos y comenzar con 9.',
            // 'doc_referencia_m.required' => 'La referencia del oficio es obligatoria.'
        ];

        $request->validate($reglas, $mensajes);

        try {
            DB::connection('mysql_documentario')->beginTransaction();

            $idusuario_para_doc = null;

            // 3. PROCESAR REMITENTE: Si es nuevo, crearlo en las 3 tablas
            if ($request->tipo_remitente_m == 'nuevo_natural') {
                $nombreCompleto = trim($request->nuevo_nombres_m . ' ' . $request->nuevo_apellidos_m);
                $emailTemp = $request->nuevo_correo_m ?: $request->nuevo_dni_m . '@temp.local';

                // C) Tabla usuario (mysql_documentario)
                $idusuario_para_doc = DB::connection('mysql_documentario')->table('usuario')->insertGetId([
                    'dniusuario' => $request->nuevo_dni_m,
                    'nombres' => $nombreCompleto,

                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ]);
            } elseif ($request->tipo_remitente_m == 'natural') {
                $idusuario_para_doc = $request->usuario_m;
            }

            $iddocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                'numero_de_exp'            => $request->num_expe_m,
                'fecha_ingreso'            => now(),
                'asunto'                   => $request->asunto_m,
                'folio'                    => $request->folio_m,
                'idtipo_documento'         => $request->tipo_documento_m,
                'emisor'                   => $emisor_id,
                'id_user'                  => $usuario_id_sistema,
                'iddetalle_tramite'        => $request->para_su_m,
                'idusuario'                => $idusuario_para_doc,
                'recomendacion'            => $request->Recomendaciones_m,
                'id_entidad_externa'       => ($emisor_id == 24 && $request->tipo_remitente_m == 'juridica') ? $request->id_entidad_m_externa : null,
                'numero_documento_externo' => ($emisor_id == 24 && $request->tipo_remitente_m == 'juridica') ? $request->numero_documento_externo_m : null,
                'correo'           => $request->correo_entidad_m,
                'celular'          => $request->celular_entidad_m,
                'anexos_fisicos'           => $request->detalle_anexos_fisicos_m,
                'estado_actu'              => 1,
                'token'                    => ($emisor_id == 24) ? Str::uuid() : null,
                'est_firma'                => $request->est_firma,
                'doc_referencia'           => ($request->tipo_documento_m == 2) ? $request->doc_referencia_m : null,
            ]);

            $advertencias = [];
            $movimientos_insertados = 0;

            // 1. Envío a Docentes
            if ($request->filled('docentes_especificos_m')) {
                foreach ($request->docentes_especificos_m as $id_user_docente) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $iddocumento,
                        'iddependencias_emior' => $emisor_id,
                        'iddependencias_receptor' => 2,
                        'id_user_receptor' => $id_user_docente,
                        'fecha_de_envio' => now(),
                        'idestado' => 1
                    ]);
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_user_docente]);
                    event(new DocumentoRecibido($id_user_docente, $cont, 'personal'));
                }
            }
            // 2. Envío a Egresados
            elseif ($request->filled('egresados_m')) {
                foreach ($request->egresados_m as $id_user_egresado) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $iddocumento,
                        'iddependencias_emior' => $emisor_id,
                        'iddependencias_receptor' => 5,
                        'id_user_receptor' => $id_user_egresado,
                        'fecha_de_envio' => now(),
                        'idestado' => 1
                    ]);
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_user_egresado]);
                    event(new DocumentoRecibido($id_user_egresado, $cont, 'personal'));
                }
            } elseif ($request->filled('alumnos_m')) { // 👈 NUEVO BLOQUE
                foreach ($request->alumnos_m as $id_user_alumno) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos'            => $iddocumento,
                        'iddependencias_emior'    => $emisor_id,
                        'iddependencias_receptor' => 4, // ID de la dependencia Alumno
                        'id_user_receptor'        => $id_user_alumno,
                        'fecha_de_envio'          => now(),
                        'idestado'                => 1
                    ]);

                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_user_alumno]);
                    event(new DocumentoRecibido($id_user_alumno, $cont, 'personal'));
                }
            } else {
                $receptores = [];
                if ($request->has('todasDepenSelects_m') && $request->todasDepenSelects_m == 'on') {
                    $receptores = DB::connection('mysql_documentario')->table('dependencias')
                        ->whereNotIn('iddependencias', [1, 2, 4, 5, 6, $emisor_id])
                        ->pluck('iddependencias')
                        ->toArray();
                } else {
                    $receptores = $request->dependencia_enviar_m;
                }

                foreach ($receptores as $id_receptor) {
                    $nombre_dep = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_receptor)->value('nombre_dependencia') ?? 'Desconocida';
                    $jefes_count = DB::connection('mysql_documentario')->table('dependencia_user')
                        ->where('dependencia_id', $id_receptor)->where('tipo_usuario', 2)->where('estado', 1)->count();

                    if ($jefes_count === 1) {
                        $id_user_receptor = DB::connection('mysql_documentario')->table('dependencia_user')
                            ->where('dependencia_id', $id_receptor)->where('tipo_usuario', 2)->where('estado', 1)->value('user_id');

                        DB::connection('mysql_documentario')->table('movimiento')->insert([
                            'iddocumentos' => $iddocumento,
                            'iddependencias_emior' => $emisor_id,
                            'iddependencias_receptor' => $id_receptor,
                            'id_user_receptor' => $id_user_receptor,
                            'fecha_de_envio' => now(),
                            'idestado' => 1
                        ]);
                        $movimientos_insertados++;

                        $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_receptor]);
                        event(new DocumentoRecibido($id_receptor, $cont, 'dependencia'));
                    } elseif ($jefes_count === 0) {
                        $advertencias[] = "La dependencia '<b>{$nombre_dep}</b>' no tiene un jefe activo asignado.";
                    } else {
                        $advertencias[] = "La dependencia '<b>{$nombre_dep}</b>' tiene múltiples jefes activos.";
                    }
                }
            }

            // 4. Evaluación de Advertencias
            if ($movimientos_insertados === 0 && count($advertencias) > 0) {
                DB::connection('mysql_documentario')->rollBack();
                return response()->json([
                    'error' => 'No se pudo enviar el documento a ninguna dependencia.',
                    'warnings' => $advertencias
                ], 422);
            }

            // 5. Subida de PDF
            if ($request->hasFile('archivo_pdf_m')) {
                $archivo = $request->file('archivo_pdf_m');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move(public_path('documentos/documentos_director_pdf'), $nombreArchivo);

                DB::connection('mysql_documentario')->table('documenpdf')->insert([
                    'nombre_del_documento' => $nombreArchivo,
                    'fecha_subida' => now(),
                    'iddocumentos' => $iddocumento,
                    'token_pdf' => Str::uuid(),
                    'tipo_pdf' => 1,
                    'usuario_id' =>  Auth::user()->id
                ]);
            }

            DB::connection('mysql_documentario')->commit();

            // 6. Respuesta JSON
            if (count($advertencias) > 0) {
                return response()->json([
                    'success' => 'Documento recepcionado, pero con advertencias en algunas dependencias.',
                    'warnings' => $advertencias
                ]);
            }

            return response()->json(['success' => '¡Documento recepcionado y enviado con éxito!']);
        } catch (\Throwable $th) {
            DB::connection('mysql_documentario')->rollBack();
            Log::error("Error en registro MP: " . $th->getMessage());
            return response()->json(['error' => 'Error al registrar: ' . $th->getMessage()], 500);
        }
    }

    public function datos()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        $datos = DB::connection('mysql_segunda')->table('userprofile')
            ->where('id_users', '=', Auth::user()->id)->first();
        $dni = DB::table('users')->where('id', '=', Auth::user()->id)->pluck('dni')->first();

        return view('mesaPartes.datos', compact('nom_usu', 'datos', 'dni'));
    }

    public function togglePermisoAsistentes(Request $request)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        try {
            // Actualizamos SOLO si es el jefe de la dependencia actual
            $affected = DB::connection('mysql_documentario')->table('dependencia_user')
                ->where('user_id', Auth::id())
                ->where('dependencia_id', session('dependencia_id'))
                ->where('estado', 1)
                ->where('tipo_usuario', 2) // Solo afecta a registros de Jefe
                ->update([
                    'dar_permiso' => $request->status,
                    'fecha_permiso' => now()
                ]);

            if ($affected > 0) {
                return response()->json([
                    'success' => true,
                    'message' => $request->status == 1
                        ? '¡Permiso otorgado! Ahora sus asistentes pueden enviar documentos.'
                        : 'Permiso revocado. Solo usted puede enviar documentos.'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No se pudo actualizar el estado.'], 400);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error del servidor: ' . $th->getMessage()], 500);
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

            // --- EGRESADOS (ID 5) ---
            if (in_array(5, $dependenciasSelect)) {
                $listEgresados = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $id)
                    ->where('iddependencias_receptor', 5)
                    ->pluck('id_user_receptor')
                    ->toArray();

                $egresadosSeleccionados = DB::table('users')
                    ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
                    ->whereIn('users.id', $listEgresados)
                    ->select(
                        'users.id as id',
                        DB::raw("CONCAT(users.dni, ' --- ', postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as text")
                    )
                    ->get();
            } else {
                $egresadosSeleccionados = collect();
            }

            // --- ALUMNOS (ID 4) ---
            if (in_array(4, $dependenciasSelect)) {
                $listAlumnos = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $id)
                    ->where('iddependencias_receptor', 4)
                    ->pluck('id_user_receptor')
                    ->toArray();

                $alumnosSeleccionados = DB::table('users')
                    ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
                    ->whereIn('users.id', $listAlumnos)
                    ->select(
                        'users.id as id',
                        DB::raw("CONCAT(users.dni, ' --- ', postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as text")
                    )
                    ->get();
            } else {
                $alumnosSeleccionados = collect();
            }

            // dd($docentesSeleccionados);

            $usu = DB::connection('mysql_documentario')->table('usuario')
                ->where('idusuario', '=', $queryDoc->idusuario)
                ->first();
            DB::commit();
            return view('mesaPartes.showEmitido')->with('dependencias', $dependencias)->with('detalle_tramite', $detalle_tramite)->with('queryDoc', $queryDoc)->with('queryMovi', $queryMovi)->with('dependenciasSelect', $dependenciasSelect)->with('estadoMayor', $estadoMayor)->with('queryRecepDepenFech', $queryRecepDepenFech)->with('usu', $usu)->with('rol', $rol)->with('id_depen', $id_depen)->with('cont_est', $cont_est)->with('pdfdocumento', $pdfdocumento)->with('docentesSeleccionados', $docentesSeleccionados)->with('egresadosSeleccionados', $egresadosSeleccionados) // 👈 NUEVO
                ->with('alumnosSeleccionados', $alumnosSeleccionados);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return view('mesaPartes.index');
        }
    }

    public function updateDocuEmi(Request $request, $iddocumentos)
    {
        // 1. Verificar si el que intenta enviar es Asistente (tipo_usuario = 1)
        $es_jefe_o_usuario = 0;
        $esAsistente = DB::connection('mysql_documentario')->table('dependencia_user')
            ->where('user_id', Auth::id())
            ->where('dependencia_id', session('dependencia_id'))
            ->where('tipo_usuario', 1)
            ->where('estado', 1)
            ->exists();

        if ($esAsistente) {
            // 2. Si es asistente, verificar si el JEFE de esta dependencia tiene dar_permiso = 1
            $jefeDioPermiso = DB::connection('mysql_documentario')->table('dependencia_user')
                ->where('dependencia_id', session('dependencia_id'))
                ->where('tipo_usuario', 2) // Buscamos al Jefe
                ->where('dar_permiso', 1)
                ->where('estado', 1)
                ->exists();
            // dd($jefeDioPermiso);


            if (!$jefeDioPermiso) {
                // Bloquear la acción
                return response()->json(['error' => 'Acción denegada: El Jefe del Área no ha habilitado el permiso para que los asistentes envíen documentos.'], 500);
            } else {
                $es_jefe_o_usuario = 1;
                $idJefe = DB::connection('mysql_documentario')
                    ->table('dependencia_user')
                    ->where('dependencia_id', session('dependencia_id'))
                    ->where('tipo_usuario', 2)
                    ->where('estado', 1)
                    ->value('user_id');
            }
        }

        // dd($request);
        $rules = [
            'tipo_documento'   => 'required|not_in:0',
            'folio'            => 'required|numeric|min:1',
            'asunto'           => 'required|string|min:1',
            'para_su'          => 'required|not_in:0',
            'reemplazar_pdf.*' => 'nullable|file|mimes:pdf|max:5120',
        ];

        // Validar que al menos haya un destino seleccionado
        if (!$request->filled('docentes_especificos') && !$request->filled('egresados') && !$request->filled('alumnos')) {
            $rules['dependencia_enviar'] = 'required|array|min:1';
        }

        $messages = [
            'tipo_documento.not_in'      => 'Debe seleccionar un tipo de documento válido.',
            'para_su.not_in'             => 'Debe seleccionar una opción en "Para su".',
            'dependencia_enviar.required' => 'Debe seleccionar al menos una dependencia si no hay personas específicas seleccionadas.',
            'reemplazar_pdf.*.mimes'     => 'El archivo debe ser un formato PDF válido.',
        ];

        $request->validate($rules, $messages);

        if ($es_jefe_o_usuario == 1) {
            # code...
            $id_users = $idJefe;
        } else {
            $id_users = Auth::user()->id;
        }
        $idusuario = null;
        $id_entidad_externa = null;
        $numero_documento_externo = null;
        $correo = null;
        $celular = null;

        if (session('dependencia_id') == 24) {
            if ($request->tipo_remitente_m === 'natural') {
                $idusuario = $request->usuario_m;
            } elseif ($request->tipo_remitente_m === 'juridica') {
                $id_entidad_externa = $request->id_entidad_m_externa;
                $numero_documento_externo = $request->numero_documento_externo_m;
                $correo = $request->correo_entidad_m;
                $celular = $request->celular_entidad_m;
            }
        }

        try {
            DB::connection('mysql_documentario')->beginTransaction();

            // Variables para controlar advertencias (igual que en registrarDocu)
            $advertencias = [];
            $movimientos_insertados = 0;

            // 1. Actualizar documento principal
            DB::connection('mysql_documentario')->table('documentos')
                ->where('iddocumentos', $iddocumentos)
                ->update([
                    'numero_de_exp'            => $request->num_expe,
                    'asunto'                   => $request->asunto,
                    'folio'                    => $request->folio,
                    'idtipo_documento'         => $request->tipo_documento,
                    'iddetalle_tramite'        => $request->para_su,
                    'recomendacion'            => $request->Recomendaciones,
                    'anexos_fisicos'           => $request->detalle_anexos_fisicos,
                    'idusuario'                => $idusuario,
                    'id_entidad_externa'       => $id_entidad_externa,
                    'numero_documento_externo' => $numero_documento_externo,
                    'correo'           => $correo,
                    'celular'          => $celular,
                ]);

            // 2. Lógica de Movimientos
            if ($request->filled('docentes_especificos')) {
                // Limpiar lo que NO sea docente
                $movs_eliminar = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', '!=', 2)->where('idestado', 1)
                    ->get(['idmovimiento', 'iddependencias_receptor']);

                foreach ($movs_eliminar as $mov) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('idmovimiento', $mov->idmovimiento)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$mov->iddependencias_receptor]);
                    event(new editarDocumento($mov->iddependencias_receptor, $cont, 'dependencia'));
                }

                $actuales = DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 2)->where('idestado', 1)->pluck('id_user_receptor')->toArray();
                $quito = array_diff($actuales, $request->docentes_especificos);
                $agrego = array_diff($request->docentes_especificos, $actuales);

                foreach ($quito as $depe) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 2)->where('id_user_receptor', $depe)->where('idestado', 1)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [2, $depe]);
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
                foreach ($agrego as $depa) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert(['iddocumentos' => $iddocumentos, 'iddependencias_emior' => $request->emisor, 'iddependencias_receptor' => 2, 'id_user_receptor' => $depa, 'fecha_de_envio' => now(), 'idestado' => 1]);
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [2, $depa]);
                    event(new DocumentoRecibido($depa, $cont, 'personal'));
                }

                $seMantienenDoc = array_intersect($actuales, $request->docentes_especificos);
                foreach ($seMantienenDoc as $depe) {
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [2, $depe]);
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
            } elseif ($request->filled('egresados')) {
                // Limpiar lo que NO sea egresado
                $movs_eliminar = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', '!=', 5)->where('idestado', 1)
                    ->get(['idmovimiento', 'iddependencias_receptor']);
                foreach ($movs_eliminar as $mov) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('idmovimiento', $mov->idmovimiento)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$mov->iddependencias_receptor]);
                    event(new editarDocumento($mov->iddependencias_receptor, $cont, 'dependencia'));
                }

                $actuales = DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 5)->where('idestado', 1)->pluck('id_user_receptor')->toArray();
                $quito = array_diff($actuales, $request->egresados);
                $agrego = array_diff($request->egresados, $actuales);

                foreach ($quito as $depe) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 5)->where('id_user_receptor', $depe)->where('idestado', 1)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [5, $depe]); // 👈 CORREGIDO: era 2
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
                foreach ($agrego as $depa) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert(['iddocumentos' => $iddocumentos, 'iddependencias_emior' => $request->emisor, 'iddependencias_receptor' => 5, 'id_user_receptor' => $depa, 'fecha_de_envio' => now(), 'idestado' => 1]);
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [5, $depa]); // 👈 CORREGIDO: era 2
                    event(new DocumentoRecibido($depa, $cont, 'personal'));
                }
                $seMantienenEgr = array_intersect($actuales, $request->egresados);
                foreach ($seMantienenEgr as $depe) {
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [5, $depe]);
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
            } elseif ($request->filled('alumnos')) {
                // Limpiar lo que NO sea alumno
                $movs_eliminar = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', '!=', 4)->where('idestado', 1)
                    ->get(['idmovimiento', 'iddependencias_receptor']);
                foreach ($movs_eliminar as $mov) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('idmovimiento', $mov->idmovimiento)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$mov->iddependencias_receptor]);
                    event(new editarDocumento($mov->iddependencias_receptor, $cont, 'dependencia'));
                }

                $actuales = DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 4)->where('idestado', 1)->pluck('id_user_receptor')->toArray();
                $quito = array_diff($actuales, $request->alumnos);
                $agrego = array_diff($request->alumnos, $actuales);

                foreach ($quito as $depe) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', 4)->where('id_user_receptor', $depe)->where('idestado', 1)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [4, $depe]); // 👈 CORREGIDO: era 2
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
                foreach ($agrego as $depa) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert(['iddocumentos' => $iddocumentos, 'iddependencias_emior' => $request->emisor, 'iddependencias_receptor' => 4, 'id_user_receptor' => $depa, 'fecha_de_envio' => now(), 'idestado' => 1]);
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [4, $depa]); // 👈 CORREGIDO: era 2
                    event(new DocumentoRecibido($depa, $cont, 'personal'));
                }
                $seMantienenAlu = array_intersect($actuales, $request->alumnos);
                foreach ($seMantienenAlu as $depe) {
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [4, $depe]);
                    event(new editarDocumento($depe, $cont, 'personal'));
                }
            } else {
                // ==========================================
                // DEPENDENCIAS GENÉRICAS (CON VALIDACIÓN DE JEFE)
                // ==========================================

                // A. Limpiar movimientos de personas específicas (2, 4, 5)
                $movs_personales = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)
                    ->whereIn('iddependencias_receptor', [2, 4, 5])
                    ->where('idestado', 1)
                    ->get(['idmovimiento', 'iddependencias_receptor', 'id_user_receptor']);

                foreach ($movs_personales as $mov) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('idmovimiento', $mov->idmovimiento)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$mov->iddependencias_receptor, $mov->id_user_receptor]);
                    event(new editarDocumento($mov->id_user_receptor, $cont, 'personal'));
                }

                // B. Diff para dependencias genéricas
                $moviConEseDoc = DB::connection('mysql_documentario')->table('movimiento')
                    ->where('iddocumentos', $iddocumentos)->where('idestado', 1)
                    ->pluck('iddependencias_receptor')->map(fn($id) => (string) $id)->toArray();

                // 1. Obtenemos las dependencias del request
                $requestDependencias = array_map('strval', $request->dependencia_enviar ?? []);

                // 2. 👈 FILTRO DE SEGURIDAD: Eliminamos cualquier rastro de 2, 4 o 5 que haya colado
                $requestDependencias = array_values(array_filter($requestDependencias, function ($id) {
                    return !in_array($id, ['2', '4', '5']);
                }));
                $quitoDep = array_diff($moviConEseDoc, $requestDependencias);
                $agregoDep = array_diff($requestDependencias, $moviConEseDoc);

                // C. Quitar dependencias
                foreach ($quitoDep as $depe) {
                    DB::connection('mysql_documentario')->table('movimiento')->where('iddocumentos', $iddocumentos)->where('iddependencias_receptor', $depe)->where('idestado', 1)->delete();
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$depe]);
                    event(new editarDocumento($depe, $cont, 'dependencia'));
                }

                // D. Agregar dependencias con validación estricta de jefe
                foreach ($agregoDep as $depa) {
                    $nombre_dep = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $depa)->value('nombre_dependencia') ?? 'Desconocida';
                    $jefes_count = DB::connection('mysql_documentario')->table('dependencia_user')->where('dependencia_id', $depa)->where('tipo_usuario', 2)->where('estado', 1)->count();

                    if ($jefes_count === 1) {
                        $id_user_receptor = DB::connection('mysql_documentario')->table('dependencia_user')->where('dependencia_id', $depa)->where('tipo_usuario', 2)->where('estado', 1)->value('user_id');

                        DB::connection('mysql_documentario')->table('movimiento')->insert([
                            'iddocumentos' => $iddocumentos,
                            'iddependencias_emior' => $request->emisor,
                            'iddependencias_receptor' => $depa,
                            'id_user_receptor' => $id_user_receptor,
                            'fecha_de_envio' => now(),
                            'idestado' => 1
                        ]);
                        $movimientos_insertados++;

                        $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$depa]);
                        event(new DocumentoRecibido($depa, $cont, 'dependencia'));
                    } elseif ($jefes_count === 0) {
                        $advertencias[] = "La dependencia '<b>{$nombre_dep}</b>' no tiene un jefe activo asignado. No se realizó el envío.";
                    } else {
                        $advertencias[] = "La dependencia '<b>{$nombre_dep}</b>' tiene múltiples jefes activos. Por seguridad, no se realizó el envío.";
                    }
                }
                $seMantienenDep = array_intersect($moviConEseDoc, $requestDependencias);
                foreach ($seMantienenDep as $depa) {
                    $cont = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$depa]);
                    event(new editarDocumento($depa, $cont, 'dependencia'));
                }
            }

            // 3. Actualización de PDFs
            if ($request->hasFile('reemplazar_pdf')) {
                foreach ($request->file('reemplazar_pdf') as $idPdf => $nuevoArchivo) {
                    if ($nuevoArchivo->isValid()) {
                        $pdfActual = DB::connection('mysql_documentario')->table('documenpdf')->where('iddocumenpdf', $idPdf)->first();
                        if ($pdfActual) {
                            $rutaCarpeta = public_path('documentos/documentos_director_pdf/');
                            if (file_exists($rutaCarpeta . $pdfActual->nombre_del_documento)) {
                                unlink($rutaCarpeta . $pdfActual->nombre_del_documento);
                            }
                            $nuevoNombre = $id_users . '_' . session('dependencia_id') . '_' . time() . '_' . uniqid() . '.' . $nuevoArchivo->getClientOriginalExtension();
                            $nuevoArchivo->move($rutaCarpeta, $nuevoNombre);

                            DB::connection('mysql_documentario')->table('documenpdf')->where('iddocumenpdf', $idPdf)->update([
                                'nombre_del_documento' => $nuevoNombre,
                                'fecha_actualizacion' => now(),
                                'usuario_id' => $id_users
                            ]);
                        }
                    }
                }
            }

            if ($movimientos_insertados === 0 && count($advertencias) > 0) {
                DB::connection('mysql_documentario')->rollBack();

                $errorMsg = 'No se pudo enviar el documento a ninguna de las dependencias seleccionadas. Motivos:';

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $errorMsg,
                        'warnings' => $advertencias // <-- Aquí va la lista de dependencias con 0 o +1 jefes
                    ], 422);
                }
                return redirect()->back()->with('error', $errorMsg)->with('warnings', $advertencias)->withInput();
            }

            DB::connection('mysql_documentario')->commit();

            // 5. Respuesta al frontend
            if ($request->ajax()) {
                if (count($advertencias) > 0) {
                    return response()->json([
                        'success' => 'Documento actualizado, pero con advertencias en algunas dependencias.',
                        'warnings' => $advertencias
                    ]);
                }
                return response()->json(['success' => 'Documento actualizado con éxito']);
            }

            if (count($advertencias) > 0) {
                return redirect()->back()->with('warning', 'Documento actualizado con advertencias.')->with('warnings', $advertencias);
            }

            return redirect()->back()->with('success', 'Actualizado con éxito');
        } catch (\Throwable $th) {
            DB::connection('mysql_documentario')->rollBack();
            Log::error("Error en actualización: " . $th->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al actualizar: ' . $th->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar los datos');
        }
    }
}
