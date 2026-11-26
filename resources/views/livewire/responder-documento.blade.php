<div class="container py-4">
    <!-- Encabezado con contexto -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1"><i class="fas fa-reply me-2"></i>Responder Documento de Gestión</h4>
            <p class="text-muted mb-0">Complete los siguientes pasos para generar y enviar la respuesta.</p>
        </div>
        <a href="{{ route('documentario.mesapar.bandeja') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a la Bandeja
        </a>
    </div>

    <!-- Mensajes de Feedback -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit.prevent="submit" enctype="multipart/form-data">
        <input type="hidden" wire:model="iddocument">
        <input type="hidden" wire:model="dependencia">

        <!-- PASO 1: DESTINATARIO (Lo más importante, va primero y destacado) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-paper-plane me-2"></i> Paso 1: ¿A quién va dirigido?</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="dependencia" id="tipoDependencia" autocomplete="off">
                            <label class="btn btn-outline-primary d-flex align-items-center justify-content-center py-3" for="tipoDependencia">
                                <i class="fas fa-building fa-lg me-2"></i> <span>Dependencia / Oficina</span>
                            </label>

                            <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="docente" id="tipoDocente" autocomplete="off">
                            <label class="btn btn-outline-success d-flex align-items-center justify-content-center py-3" for="tipoDocente">
                                <i class="fas fa-chalkboard-teacher fa-lg me-2"></i> <span>Docentes</span>
                            </label>

                            <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="egresado" id="tipoEgresado" autocomplete="off">
                            <label class="btn btn-outline-info d-flex align-items-center justify-content-center py-3" for="tipoEgresado">
                                <i class="fas fa-user-graduate fa-lg me-2"></i> <span>Egresados</span>
                            </label>

                            @if($id_depen == 7)
                                <input type="radio" class="btn-check" wire:model.live="tipo_destinatario" value="externo" id="tipoExterno" autocomplete="off">
                                <label class="btn btn-outline-warning text-dark d-flex align-items-center justify-content-center py-3" for="tipoExterno">
                                    <i class="fas fa-globe fa-lg me-2"></i> <span>Entidad Externa</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- Campos dinámicos según selección -->
                    <div class="col-12 mt-3">
                        @if($tipo_destinatario === 'dependencia')
                            <div x-data="{ open: false, search: '' }">
                                <label class="form-label fw-semibold">Seleccione la Oficina de Destino</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-lg border-primary-subtle" 
                                        :placeholder="search || '{{ $oficina_destino ? collect($dependencias)->firstWhere("iddependencias", $oficina_destino)->nombre_dependencia : "Escriba para buscar la oficina..." }}'"
                                        @click="open = !open" @click.outside="open = false" x-model="search" @input="open = true" readonly style="cursor: pointer;">
                                    <i class="fas fa-search position-absolute" style="right: 15px; top: 18px; color: #adb5bd;"></i>
                                    
                                    <div x-show="open" x-transition class="position-absolute w-100 bg-white border rounded shadow mt-1" style="z-index: 1000; max-height: 250px; overflow-y: auto;">
                                        <div class="p-2 border-bottom bg-light sticky-top">
                                            <input type="text" class="form-control form-control-sm" placeholder="Filtrar..." x-model="search" @click.stop>
                                        </div>
                                        @foreach ($dependencias as $oficina)
                                            <div x-show="search === '' || '{{ $oficina->nombre_dependencia }}'.toLowerCase().includes(search.toLowerCase())"
                                                @click="$wire.set('oficina_destino', {{ $oficina->iddependencias }}); open = false; search = ''"
                                                class="p-3 hover-bg-light cursor-pointer border-bottom" style="cursor: pointer;"
                                                :class="{ 'bg-primary text-white': {{ $oficina->iddependencias }} == $wire.oficina_destino }">
                                                {{ $oficina->nombre_dependencia }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('oficina_destino') <small class="text-danger mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                            </div>
                        @endif

                        @if($tipo_destinatario === 'docente')
                            <label class="form-label fw-semibold">Buscar y seleccionar Docentes</label>
                            <input type="text" wire:model.live.debounce.300ms="busquedaDocente" class="form-control form-control-lg" placeholder="Escriba el nombre o DNI del docente...">
                            @if(count($resultadosDocentes) > 0)
                                <div class="list-group mt-2 shadow-sm" style="max-height: 200px; overflow-y: auto; z-index: 1000;">
                                    @foreach($resultadosDocentes as $docente)
                                        <button type="button" wire:click="agregarDocente({{ $docente->id }})" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user me-2 text-muted"></i> {{ $docente->nombre }}</span>
                                            <i class="fas fa-plus-circle text-primary"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @if(count($docentesSeleccionadosInfo) > 0)
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <small class="text-muted fw-bold d-block mb-2">Docentes seleccionados:</small>
                                    @foreach($docentesSeleccionadosInfo as $id => $nombre)
                                        <span class="badge bg-success me-1 mb-1 p-2 fw-normal">
                                            {{ $nombre }}
                                            <button type="button" wire:click="removerDocente({{ $id }})" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;"></button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @error('docentes_seleccionados') <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        @endif

                        @if($tipo_destinatario === 'egresado')
                            <label class="form-label fw-semibold">Buscar y seleccionar Egresados</label>
                            <input type="text" wire:model.live.debounce.300ms="busquedaEgresado" class="form-control form-control-lg" placeholder="Escriba el nombre o DNI del egresado...">
                            @if(count($resultadosEgresados) > 0)
                                <div class="list-group mt-2 shadow-sm" style="max-height: 200px; overflow-y: auto; z-index: 1000;">
                                    @foreach($resultadosEgresados as $egresado)
                                        <button type="button" wire:click="agregarEgresado({{ $egresado->id }})" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-graduate me-2 text-muted"></i> {{ $egresado->nombre }}</span>
                                            <i class="fas fa-plus-circle text-info"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @if(count($egresadosSeleccionadosInfo) > 0)
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <small class="text-muted fw-bold d-block mb-2">Egresados seleccionados:</small>
                                    @foreach($egresadosSeleccionadosInfo as $id => $nombre)
                                        <span class="badge bg-info text-dark me-1 mb-1 p-2 fw-normal">
                                            {{ $nombre }}
                                            <button type="button" wire:click="removerEgresado({{ $id }})" class="btn-close ms-2" style="font-size: 0.6rem;"></button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @error('egresados_seleccionados') <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        @endif

                        {{-- SECCIÓN ENTIDAD EXTERNA (Destacada y clara) --}}
                        @if($tipo_destinatario === 'externo')
                            <div class="p-4 bg-light border border-primary rounded-3 mt-3">
                                <h6 class="text-primary fw-bold mb-3"><i class="fas fa-globe-americas me-2"></i>Datos de la Entidad Externa</h6>
                                @if(empty($entidad_externa_correo))
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                        <div><strong>¡Atención!</strong> Este documento no tiene un correo electrónico registrado. No se puede realizar el envío. Solicite a Mesa de Partes que actualice el registro.</div>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Nombre de la Entidad</label>
                                            <input type="text" wire:model="entidad_externa_nombre" class="form-control bg-white fw-semibold">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Correo Electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="fas fa-envelope text-primary"></i></span>
                                                <input type="email" wire:model="entidad_externa_correo" class="form-control bg-white fw-semibold" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Teléfono / Celular</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="fas fa-phone text-primary"></i></span>
                                                <input type="text" wire:model="entidad_externa_celular" class="form-control bg-white fw-semibold" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-primary mt-3 mb-0 d-flex align-items-start">
                                        <i class="fas fa-info-circle me-2 mt-1 fa-lg"></i>
                                        <div class="small">
                                            <strong>Flujo de envío:</strong> Al registrar, el sistema guardará el documento, descargará el PDF automáticamente y abrirá su correo con los datos listos. Solo deberá arrastrar el archivo y enviar.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- PASO 2: CLASIFICACIÓN (Columna Izquierda) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-tags me-2"></i> Paso 2: Clasificación</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4" x-data="{ open: false, search: '' }">
                            <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" class="form-control" 
                                    :placeholder="search || '{{ $idTipoDocumento ? collect($tiposDocumento)->firstWhere("idtipo_documento", $idTipoDocumento)->nombre_documento : "Buscar tipo..." }}'"
                                    @click="open = !open" @click.outside="open = false" x-model="search" @input="open = true" readonly style="cursor: pointer;">
                                <i class="fas fa-chevron-down position-absolute" style="right: 12px; top: 12px; color: #adb5bd;"></i>
                                <div x-show="open" x-transition class="position-absolute w-100 bg-white border rounded shadow mt-1" style="z-index: 1000; max-height: 250px; overflow-y: auto;">
                                    <div class="p-2 border-bottom bg-light"><input type="text" class="form-control form-control-sm" placeholder="Filtrar..." x-model="search" @click.stop></div>
                                    @foreach ($tiposDocumento as $tipo)
                                        <div x-show="search === '' || '{{ $tipo->nombre_documento }}'.toLowerCase().includes(search.toLowerCase())"
                                            @click="$wire.set('idTipoDocumento', {{ $tipo->idtipo_documento }}); open = false; search = ''"
                                            class="p-2 hover-bg-light" style="cursor: pointer;" :class="{ 'bg-primary text-white': {{ $tipo->idtipo_documento }} == $wire.idTipoDocumento }">
                                            {{ $tipo->nombre_documento }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('idTipoDocumento') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if ($idTipoDocumento && $correlativoPreview)
                            @php $nombreTipoDoc = collect($tiposDocumento)->firstWhere('idtipo_documento', $idTipoDocumento)->nombre_documento ?? 'Documento'; @endphp
                            <div class="alert alert-primary d-flex align-items-center border-0 mb-4" style="background-color: #e7f1ff !important;">
                                <i class="fas fa-eye text-primary me-3 fa-lg"></i>
                                <div>
                                    <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.65rem;">Vista previa</small>
                                    <span class="fw-bold">Se generará como: </span>
                                    <span class="badge bg-primary fs-6 ms-1 px-2 py-2">{{ strtoupper($nombreTipoDoc) }} N° {{ $correlativoPreview }}</span>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label fw-semibold">Número de Folios <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-copy text-muted"></i></span>
                                <input type="number" wire:model="folio" class="form-control" placeholder="Ej: 5">
                            </div>
                            @error('folio') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 3: CONTENIDO (Columna Derecha, más ancha) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-edit me-2"></i> Paso 3: Contenido de la Respuesta</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
                                <input type="text" wire:model="asunto" class="form-control form-control-lg" placeholder="Resumen claro y conciso del documento...">
                                @error('asunto') <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Acción a realizar</label>
                                <select wire:model="tramite_documento" class="form-select form-select-lg">
                                    <option value="">Seleccione...</option>
                                    @foreach ($detalledocumento as $detalle)
                                        <option value="{{ $detalle->iddetalle_tramite }}">{{ $detalle->nombre_detalle_tramite }}</option>
                                    @endforeach
                                </select>
                                @error('tramite_documento') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recomendaciones / Observaciones <span class="text-danger">*</span></label>
                            <textarea wire:model="recomendaciones" class="form-control" rows="5" placeholder="Detalle las instrucciones, notas o decisiones importantes..."></textarea>
                            @error('recomendaciones') <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" wire:model.live="agregarReferencia" class="form-check-input" id="agregarReferencia">
                            <label class="form-check-label" for="agregarReferencia">Agregar referencia al documento original</label>
                        </div>
                        @if ($agregarReferencia && $referenciaTexto)
                            <div class="alert alert-secondary py-2 d-flex align-items-center">
                                <i class="fas fa-link me-2 text-muted"></i> <small>{{ $referenciaTexto }}</small>
                            </div>
                        @endif

                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <button type="button" wire:click="generarWord" wire:loading.attr="disabled" class="btn btn-outline-primary">
                                <span wire:loading wire:target="generarWord" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="fas fa-file-word me-2"></i> Generar Borrador Word
                            </button>
                        </div
                    </div>
                </div>
            </div>
        </div>

        <!-- PASO 4: ARCHIVOS Y ENTREGA -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-paperclip me-2"></i> Paso 4: Modo de Entrega y Archivos</h6>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Modo de Entrega <span class="text-danger">*</span></label>
                        <select wire:model.live="entregaOpciones" class="form-select form-select-lg bg-light">
                            <option value="">Seleccione una opción...</option>
                            <option value="1">📦 Solo Físico</option>
                            <option value="2">💻 Solo Virtual (PDF)</option>
                            <option value="3">🔄 Mixto (Físico y Virtual)</option>
                        </select>
                        @error('entregaOpciones') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-7">
                        @if ($entregaOpciones == 2 || $entregaOpciones == 3)
                            <label class="form-label fw-semibold">Adjuntar Documento PDF <span class="text-danger">*</span></label>
                            <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="border-style: dashed !important; transition: all 0.3s;">
                                <input type="file" wire:model="archivo_virtual" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept=".pdf">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                <p class="mb-1 fw-semibold">Haga clic o arrastre el archivo PDF aquí</p>
                                <small class="text-muted">Tamaño máximo: 2MB</small>
                                
                                <div wire:loading wire:target="archivo_virtual" class="text-primary mt-2">
                                    <i class="fas fa-spinner fa-spin"></i> Procesando archivo...
                                </div>
                                @error('archivo_virtual') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror

                                @if ($archivo_virtual)
                                    <div class="mt-3 p-2 bg-success bg-opacity-10 text-success rounded border border-success">
                                        <i class="fas fa-check-circle me-2"></i> <strong>{{ $archivo_virtual->getClientOriginalName() }}</strong>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="h-100 d-flex align-items-center justify-content-center p-4 bg-light rounded-3 text-muted">
                                <div class="text-center">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p class="mb-0">Seleccione "Solo Virtual" o "Mixto" para habilitar la carga de archivos.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($archivo_virtual)
                    <div class="mt-4 p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf fa-3x text-danger me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Vista Previa del Documento</h6>
                                <small class="text-muted">{{ $archivo_virtual->getClientOriginalName() }}</small>
                            </div>
                        </div>
                        <button type="button" onclick="window.open('{{ $this->getCustomPreviewUrl() }}', '_blank')" class="btn btn-primary shadow-sm">
                            <i class="fas fa-eye me-1"></i> Visualizar PDF
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN FINAL (Sticky o muy visibles) -->
        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top bg-white p-3 rounded shadow-sm position-sticky bottom-0">
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i> Los campos con <span class="text-danger">*</span> son obligatorios.
            </div>
            <div>
                <a href="{{ route('documentario.mesapar.bandeja') }}" class="btn btn-light border me-2 px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm me-2"></span>
                    <i class="fas fa-save me-2"></i> Registrar y Continuar
                </button>
            </div.>
        </div>
    </form>
</div>

{{-- Scripts (Se mantienen intactos) --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        const initTomSelect = () => {
            const elTipoDoc = document.getElementById('selectTipoDocumento');
            if (elTipoDoc && !elTipoDoc.tomselect) {
                new TomSelect(elTipoDoc, { create: false, sortField: { field: "text", direction: "asc" }, placeholder: 'Escriba para buscar...', highlight: true, diacritics: true });
            }
            const elOficina = document.getElementById('selectOficinaDestino');
            if (elOficina && !elOficina.tomselect) {
                new TomSelect(elOficina, { create: false, sortField: { field: "text", direction: "asc" }, placeholder: 'Escriba para buscar...', highlight: true, diacritics: true });
            }
        };
        setTimeout(initTomSelect, 100);
        Livewire.hook('commit', ({ component, succeed }) => { succeed(() => { setTimeout(initTomSelect, 100); }); });
    });
</script>

@script
<script>
    $wire.on('mostrarAlertaJefe', (event) => {
        Swal.fire({
            icon: 'warning', title: '⚠️ Envío Bloqueado por Seguridad',
            html: `<div class="text-start"><p class="mb-2">${event.mensaje}</p><small class="text-muted"><i class="fas fa-info-circle"></i> El documento NO se ha guardado para evitar inconsistencias.</small></div>`,
            confirmButtonText: 'Entendido, volver a la bandeja', confirmButtonColor: '#0d6efd', allowOutsideClick: false, allowEscapeKey: false
        }).then((result) => { if (result.isConfirmed) { window.location.href = "{{ route('documentario.mesapar.bandeja') }}"; } });
    });
</script>
@endscript