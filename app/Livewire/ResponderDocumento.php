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
    
    // ✅ ESTO ESTÁ BIEN: El NUEVO emisor eres TÚ (la dependencia 8)
    $this->dependencia = $id_depen; 
    
    $this->dependencias = $dependencias;
    $this->detalledocumento = $detalledocumento;
    $this->id_depen = $id_depen;

    $this->tiposDocumento = DB::connection('mysql_documentario')->table('tipo_documento')->get();
    $this->cargarReferencia();
}

    protected function rules()
    {
        $rules = [
            'folio' => 'required|numeric',
            'asunto' => 'required|string|min:5',
            'recomendaciones' => 'required|string|min:5',
            'entregaOpciones' => 'required|integer',
            'archivo_virtual' => 'nullable|file|mimes:pdf|max:2048',
            'idTipoDocumento' => 'required|integer',
            'tipo_destinatario' => 'required|in:dependencia,docente,egresado',
        ];

        if ($this->tipo_destinatario === 'dependencia') {
            $rules['oficina_destino'] = 'required|integer';
        } elseif ($this->tipo_destinatario === 'docente') {
            $rules['docentes_seleccionados'] = 'required|array|min:1';
        } elseif ($this->tipo_destinatario === 'egresado') {
            $rules['egresados_seleccionados'] = 'required|array|min:1';
        }

        return $rules;
    }

    public function submit()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Obtener último expediente por tipo de documento
            $ultimoExpediente = DB::connection('mysql_documentario')->table('documentos')
            ->where('emisor', $this->dependencia)
            ->where('idtipo_documento', $this->idTipoDocumento)
            ->where('est_firma', 1) // 👈 Solo contamos los que son para firma
            ->orderBy('idDocumentos', 'desc')
            ->value('numero_de_exp');

            $nuevoExpediente = $ultimoExpediente 
            ? str_pad((int)$ultimoExpediente + 1, 3, '0', STR_PAD_LEFT) 
            : '001';

            // Insertar nuevo documento
                      // Insertar nuevo documento (LA RESPUESTA)
            $ultimoIdDocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                'numero_de_exp'          => $nuevoExpediente,
                'fecha_ingreso'          => now(),
                'asunto'                 => $this->asunto,
                'idtipo_documento'       => $this->idTipoDocumento,
                'emisor'                 => $this->dependencia, // Ahora sí es tu dependencia (Ej: 8)
                'iddetalle_tramite'      => $this->tramite_documento,
                
                // 👇 AQUÍ ESTÁN LOS CAMPOS DE USUARIO Y ESTADO (Agregados)
                'id_user'                => Auth::id(),         // El usuario del sistema que realiza la acción
                      
                
                'recomendacion'          => $this->recomendaciones,
                'iddocumento_referencia' => $this->iddocument,  // Vinculado al documento original
                'folio'                  => $this->folio,
                'estado_actu'            => 1,                  // Estado inicial del nuevo documento
                'est_firma'              => 1,                  // IMPORTANTE: Al responder, generas un doc propio para firma
            ]);

            // Actualizar documento anterior
            DB::connection('mysql_documentario')->update("
                UPDATE movimiento
                SET idestado = 3, fecha_finalizacion = now()
                WHERE iddocumentos = ?
            ", [$this->iddocument]);

            DB::connection('mysql_documentario')->update("
                UPDATE documentos
                SET fecha_finalizacion = now()
                WHERE iddocumentos = ?
            ", [$this->iddocument]);

     if ($this->tipo_destinatario === 'docente') {
                foreach ($this->docentes_seleccionados as $id_user_docente) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $ultimoIdDocumento,
                        'iddependencias_emior' => $this->dependencia,
                        'iddependencias_receptor' => 2, // ID de docentes
                        'id_user_receptor' => $id_user_docente,
                        'fecha_de_envio' => now(),
                        'idestado' => 1
                    ]);

                    $cont_estados = DB::connection('mysql_documentario')->select('
                        SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                        FROM estado
                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                        AND movimiento.id_user_receptor = ?
                        WHERE estado.idestado IN (1,2,3)
                        GROUP BY estado.idestado;
                    ', [$id_user_docente]);

                    event(new DocumentoRecibido($id_user_docente, $cont_estados, 'personal'));
                }
            } elseif ($this->tipo_destinatario === 'egresado') {
                foreach ($this->egresados_seleccionados as $id_user_egresado) {
                    DB::connection('mysql_documentario')->table('movimiento')->insert([
                        'iddocumentos' => $ultimoIdDocumento,
                        'iddependencias_emior' => $this->dependencia,
                        'iddependencias_receptor' => 5, // ID de egresados
                        'id_user_receptor' => $id_user_egresado,
                        'fecha_de_envio' => now(),
                        'idestado' => 1
                    ]);

                    $cont_estados = DB::connection('mysql_documentario')->select('
                        SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                        FROM estado
                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                        AND movimiento.id_user_receptor = ?
                        WHERE estado.idestado IN (1,2,3)
                        GROUP BY estado.idestado;
                    ', [$id_user_egresado]);

                    event(new DocumentoRecibido($id_user_egresado, $cont_estados, 'personal'));
                }
            } else {
                // Envío normal a dependencia
                DB::connection('mysql_documentario')->table('movimiento')->insert([
                    'iddocumentos' => $ultimoIdDocumento,
                    'iddependencias_emior' => $this->dependencia,
                    'iddependencias_receptor' => $this->oficina_destino,
                    'fecha_de_envio' => now(),
                    'fecha_de_recepcion' => null,
                    'idestado' => 1
                ]);

                $cont_estados = DB::connection('mysql_documentario')->select('
                    SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                    FROM estado
                    LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                    AND movimiento.iddependencias_receptor = ?
                    WHERE estado.idestado IN (1,2,3)
                    GROUP BY estado.idestado;
                ', [$this->oficina_destino]);

                 event(new DocumentoRecibido($this->oficina_destino, $cont_estados, 'dependencia'));
            }

                      // ✅ GUARDAR ARCHIVO PDF (Método infalible para Windows/XAMPP)
            if ($this->archivo_virtual) {
                // 1. Limpiar nombre del archivo
                $nombreOriginal = $this->archivo_virtual->getClientOriginalName();
                $nombreLimpio = preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $nombreOriginal);
                $nombreArchivo = time() . '_' . $nombreLimpio;
                
                // 2. Definir rutas
                $directorio = public_path('documentos' . DIRECTORY_SEPARATOR . 'documentos_director_pdf');
                $rutaTemporal = $this->archivo_virtual->getRealPath(); // Ruta real del archivo temporal de Livewire
                $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;

                // 3. Crear el directorio de forma robusta con Laravel (si no existe)
                if (!\Illuminate\Support\Facades\File::exists($directorio)) {
                    \Illuminate\Support\Facades\File::makeDirectory($directorio, 0755, true, true);
                }

                // 4. Usar copy() en lugar de move() (mucho más estable en Windows)
                if (!copy($rutaTemporal, $rutaDestino)) {
                    throw new \Exception("No se pudo guardar el PDF. Verifica que la carpeta 'public/documentos/documentos_director_pdf' tenga permisos de escritura.");
                }

                // 5. Guardar el registro en la base de datos
                DB::connection('mysql_documentario')->table('documenpdf')->insert([
                    'nombre_del_documento' => $nombreArchivo,
                    'fecha_subida'         => now(),
                    'iddocumentos'         => $ultimoIdDocumento,
                    'token_pdf'            => \Illuminate\Support\Str::uuid(),
                    'tipo_pdf'             => 1, // 1 para virtual
                    'usuario_id'           => Auth::id()
                ]);
            }
            
            DB::commit();
            
            // 👇 REDIRECCIÓN A LA BANDEJA CON MENSAJE DE ÉXITO
            return redirect()->route('documentario.mesapar.bandeja')
                ->with('success', "Documento respondido y enviado exitosamente con N° Exp: $nuevoExpediente");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al enviar documento: ' . $e->getMessage());
            
            // Si hay error, nos quedamos en la misma vista y mostramos la alerta roja
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


    public function render()
    {
        return view('livewire.responder-documento');
    }
}