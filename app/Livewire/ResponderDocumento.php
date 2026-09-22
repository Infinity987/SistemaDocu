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
    public $idTipoDocumento; // 👈 Única propiedad para tipo de documento
public $referenciaTexto = null;
public $agregarReferencia = false;
  


  public function updated($property, $value)
{
    if ($property === 'idTipoDocumento') {
        $this->calcularCorrelativo($value);
    }
}

public function calcularCorrelativo($value = null)
{
    $valor = $value ?? $this->idTipoDocumento;

    if ($valor) {
        $ultimoExpediente =DB:: connection('mysql_documentario')->table('documentos')
            ->where('emisor', $this->dependencia)
            ->where('idtipo_documento', $valor)
            ->orderBy('idDocumentos', 'desc')
            ->value('numero_de_exp');

        $this->correlativoPreview = $ultimoExpediente ? $ultimoExpediente + 1 : 1;
    } else {
        $this->correlativoPreview = null;
    }
}

    public function mount($documento, $dependencias, $detalledocumento, $id_depen)
    {
        $this->iddocument = $documento->iddocumentos;
        $this->dependencia = $documento->emisor;
        $this->dependencias = $dependencias;
        $this->detalledocumento = $detalledocumento;
        $this->id_depen = $id_depen;

        $this->tiposDocumento = DB::connection('mysql_documentario')->table('tipo_documento')->get();
        $this->cargarReferencia();

    }

    protected $rules = [
        'folio' => 'required|numeric',
        'asunto' => 'required|string|min:5',
        'oficina_destino' => 'required|integer',
        'recomendaciones' => 'required|string|min:5',
        'entregaOpciones' => 'required|integer',
        'archivo_virtual' => 'nullable|file|mimes:pdf|max:2048',
        'idTipoDocumento' => 'required|integer', // 👈 validación
    ];

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
           $ultimoIdDocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
            'numero_de_exp' => $nuevoExpediente,
            'fecha_ingreso' => now(),
            'asunto' => $this->asunto,
            'idtipo_documento' => $this->idTipoDocumento,
            'emisor' => $this->dependencia,
            'iddetalle_tramite' => $this->tramite_documento,
            'idusuario' => Auth::id(),
            'recomendacion' => $this->recomendaciones,
            'iddocumento_referencia' => $this->iddocument,
            'folio' => $this->folio,
            'est_firma' => 1, // 👈 IMPORTANTE: Al responder, generas un doc propio
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

            // Insertar nuevo movimiento
            DB::connection('mysql_documentario')->insert("
                INSERT INTO movimiento(
                    iddocumentos, 
                    iddependencias_emior, 
                    iddependencias_receptor, 
                    fecha_de_envio, 
                    fecha_de_recepcion,
                    idestado
                ) VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $ultimoIdDocumento,
                $this->dependencia,
                $this->oficina_destino,
                now(),
                null,
                1
            ]);

             $cont_estados = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                                        FROM estado
                                        LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                                        AND movimiento.iddependencias_receptor = ?
                                        WHERE estado.idestado IN (1,2,3)
                                        GROUP BY estado.idestado;', [$this->oficina_destino]);

            DB::commit();
            event(new DocumentoRecibido($this->oficina_destino, $cont_estados));
            session()->flash('success', "Documento agregado exitosamente con número de expediente $nuevoExpediente");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', "Error al insertar datos: " . $e->getMessage());
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