<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center py-3">
            <i class="fas fa-file-signature fa-lg me-2"></i>
            <h5 class="mb-0 fw-bold">Responder Documento de Gestión</h5>
        </div>

        <div class="card-body bg-light-50">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form wire:submit.prevent="submit" enctype="multipart/form-data">
                <input type="hidden" wire:model="iddocument">
                <input type="hidden" wire:model="dependencia">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded shadow-sm h-100">
                            <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">
                                <i class="fas fa-info-circle me-1"></i> Clasificación del Documento
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de documento</label>
                                <select wire:model.live="idTipoDocumento" class="form-select border-primary-subtle">
                                    <option value="">Seleccione el tipo...</option>
                                    @foreach ($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->idtipo_documento }}">{{ $tipo->nombre_documento }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idTipoDocumento')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (!is_null($correlativoPreview) && $idTipoDocumento)
                                <div class="alert alert-warning py-2 border-0 shadow-sm">
                                    <small class="fw-bold">
                                        <i class="fas fa-hashtag me-1"></i> Próximo número:
                                        <span class="badge bg-dark">{{ $correlativoPreview }}</span>
                                    </small>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Número de Folios <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i
                                            class="fas fa-copy text-muted"></i></span>
                                    <input type="number" wire:model="folio" class="form-control" placeholder="Ej: 5">
                                </div>
                                @error('folio')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded shadow-sm h-100">
                            <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">
                                <i class="fas fa-paper-plane me-1"></i> Destino y Movimiento
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Oficina de Destino</label>
                                <select wire:model="oficina_destino" class="form-select border-primary-subtle">
                                    <option value="">Seleccione la oficina...</option>
                                    @foreach ($dependencias as $oficinas)
                                        <option value="{{ $oficinas->iddependencias }}">
                                            {{ $oficinas->nombre_dependencia }}</option>
                                    @endforeach
                                </select>
                                @error('oficina_destino')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" wire:model.live="agregarReferencia" class="form-check-input"
                                    id="agregarReferencia">
                                <label class="form-check-label" for="agregarReferencia">Agregar referencia</label>
                            </div>

                            @if ($agregarReferencia && $referenciaTexto)
                                <div class="alert alert-secondary">
                                    Referencia: {{ $referenciaTexto }}
                                </div>
                            @endif

                            @if ($id_depen == 2)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-success">Acción a realizar</label>
                                    <select wire:model="tramite_documento" class="form-select border-success">
                                        <option value="">Seleccione la acción...</option>
                                        @foreach ($detalledocumento as $detalle)
                                            <option value="{{ $detalle->iddetalle_tramite }}">
                                                {{ $detalle->nombre_detalle_tramite }}</option>
                                        @endforeach
                                    </select>
                                    @error('tramite_documento')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-white border rounded shadow-sm">
                            <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">
                                <i class="fas fa-edit me-1"></i> Detalles del Contenido
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
                                <input type="text" wire:model="asunto" class="form-control"
                                    placeholder="Resumen breve del documento...">
                                @error('asunto')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Recomendaciones / Observaciones</label>
                                <textarea wire:model="recomendaciones" class="form-control" rows="3" placeholder="Instrucciones adicionales..."></textarea>
                                @error('recomendaciones')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="button" wire:click="generarWord" class="btn btn-secondary">
                                <i class="fas fa-file-word"></i> Generar Word
                            </button>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 border-start border-4 border-primary bg-white rounded shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fas fa-truck me-1 text-muted"></i>
                                        Modo de Entrega</label>
                                    <select wire:model.live="entregaOpciones" class="form-select bg-light">
                                        <option value="">Seleccione opción...</option>
                                        <option value="1">📦 Solo Físico</option>
                                        <option value="2">💻 Solo Virtual</option>
                                        <option value="3">🔄 Mixto (Físico y Virtual)</option>
                                    </select>
                                    @error('entregaOpciones')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 mt-md-0">
                                    @if ($entregaOpciones == 2 || $entregaOpciones == 3)
                                        <label class="form-label fw-semibold"><i
                                                class="fas fa-cloud-upload-alt me-1 text-muted"></i> Adjuntar
                                            PDF</label>
                                        <input type="file" wire:model="archivo_virtual"
                                            class="form-control shadow-sm">
                                        <div wire:loading wire:target="archivo_virtual" class="text-primary mt-1">
                                            <small><i class="fas fa-spinner fa-spin"></i> Subiendo...</small>
                                        </div>
                                        @error('archivo_virtual')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                        @if ($archivo_virtual)
                                            <div class="mt-2 small text-success">
                                                <i class="fas fa-file-pdf"></i> Listo:
                                                {{ $archivo_virtual->getClientOriginalName() }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($archivo_virtual)
                        <div class="card border-primary bg-light mt-3 shadow-sm">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Documento cargado correctamente</h6>
                                        <small
                                            class="text-muted">{{ $archivo_virtual->getClientOriginalName() }}</small>
                                    </div>
                                </div>

                                <button type="button"
                                    onclick="window.open('{{ $this->getCustomPreviewUrl() }}', '_blank')"
                                    class="btn btn-primary btn-sm px-3 shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Visualizar en pestaña nueva
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary px-4">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow" wire:loading.attr="disabled">
                        <i class="fas fa-save me-2"></i> Registrar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPdf(url) {
            const win = window.open();
            win.document.write(`
            <html>
                <body style="margin:0;">
                    <embed width="100%" height="100%" src="${url}" type="application/pdf">
                </body>
            </html>
        `);
        }
    </script>
</div>
