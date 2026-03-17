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
                ->orderBy('idDocumentos', 'desc')
                ->value('numero_de_exp');

            $nuevoExpediente = $ultimoExpediente
                ? str_pad($ultimoExpediente + 1, 3, '0', STR_PAD_LEFT)
                : '001';

            // Insertar nuevo documento
            $ultimoIdDocumento = DB::connection('mysql_documentario')->table('documentos')->insertGetId([
                'numero_de_exp' => $nuevoExpediente,
                'fecha_ingreso' => now(),
                'asunto' => $this->asunto,
                'idtipo_documento' => $this->idTipoDocumento, // 👈 ahora sí se guarda
                'emisor' => $this->dependencia,
                'iddetalle_tramite' => $this->tramite_documento,
                'idusuario' => Auth::id(),
                'recomendacion' => $this->recomendaciones,
                'iddocumento_referencia' => $this->iddocument,
                'folio' => $this->folio,
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
    $templatePath = storage_path('app/templates/responder.docx');
    $template = new TemplateProcessor($templatePath);

    // Reemplazar variables comunes
    $template->setValue('asunto', $this->asunto);
    $template->setValue('folio', $this->folio);
    $template->setValue('dependencia', $this->dependencia);
    $template->setValue('idTipoDocumento', $this->idTipoDocumento);

    // 👇 Solo si el check está marcado
    if ($this->agregarReferencia && $this->referenciaTexto) {
        $template->setValue('referencia', "Referencia:  $this->referenciaTexto");
    } else {
        // Si no está marcado, dejamos vacío
        $template->setValue('referencia', '');
    }

    // Guardar archivo generado
    $fileName = 'respuesta_' . time() . '.docx';
    $outputPath = storage_path("app/public/$fileName");
    $template->saveAs($outputPath);

    return response()->download($outputPath)->deleteFileAfterSend(true);
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