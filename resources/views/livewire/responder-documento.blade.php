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
                    <!-- COLUMNA IZQUIERDA: Clasificación -->
                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded shadow-sm h-100">
                            <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">
                                <i class="fas fa-info-circle me-1"></i> Clasificación del Documento
                            </h6>
                            
                            <!-- TIPO DE DOCUMENTO -->
<div class="mb-3" x-data="{ open: false, search: '' }">
    <label class="form-label fw-semibold">Tipo de documento</label>
    
    <!-- Input que muestra la selección y abre el dropdown -->
    <div class="position-relative">
        <input 
            type="text" 
            class="form-control border-primary-subtle" 
            :placeholder="search || '{{ $idTipoDocumento ? collect($tiposDocumento)->firstWhere("idtipo_documento", $idTipoDocumento)->nombre_documento : "Seleccione o busque el tipo..." }}'"
            @click="open = !open"
            @click.outside="open = false"
            x-model="search"
            @input="open = true"
            readonly
            style="cursor: pointer;"
        >
        <i class="fas fa-chevron-down position-absolute" style="right: 12px; top: 12px; pointer-events: none;"></i>
        
        <!-- Dropdown con búsqueda -->
        <div x-show="open" 
             x-transition
             class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" 
             style="z-index: 1000; max-height: 250px; overflow-y: auto;">
            
            <div class="p-2 border-bottom">
                <input 
                    type="text" 
                    class="form-control form-control-sm" 
                    placeholder="Buscar..." 
                    x-model="search"
                    @click.stop
                >
            </div>
            
            <div class="p-1">
                @foreach ($tiposDocumento as $tipo)
                    <div 
                        x-show="search === '' || '{{ $tipo->nombre_documento }}'.toLowerCase().includes(search.toLowerCase())"
                        @click="$wire.set('idTipoDocumento', {{ $tipo->idtipo_documento }}); open = false; search = ''"
                        class="p-2 hover-bg-light cursor-pointer"
                        style="cursor: pointer;"
                        :class="{ 'bg-primary text-white': {{ $tipo->idtipo_documento }} == $wire.idTipoDocumento }"
                    >
                        {{ $tipo->nombre_documento }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @error('idTipoDocumento') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Justo debajo del select de "Tipo de documento" -->

@if ($idTipoDocumento && $correlativoPreview)
    @php
        // Obtenemos el nombre del tipo de documento seleccionado para mostrarlo
        $nombreTipoDoc = collect($tiposDocumento)->firstWhere('idtipo_documento', $idTipoDocumento)->nombre_documento ?? 'Documento';
    @endphp
    
    <div class="alert alert-primary d-flex align-items-center border-0 shadow-sm mb-3" role="alert" style="background-color: #e7f1ff !important;">
        <i class="fas fa-eye text-primary me-3 fa-lg"></i>
        <div>
            <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Vista previa del correlativo
            </small>
            <span class="fw-bold text-dark">Este documento se generará como: </span>
            <span class="badge bg-primary fs-6 ms-1 px-3 py-2">
                {{ strtoupper($nombreTipoDoc) }} N° {{ $correlativoPreview }}
            </span>
        </div>
    </div>
@endif


                            <div class="mb-3">
                                <label class="form-label fw-semibold">Número de Folios <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-copy text-muted"></i></span>
                                    <input type="number" wire:model="folio" class="form-control" placeholder="Ej: 5">
                                </div>
                                @error('folio') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA: Destino y Movimiento -->
                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded shadow-sm h-100">
                            <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">
                                <i class="fas fa-paper-plane me-1"></i> Destino y Movimiento
                            </h6>

                            <!-- Selector de Tipo de Destinatario -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Enviar a:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="dependencia" id="tipoDependencia" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="tipoDependencia">
                                        <i class="fas fa-building"></i> Dependencia
                                    </label>

                                    <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="docente" id="tipoDocente" autocomplete="off">
                                    <label class="btn btn-outline-success" for="tipoDocente">
                                        <i class="fas fa-chalkboard-teacher"></i> Docentes
                                    </label>

                                    <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="egresado" id="tipoEgresado" autocomplete="off">
                                    <label class="btn btn-outline-info" for="tipoEgresado">
                                        <i class="fas fa-user-graduate"></i> Egresados
                                    </label>
                                </div>
                            </div>

                            <!-- A: Select de Dependencias -->
                            @if($tipo_destinatario === 'dependencia')
                                <!-- OFICINA DE DESTINO -->
<div class="mb-3" x-data="{ open: false, search: '' }">
    <label class="form-label fw-semibold">Oficina de Destino</label>
    
    <div class="position-relative">
        <input 
            type="text" 
            class="form-control border-primary-subtle" 
            :placeholder="search || '{{ $oficina_destino ? collect($dependencias)->firstWhere("iddependencias", $oficina_destino)->nombre_dependencia : "Seleccione o busque la oficina..." }}'"
            @click="open = !open"
            @click.outside="open = false"
            x-model="search"
            @input="open = true"
            readonly
            style="cursor: pointer;"
        >
        <i class="fas fa-chevron-down position-absolute" style="right: 12px; top: 12px; pointer-events: none;"></i>
        
        <div x-show="open" 
             x-transition
             class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" 
             style="z-index: 1000; max-height: 250px; overflow-y: auto;">
            
            <div class="p-2 border-bottom">
                <input 
                    type="text" 
                    class="form-control form-control-sm" 
                    placeholder="Buscar..." 
                    x-model="search"
                    @click.stop
                >
            </div>
            
            <div class="p-1">
                @foreach ($dependencias as $oficina)
                    <div 
                        x-show="search === '' || '{{ $oficina->nombre_dependencia }}'.toLowerCase().includes(search.toLowerCase())"
                        @click="$wire.set('oficina_destino', {{ $oficina->iddependencias }}); open = false; search = ''"
                        class="p-2 hover-bg-light cursor-pointer"
                        style="cursor: pointer;"
                        :class="{ 'bg-primary text-white': {{ $oficina->iddependencias }} == $wire.oficina_destino }"
                    >
                        {{ $oficina->nombre_dependencia }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @error('oficina_destino') <small class="text-danger">{{ $message }}</small> @enderror
</div>
                            @endif

                            <!-- B: Buscador de Docentes -->
                            @if($tipo_destinatario === 'docente')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Buscar Docentes</label>
                                    <input type="text" wire:model.live.debounce.300ms="busquedaDocente" class="form-control" placeholder="Escriba el nombre del docente...">
                                    
                                    @if(count($resultadosDocentes) > 0)
                                        <div class="list-group mt-2" style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%;">
                                            @foreach($resultadosDocentes as $docente)
                                                <button type="button" wire:click="agregarDocente({{ $docente->id }})" class="list-group-item list-group-item-action">
                                                    {{ $docente->nombre }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(count($docentesSeleccionadosInfo) > 0)
                                        <div class="mt-4">
                                            <label class="form-label fw-semibold">Docentes Seleccionados ({{ count($docentes_seleccionados) }})</label>
                                            <div class="border rounded p-2 bg-light">
                                                @foreach($docentesSeleccionadosInfo as $id => $nombre)
                                                    <span class="badge bg-success me-1 mb-1 p-2">
                                                        {{ $nombre }}
                                                        <button type="button" wire:click="removerDocente({{ $id }})" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;"></button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @error('docentes_seleccionados') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            @endif

                            <!-- C: Buscador de Egresados -->
                            @if($tipo_destinatario === 'egresado')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Buscar Egresados</label>
                                    <input type="text" wire:model.live.debounce.300ms="busquedaEgresado" class="form-control" placeholder="Escriba el nombre o DNI del egresado...">
                                    
                                    @if(count($resultadosEgresados) > 0)
                                        <div class="list-group mt-2" style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%;">
                                            @foreach($resultadosEgresados as $egresado)
                                                <button type="button" wire:click="agregarEgresado({{ $egresado->id }})" class="list-group-item list-group-item-action">
                                                    {{ $egresado->nombre }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(count($egresadosSeleccionadosInfo) > 0)
                                        <div class="mt-4">
                                            <label class="form-label fw-semibold">Egresados Seleccionados ({{ count($egresados_seleccionados) }})</label>
                                            <div class="border rounded p-2 bg-light">
                                                @foreach($egresadosSeleccionadosInfo as $id => $nombre)
                                                    <span class="badge bg-info text-dark me-1 mb-1 p-2">
                                                        {{ $nombre }}
                                                        <button type="button" wire:click="removerEgresado({{ $id }})" class="btn-close ms-2" style="font-size: 0.6rem;"></button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @error('egresados_seleccionados') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            @endif

                            <!-- Referencia -->
                            <div class="form-check mb-3 mt-3">
                                <input type="checkbox" wire:model.live="agregarReferencia" class="form-check-input" id="agregarReferencia">
                                <label class="form-check-label" for="agregarReferencia">Agregar referencia al documento original</label>
                            </div>

                            @if ($agregarReferencia && $referenciaTexto)
                                <div class="alert alert-secondary py-2">
                                    <small><i class="fas fa-link me-1"></i> {{ $referenciaTexto }}</small>
                                </div>
                            @endif

                            <!-- Acción a realizar (Solo para dependencia ID 2) -->
                           
                                
                        
                        </div>
                    </div>

                    <!-- FILA INFERIOR: Detalles del Contenido -->
<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-edit me-2"></i> Detalles del Contenido
            </h6>
        </div>
        <div class="card-body p-4">
            <!-- Fila 1: Asunto y Acción a realizar -->
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-dark">
                        <i class="fas fa-heading text-primary me-1"></i> Asunto <span class="text-danger">*</span>
                    </label>
                    <input type="text" wire:model="asunto" class="form-control form-control-lg" placeholder="Resumen breve del documento...">
                    @error('asunto') <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-success">
                        <i class="fas fa-tasks text-success me-1"></i> Acción a realizar
                    </label>
                    <select wire:model="tramite_documento" class="form-select form-select-lg border-success">
                        <option value="">Seleccione...</option>
                        @foreach ($detalledocumento as $detalle)
                            <option value="{{ $detalle->iddetalle_tramite }}">{{ $detalle->nombre_detalle_tramite }}</option>
                        @endforeach
                    </select>
                    @error('tramite_documento') <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Fila 2: Recomendaciones -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark">
                    <i class="fas fa-comment-dots text-info me-1"></i> Recomendaciones / Observaciones
                </label>
                <textarea wire:model="recomendaciones" class="form-control" rows="4" placeholder="Instrucciones adicionales, notas importantes, etc..."></textarea>
                @error('recomendaciones') <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
            </div>

            <!-- Separador y Botón de Word -->
            <hr class="my-4">
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios
                </div>
                
                <button type="button" wire:click="generarWord" wire:loading.attr="disabled" class="btn btn-primary btn-lg shadow-sm">
                    <span wire:loading wire:target="generarWord" class="spinner-border spinner-border-sm me-2"></span>
                    <i class="fas fa-file-word me-2"></i> Generar Borrador Word
                </button>
            </div>
        </div>
    </div>
</div>
                    <!-- FILA INFERIOR: Modo de Entrega y Archivos -->
                    <div class="col-12">
                        <div class="p-3 border-start border-4 border-primary bg-white rounded shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fas fa-truck me-1 text-muted"></i> Modo de Entrega</label>
                                    <select wire:model.live="entregaOpciones" class="form-select bg-light">
                                        <option value="">Seleccione opción...</option>
                                        <option value="1">📦 Solo Físico</option>
                                        <option value="2">💻 Solo Virtual</option>
                                        <option value="3">🔄 Mixto (Físico y Virtual)</option>
                                    </select>
                                    @error('entregaOpciones') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6 mt-3 mt-md-0">
                                    @if ($entregaOpciones == 2 || $entregaOpciones == 3)
                                        <label class="form-label fw-semibold"><i class="fas fa-cloud-upload-alt me-1 text-muted"></i> Adjuntar PDF</label>
                                        <input type="file" wire:model="archivo_virtual" class="form-control shadow-sm" accept=".pdf">
                                        <div wire:loading wire:target="archivo_virtual" class="text-primary mt-1">
                                            <small><i class="fas fa-spinner fa-spin"></i> Subiendo...</small>
                                        </div>
                                        @error('archivo_virtual') <small class="text-danger">{{ $message }}</small> @enderror

                                        @if ($archivo_virtual)
                                            <div class="mt-2 small text-success">
                                                <i class="fas fa-check-circle"></i> Listo: {{ $archivo_virtual->getClientOriginalName() }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($archivo_virtual)
                        <div class="col-12">
                            <div class="card border-primary bg-light shadow-sm">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Documento cargado correctamente</h6>
                                            <small class="text-muted">{{ $archivo_virtual->getClientOriginalName() }}</small>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="window.open('{{ $this->getCustomPreviewUrl() }}', '_blank')" 
                                            class="btn btn-primary btn-sm px-3 shadow-sm">
                                        <i class="fas fa-eye me-1"></i> Visualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Botones de Acción Final -->
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                    <a href="{{ route('documentario.mesapar.bandeja') }}" class="btn btn-outline-secondary px-4">
    <i class="fas fa-arrow-left me-1"></i> Cancelar y Volver a la Bandeja
</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm me-1"></span>
                        <i class="fas fa-save me-2"></i> Registrar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('livewire:initialized', () => {
        const initTomSelect = () => {
            // Inicializar Tipo de Documento (siempre existe)
            const elTipoDoc = document.getElementById('selectTipoDocumento');
            if (elTipoDoc && !elTipoDoc.tomselect) {
                new TomSelect(elTipoDoc, {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: 'Escriba para buscar...',
                    highlight: true,
                    diacritics: true
                });
            }

            // Inicializar Oficina de Destino (SOLO si existe en el DOM)
            const elOficina = document.getElementById('selectOficinaDestino');
            if (elOficina && !elOficina.tomselect) {
                new TomSelect(elOficina, {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: 'Escriba para buscar la dependencia...',
                    highlight: true,
                    diacritics: true
                });
            }
        };

        // Ejecutar al cargar
        setTimeout(initTomSelect, 100);

        // Volver a inicializar si Livewire actualiza el componente
        Livewire.hook('commit', ({ component, succeed }) => {
            succeed(() => {
                setTimeout(initTomSelect, 100);
            });
        });
    });
</script>