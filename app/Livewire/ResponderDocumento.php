<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Events\DocumentoRecibido;


class ResponderDocumento extends Component
{
    use WithFileUploads;

    // Propiedades públicas
    public $iddocument;
    public $dependencia;
    public $folio;
    public $asunto;
    public $oficina_destino;
    public $tramite_documento;
    public $recomendaciones;
    public $entregaOpciones;
    public $archivo_virtual;

    public $dependencias;
    public $detalledocumento;
    public $id_depen;

    public $tiposDocumento;
    public $correlativoPreview;
    public $idTipoDocumento; 
public $referenciaTexto = null;
public $agregarReferencia = false;
  


    public $tipo_destinatario = 'dependencia'; // dependencia, docente, egresado
       public $entidad_externa_nombre = '';
    public $entidad_externa_correo = '';
    public $entidad_externa_celular = '';
    
    // Para docentes
    public $busquedaDocente = '';
    public $resultadosDocentes = [];
    public $docentes_seleccionados = [];
    public $docentesSeleccionadosInfo = [];
    
    // Para egresados
    public $busquedaEgresado = '';
    public $resultadosEgresados = [];
    public $egresados_seleccionados = [];
    public $egresadosSeleccionadosInfo = [];
public $mensajeErrorJefe = null;

  public function updated($property, $value)
    {
        if ($property === 'idTipoDocumento') {
            $this->calcularCorrelativo($value);
        }
        
        // 👇 Búsqueda en tiempo real para docentes
        if ($property === 'busquedaDocente' && strlen($value) >= 2) {
            $this->buscarDocentes();
        }
        
        // 👇 Búsqueda en tiempo real para egresados
        if ($property === 'busquedaEgresado' && strlen($value) >= 2) {
            $this->buscarEgresados();
        }
    }

public function calcularCorrelativo($value = null)
{
    $valor = $value ?? $this->idTipoDocumento;

    if ($valor) {
        $ultimoExpediente = DB::connection('mysql_documentario')->table('documentos')
            ->where('emisor', $this->dependencia)
            ->where('idtipo_documento', $valor)
            ->orderBy('idDocumentos', 'desc')
            ->value('numero_de_exp');

        // Calculamos el siguiente y lo formateamos a 3 dígitos (ej: 001, 002, 015)
        $nextNumber = $ultimoExpediente ? (int)$ultimoExpediente + 1 : 1;
        $this->correlativoPreview = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $this->correlativoPreview = null;
    }
}

public function buscarDocentes()
{
    // 1. Validación de seguridad
    if (strlen($this->busquedaDocente) < 2) {
        $this->resultadosDocentes = [];
        return;
    }

    // 2. Consulta corregida para buscar por Nombre O por DNI
    $this->resultadosDocentes = DB::table('users')
        ->join('gamnielb_sia.userprofile', 'users.id', '=', 'gamnielb_sia.userprofile.id_users')
        ->where(function($query) {
            $query->where('gamnielb_sia.userprofile.nombre', 'like', "%{$this->busquedaDocente}%")
                  ->orWhere('users.dni', 'like', "%{$this->busquedaDocente}%"); // <-- ¡Aquí está la clave para el DNI!
        })
        ->select(
            'gamnielb_sia.userprofile.id_users as id', 
            'gamnielb_sia.userprofile.nombre as nombre',
            'users.dni' // Lo agregamos por si quieres mostrarlo en el futuro
        )
        ->limit(10)
        ->get()
        ->toArray();
}

    public function agregarDocente($id)
    {
        if (!in_array($id, $this->docentes_seleccionados)) {
            $this->docentes_seleccionados[] = $id;
            
            $docente = DB::table('users')
                ->join('gamnielb_sia.userprofile', 'users.id', '=', 'gamnielb_sia.userprofile.id_users')
                ->where('gamnielb_sia.userprofile.id_users', $id)
                ->select('gamnielb_sia.userprofile.id_users as id', 'gamnielb_sia.userprofile.nombre as nombre')
                ->first();
            
            if ($docente) {
                $this->docentesSeleccionadosInfo[$id] = $docente->nombre;
            }
        }
        
        $this->busquedaDocente = '';
        $this->resultadosDocentes = [];
    }

    public function removerDocente($id)
    {
        $this->docentes_seleccionados = array_values(array_diff($this->docentes_seleccionados, [$id]));
        unset($this->docentesSeleccionadosInfo[$id]);
    }

    public function buscarEgresados()
    {
        if (strlen($this->busquedaEgresado) < 2) {
            $this->resultadosEgresados = [];
            return;
        }

        $this->resultadosEgresados = DB::table('users')
            ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where(function ($query) {
                $query->where(DB::raw("CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante)"), 'like', "%{$this->busquedaEgresado}%")
                    ->orWhere('users.dni', 'like', "%{$this->busquedaEgresado}%");
            })
            ->where('model_has_roles.role_id', '=', 5)
            ->select(
                'users.id as id',
                DB::raw("CONCAT(users.dni, ' - ', postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as nombre")
            )
            ->limit(10)
            ->get()
            ->toArray();
    }

     public function agregarEgresado($id)
    {
        if (!in_array($id, $this->egresados_seleccionados)) {
            $this->egresados_seleccionados[] = $id;
            
            $egresado = DB::table('users')
                ->join('postulante', 'users.dni', '=', 'postulante.idpostulante')
                ->where('users.id', $id)
                ->select(
                    'users.id as id',
                    DB::raw("CONCAT(users.dni, ' - ', postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) as nombre")
                )
                ->first();
            
            if ($egresado) {
                $this->egresadosSeleccionadosInfo[$id] = $egresado->nombre;
            }
        }
        
        $this->busquedaEgresado = '';
        $this->resultadosEgresados = [];
    }

     public function removerEgresado($id)
    {
        $this->egresados_seleccionados = array_values(array_diff($this->egresados_seleccionados, [$id]));
        unset($this->egresadosSeleccionadosInfo[$id]);
    }
        public function mount($documento, $dependencias, $detalledocumento, $id_depen)
    {
        $this->iddocument = $documento->iddocumentos;
        $this->dependencia = $id_depen; 
        $this->dependencias = $dependencias;
        $this->detalledocumento = $detalledocumento;
        $this->id_depen = $id_depen;

        $this->tiposDocumento = DB::connection('mysql_documentario')->table('tipo_documento')->get();
        $this->cargarReferencia();

        // ✅ BUSCAR EL DOCUMENTO MATRIZ (original) para obtener datos de entidad externa
        $documentoMatriz = $this->obtenerDocumentoMatriz($documento->iddocumentos);

        if ($documentoMatriz) {
            $this->entidad_externa_correo = $documentoMatriz->correo ?? '';
            $this->entidad_externa_celular = $documentoMatriz->celular ?? '';
            
            $this->entidad_externa_nombre = 'Entidad Externa';
            if (!empty($documentoMatriz->id_entidad_externa)) {
                $nombreEntidad = DB::connection('mysql_documentario')
                    ->table('entidades_externas')
                    ->where('id', $documentoMatriz->id_entidad_externa)
                    ->value('razon_social_nombre'); 
                
                if ($nombreEntidad) {
                    $this->entidad_externa_nombre = $nombreEntidad;
                }
            }
        }
    }

        protected function rules()
    {
        $rules = [
            'folio' => 'required|numeric',
            'asunto' => 'required|string|min:5',
            // 'recomendaciones' => 'required|string|min:5',
            'entregaOpciones' => 'required|integer',
            'archivo_virtual' => 'nullable|file|mimes:pdf|max:2048',
            'idTipoDocumento' => 'required|integer',
            'tipo_destinatario' => 'required|in:dependencia,docente,egresado,externo', // ✅ Agregado 'externo'
        ];

        if ($this->tipo_destinatario === 'dependencia') {
            $rules['oficina_destino'] = 'required|integer';
        } elseif ($this->tipo_destinatario === 'docente') {
            $rules['docentes_seleccionados'] = 'required|array|min:1';
        } elseif ($this->tipo_destinatario === 'egresado') {
            $rules['egresados_seleccionados'] = 'required|array|min:1';
        } elseif ($this->tipo_destinatario === 'externo') {
            $rules['entidad_externa_correo'] = 'required|email';
        }

        return $rules;
    }

         public function submit()
    {
        $this->validate();

        // ✅ 1. VALIDACIÓN DE SEGURIDAD: Correo vacío para externos
        if ($this->tipo_destinatario === 'externo' && empty($this->entidad_externa_correo)) {
            session()->flash('error', 'No se puede enviar: El documento original no tiene un correo electrónico registrado. Solicite a Mesa de Partes que actualice el registro.');
            return;
        }

        // ✅ 2. VALIDACIÓN DE SEGURIDAD: Jefes para dependencias internas
        if ($this->tipo_destinatario === 'dependencia') {
            $nombreDependencia = DB::connection('mysql_documentario')->table('dependencias')
                ->where('iddependencias', $this->oficina_destino)
                ->value('nombre_dependencia') ?? 'Desconocida';

            $jefesCount = DB::connection('mysql_documentario')->table('dependencia_user')
                ->where('dependencia_id', $this->oficina_destino)
                ->where('tipo_usuario', 2) 
                ->where('estado', 1)       
                ->count();

            if ($jefesCount === 0) {
                $this->dispatch('mostrarAlertaJefe', mensaje: "No se puede enviar el documento. La dependencia '{$nombreDependencia}' NO tiene un jefe activo asignado.");
                return; 
            }

            if ($jefesCount > 1) {
                $this->dispatch('mostrarAlertaJefe', mensaje: "Envío bloqueado por seguridad. La dependencia '{$nombreDependencia}' tiene MÚLTIPLES jefes activos.");
                return; 
            }
        }

        try {
            DB::connection('mysql_documentario')->beginTransaction();

            // Obtener último expediente
            $ultimoExpediente = DB::connection('mysql_documentario')->table('documentos')
                ->where('emisor', $this->dependencia)
                ->where('idtipo_documento', $this->idTipoDocumento)
                ->where('est_firma', 1)
                ->orderBy('idDocumentos', 'desc')
                ->value('numero_de_exp');

            $nuevoExpediente = $ultimoExpediente 
                ? str_pad((int)$ultimoExpediente + 1, 3, '0', STR_PAD_LEFT) 
                : '001';

            // ✅ 3. INSERTAR NUEVO DOCUMENTO (Incluyendo correo y celular para historial)
            $ultimoIdDocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                'numero_de_exp'          => $nuevoExpediente,
                'fecha_ingreso'          => now(),
                'asunto'                 => $this->asunto,
                'idtipo_documento'       => $this->idTipoDocumento,
                'emisor'                 => $this->dependencia,
                'iddetalle_tramite'      => $this->tramite_documento,
                'id_user'                => Auth::id(),
                'recomendacion'          => $this->recomendaciones,
                'iddocumento_referencia' => $this->iddocument,
                'folio'                  => $this->folio,
                'estado_actu'            => 1,
                'est_firma'              => 1,
                'correo'                 => $this->entidad_externa_correo, // ✅ Historial
                'celular'                => $this->entidad_externa_celular, // ✅ Historial
            ]);

            // ✅ 4. ACTUALIZAR MOVIMIENTO ORIGINAL A FINALIZADO (Estado 3)
            $usuarioQueResponde = Auth::id();
            $fechaHoraActual = now();

            DB::connection('mysql_documentario')->table('movimiento')
                ->where('iddocumentos', $this->iddocument)
                ->where('idestado', '!=', 3)
                ->update([
                    'idestado'           => 3,
                    'fecha_finalizacion' => $fechaHoraActual,
                    'id_user_finali'     => $usuarioQueResponde
                ]);

            // ✅ 5. LÓGICA DE ENVÍO SEGÚN DESTINATARIO
            if ($this->tipo_destinatario === 'docente') {
                foreach ($this->docentes_seleccionados as $id_user_docente) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $ultimoIdDocumento, 'iddependencias_emior' => $this->dependencia,
                        'iddependencias_receptor' => 2, 'id_user_receptor' => $id_user_docente,
                        'fecha_de_envio' => now(), 'idestado' => 1
                    ]);
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_user_docente]);
                    event(new DocumentoRecibido($id_user_docente, $cont_estados, 'personal'));
                }
            } elseif ($this->tipo_destinatario === 'egresado') {
                foreach ($this->egresados_seleccionados as $id_user_egresado) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $ultimoIdDocumento, 'iddependencias_emior' => $this->dependencia,
                        'iddependencias_receptor' => 5, 'id_user_receptor' => $id_user_egresado,
                        'fecha_de_envio' => now(), 'idestado' => 1
                    ]);
                    $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.id_user_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado;', [$id_user_egresado]);
                    event(new DocumentoRecibido($id_user_egresado, $cont_estados, 'personal'));
                }
            } elseif ($this->tipo_destinatario === 'dependencia') {
                $id_user_receptor = DB::connection('mysql_documentario')->table('dependencia_user')
                    ->where('dependencia_id', $this->oficina_destino)->where('tipo_usuario', 2)->where('estado', 1)->value('user_id');
                DB::connection('mysql_documentario')->table('movimiento')->insert([
                    'iddocumentos' => $ultimoIdDocumento, 'iddependencias_emior' => $this->dependencia,
                    'iddependencias_receptor' => $this->oficina_destino, 'id_user_receptor' => $id_user_receptor,
                    'fecha_de_envio' => now(), 'fecha_de_recepcion' => null, 'idestado' => 1
                ]);
                $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado FROM estado LEFT JOIN movimiento ON movimiento.idestado = estado.idestado AND movimiento.iddependencias_receptor = ? WHERE estado.idestado IN (1,2,3) GROUP BY estado.idestado', [$this->oficina_destino]);
                event(new DocumentoRecibido($this->oficina_destino, $cont_estados, 'dependencia'));
            }
            // ✅ SI ES EXTERNO, NO CREAMOS MOVIMIENTO INTERNO, YA SE FINALIZÓ ARRIBA (Estado 3).

            // ✅ 6. MANEJO DEL ARCHIVO PDF
            $rutaPdfGuardado = null;
            if ($this->archivo_virtual) {
                $nombreOriginal = $this->archivo_virtual->getClientOriginalName();
                $nombreLimpio = preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $nombreOriginal);
                $nombreArchivo = time() . '_' . $nombreLimpio;
                
                $directorio = public_path('documentos' . DIRECTORY_SEPARATOR . 'documentos_director_pdf');
                $rutaTemporal = $this->archivo_virtual->getRealPath(); 
                $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;

                if (!\Illuminate\Support\Facades\File::exists($directorio)) {
                    \Illuminate\Support\Facades\File::makeDirectory($directorio, 0755, true, true);
                }

                if (!copy($rutaTemporal, $rutaDestino)) {
                    throw new \Exception("No se pudo guardar el PDF. Verifica permisos de la carpeta.");
                }

                DB::connection('mysql_documentario')->table('documenpdf')->insert([
                    'nombre_del_documento' => $nombreArchivo, 'fecha_subida' => now(),
                    'iddocumentos' => $ultimoIdDocumento, 'token_pdf' => \Illuminate\Support\Str::uuid(),
                    'tipo_pdf' => 1, 'usuario_id' => Auth::id()
                ]);
                $rutaPdfGuardado = $rutaDestino;
            }
            
            DB::connection('mysql_documentario')->commit();
            
            // ✅ 7. PREPARAR REDIRECCIÓN Y ACCIONES POSTERIORES PARA EXTERNOS
                                   if ($this->tipo_destinatario === 'externo') {
                // ✅ OBTENER EL DOCUMENTO MATRIZ PARA LOS DATOS DE REFERENCIA
                $documentoMatriz = $this->obtenerDocumentoMatriz($this->iddocument);
                
                $numeroDocumentoExterno = $documentoMatriz->numero_documento_externo ?? 'S/N';
                $expedienteOriginal = $documentoMatriz->numero_de_exp ?? 'S/N';

                $asuntoCorreo = "Respuesta Oficial - Exp. N° {$nuevoExpediente} - " . $this->asunto;
                
                $cuerpoCorreo = "Estimados {$this->entidad_externa_nombre},\n\n" .
                    "Por medio de la presente, adjuntamos la respuesta oficial al documento de su referencia:\n\n" .
                    "📄 Documento Externo N°: {$numeroDocumentoExterno}\n" .
                    "📋 Expediente Interno N°: {$expedienteOriginal}\n\n" .
                    "Asunto de la respuesta: {$this->asunto}\n\n" .
                    "Observaciones / Recomendaciones:\n{$this->recomendaciones}\n\n" .
                    "Atentamente,\nDirección General.";

                // ... resto del código (gmailLink, redirect, etc.)

                $gmailLink = "https://mail.google.com/mail/?view=cm&fs=1" .
                    "&to=" . urlencode($this->entidad_externa_correo) .
                    "&su=" . urlencode($asuntoCorreo) .
                    "&body=" . urlencode($cuerpoCorreo);

                return redirect()->route('documentario.mesapar.bandeja')
                    ->with('success', "Documento respondido y listo para envío externo (Exp: {$nuevoExpediente}).")
                    ->with('gmail_link', $gmailLink)
                    ->with('pdf_descargar', $rutaPdfGuardado); // Para auto-descarga
            }

            return redirect()->route('documentario.mesapar.bandeja')
                ->with('success', "Documento respondido y enviado exitosamente con N° Exp: $nuevoExpediente");

        } catch (\Exception $e) {
            DB::connection('mysql_documentario')->rollBack();
            Log::error('Error al enviar documento: ' . $e->getMessage());
            session()->flash('error', "Error al insertar datos: " . $e->getMessage());
            return null; 
        }
    }

    public function getCustomPreviewUrl()
{
    if (!$this->archivo_virtual) return null;
    
    // Obtenemos solo el nombre del archivo temporal de Livewire
    $filename = $this->archivo_virtual->getFilename();
    return route('documentario.pdf.preview', ['filename' => $filename]);
}

public function generarWord()
{
    try {
        // 1. RUTA DE LA PLANTILLA
        $templatePath = storage_path('app/templates/responder.docx');

        if (!file_exists($templatePath)) {
            session()->flash('error', 'No se encuentra la plantilla Word en: ' . $templatePath);
            return;
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // 2. DATOS DE CONFIGURACIÓN (LOGO Y AÑO)
        $configuracion = DB::connection('mysql_segunda')
            ->table('encargados')
            ->where('estado', 1)
            ->first();

        if ($configuracion) {
            $textoAnio = $configuracion->nombre_año ?? $configuracion->nombre_anio ?? 'AÑO VIGENTE';
            $resoDirec = $configuracion->reso_direc ?? $configuracion->resolucion ?? 'RESOLUCIÓN DIRECTORAL';
            
            $template->setValue('texto_anio_oficial', $textoAnio);
            $template->setValue('nombre_anio', $resoDirec);
            
            if (!empty($configuracion->logo)) {
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
        }

        // 3. DATOS DEL EMISOR (Quien redacta)
        $dependenciaEmisor = DB::connection('mysql_documentario')
            ->table('dependencias')
            ->where('iddependencias', $this->dependencia)
            ->first();

        $perfilUsuario = DB::connection('mysql_segunda')
            ->table('userprofile')
            ->where('id_users', Auth::id())
            ->first();

        // 4. DATOS DEL DESTINATARIO (Oficina de destino seleccionada)
        $dependenciaReceptor = DB::connection('mysql_documentario')
            ->table('dependencias')
            ->where('iddependencias', $this->oficina_destino)
            ->first();

        $nombreDestinatario = $dependenciaReceptor ? strtoupper($dependenciaReceptor->nombre_dependencia) : 'DESTINATARIO';
        $cargoDestinatario = $dependenciaReceptor ? strtoupper($dependenciaReceptor->nombre_dependencia) : 'CARGO / DEPENDENCIA';

        // 5. DATOS DEL TIPO DE DOCUMENTO Y CORRELATIVO
        $tipoDoc = DB::connection('mysql_documentario')
            ->table('tipo_documento')
            ->where('idtipo_documento', $this->idTipoDocumento)
            ->first();

        $nombreTipoDoc = $tipoDoc ? strtoupper($tipoDoc->nombre_documento) : 'DOCUMENTO';
        
        // Formateamos el correlativo a 3 dígitos (ej: 001) usando el preview que ya calculamos
        $nroFormateado = str_pad($this->correlativoPreview ?? 1, 3, "0", STR_PAD_LEFT);

        // 6. ASIGNACIÓN DE VALORES A LA PLANTILLA WORD
        $template->setValue('tipo_doc', $nombreTipoDoc);
        $template->setValue('nro_doc', $nroFormateado);
        $template->setValue('anio', date('Y'));
        $template->setValue('siglas', $dependenciaEmisor->siglas ?? 'S/N');
        
        $template->setValue('destinatario_nombre', $nombreDestinatario);
        $template->setValue('destinatario_cargo', $cargoDestinatario);
        
        $template->setValue('usuario_nombre', strtoupper($perfilUsuario->nombre ?? Auth::user()->name ?? 'USUARIO'));
        $template->setValue('dependencia', strtoupper($dependenciaEmisor->nombre_dependencia ?? 'SIN DEPENDENCIA'));
        
        $template->setValue('asunto', strtoupper($this->asunto ?? 'SIN ASUNTO'));
        $template->setValue('folio', $this->folio ?? '0');
        $template->setValue('fecha', now()->translatedFormat('d \d\e F \d\e\l Y'));
        
        // Lógica de referencia
        if ($this->agregarReferencia && !empty($this->referenciaTexto)) {
            $template->setValue('referencia', "REFERENCIA     : " . strtoupper($this->referenciaTexto));
        } else {
            $template->setValue('referencia', '');
        }

        // 7. GENERACIÓN Y DESCARGA DEL ARCHIVO
        $fileName = 'Borrador_' . ($nombreTipoDoc ?? 'Doc') . '_' . $nroFormateado . '_' . time() . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        
        $template->saveAs($tempFile);

        // En Livewire, devolver un response()->download() desde un método público funciona perfectamente
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('Error al generar Word en ResponderDocumento: ' . $e->getMessage());
        session()->flash('error', 'Error al generar el documento: ' . $e->getMessage());
        return null;
    }
}

public function cargarReferencia()
{
    if ($this->iddocument) {
        $doc = DB::connection('mysql_documentario')->table('documentos')
            ->join('dependencias', 'documentos.emisor', '=', 'dependencias.iddependencias')
            ->select('documentos.numero_de_exp', 'dependencias.nombre_dependencia')
            ->where('documentos.iddocumentos', $this->iddocument)
            ->first();

        if ($doc) {
            $this->referenciaTexto = "Documento N° {$doc->numero_de_exp} - {$doc->nombre_dependencia}";
        }
    }
}

    /**
     * Busca el documento matriz (original) siguiendo la cadena de referencias.
     * Esto es necesario cuando un documento pasa por varias oficinas antes de ser respondido.
     */
    private function obtenerDocumentoMatriz($idDocumento)
    {
        $docActual = DB::connection('mysql_documentario')
            ->table('documentos')
            ->where('iddocumentos', $idDocumento)
            ->first();

        if (!$docActual) return null;

        // Si tiene documento de referencia, seguimos la cadena hacia atrás
        // (con un límite de 10 niveles para evitar bucles infinitos)
        $contador = 0;
        while ($docActual && !empty($docActual->iddocumento_referencia) && $contador < 10) {
            $docActual = DB::connection('mysql_documentario')
                ->table('documentos')
                ->where('iddocumentos', $docActual->iddocumento_referencia)
                ->first();
            $contador++;
        }

        return $docActual;
    }


    public function render()
    {
        return view('livewire.responder-documento');
    }
}