@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @canany(['documentario.mesapar.showEmitido', 'alumno.matricula.index'])
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="{{ $estadoMayor->idestado >= 2 ? 'fas fa-eye' : 'fas fa-edit' }}"></i> <i
                                    class="fas fa-boxes"></i>
                                -
                                {{ $estadoMayor->idestado >= 2 ? 'VER' : 'EDITAR' }} DOCUMENTO
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('documentario.mesapar.index') }}"
                                        style="color: #4a3911; text-decoration: none;" class="mause">inicio</a></li>
                                <li class="breadcrumb-item active">Editar envio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcanany
@stop

@section('content')
    @canany(['documentario.mesapar.showEmitido', 'alumno.matricula.index'])
        <section class="content">
            <div class="container-fluid">
                {{-- ver si las oficinas respondieron --}}
                <div class="card collapsed-card card-{{ $estadoMayor->idestado >= 2 ? 'success' : 'danger' }}">
                    <div class="card-header">
                        <h3 class="card-title">Ver si la(s) dependencia(s) han recepcionado el documento.</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i> {{-- ícono se transforma automáticamente --}}
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <ul class="list-group pb-4">
                            @foreach ($queryRecepDepenFech as $dep)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chevron-right text-info mr-2"></i>
                                        {{ $dep->nombre_dependencia }}</span>
                                    @if ($dep->fecha_de_recepcion)
                                        <span class="badge badge-primary badge-pill">
                                            {{ \Carbon\Carbon::parse($dep->fecha_de_recepcion)->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger badge-pill">
                                            No recepcionado aun
                                        </span>
                                    @endif

                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                gggggggggggggggggggggggggg
                {{-- todo el formulario de editar --}}
                <div class="card card-{{ $estadoMayor->idestado >= 2 ? 'warning' : 'info' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="{{ $estadoMayor->idestado >= 2 ? 'fas fa-eye' : 'fas fa-pencil-alt' }}"></i>
                            {{ $estadoMayor->idestado >= 2 ? 'Ver' : 'Editar' }} documento
                            <span class="">Num. Exp.
                                {{ $queryDoc->iddocumentos }}
                            </span>
                        </h3>
                        <input type="hidden" name="emisor" id="emisor" value="{{ $queryDoc->emisor }}">
                        <input type="hidden" name="num_exR" id="num_exR" value="{{ $queryDoc->idtipo_documento }}">
                    </div>

                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-12">
                                <form
                                    action="{{ route('documentario.mesapar.updateDocuEmi', ['iddocumentos' => $queryDoc->iddocumentos]) }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="est_firma" value="1">
                                        <input type="hidden" id="emisor" name="emisor"
                                            value="{{ session('dependencia_id') }}">
                                        {{-- <input type="hidden" id="id_usuTrabajador" name="id_usuTrabajador"
                                        value="{{ $id_usuTrabajador }}"> --}}

                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="tipo_documento">
                                                            <i class="fas fa-file-contract text-primary"></i> Tipo
                                                            documento: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="tipo_documento"
                                                            class="form-control @error('tipo_documento') is-invalid @enderror"
                                                            name="tipo_documento"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            <option value="0"
                                                                {{ $queryDoc->idtipo_documento == 0 ? 'selected' : '' }}>
                                                                Seleccione
                                                                tipo
                                                                documento ...</option>
                                                            @if ($rol->nombre_dependencia == 'Mesa de Partes' && $queryDoc->est_firma == 0)
                                                                <option value="1"
                                                                    {{ $queryDoc->idtipo_documento == 1 ? 'selected' : '' }}>
                                                                    Fut</option>
                                                            @endif
                                                            <option value="2"
                                                                {{ $queryDoc->idtipo_documento == 2 ? 'selected' : '' }}>
                                                                Oficio
                                                            </option>
                                                            <option value="3"
                                                                {{ $queryDoc->idtipo_documento == 3 ? 'selected' : '' }}>
                                                                Informe
                                                            </option>
                                                            <option value="4"
                                                                {{ $queryDoc->idtipo_documento == 4 ? 'selected' : '' }}>
                                                                Requerimiento
                                                            </option>
                                                            <option value="5"
                                                                {{ $queryDoc->idtipo_documento == 5 ? 'selected' : '' }}>
                                                                Memorándum</option>
                                                            <option value="6"
                                                                {{ $queryDoc->idtipo_documento == 6 ? 'selected' : '' }}>
                                                                Memorándum Multi.</option>
                                                            <option value="7"
                                                                {{ $queryDoc->idtipo_documento == 7 ? 'selected' : '' }}>
                                                                Resoluciones</option>
                                                            <option value="8"
                                                                {{ $queryDoc->idtipo_documento == 8 ? 'selected' : '' }}>
                                                                Otro</option>
                                                        </select>
                                                        @error('tipo_documento')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="folio">
                                                            <i class="fas fa-list-ol text-warning"></i> Folio: <span
                                                                class="text-danger">*</span>
                                                        </label>
                                                        <input class="form-control @error('folio') is-invalid @enderror"
                                                            type="number" id="folio" name="folio"
                                                            onkeydown="return bloqueare(event)"
                                                            value="{{ old('folio', $queryDoc->folio) }}"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                        @error('folio')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-4 shadow"
                                                    style="background: linear-gradient(90deg, #edd0b4, #ffffff); ">
                                                    <div class="form-group">
                                                        <label for="num_expe">
                                                            <i class="fas fa-fingerprint text-secondary"></i> N° exp.
                                                            local:
                                                        </label>
                                                        <input class="form-control bg-light" type="text"
                                                            style="background: linear-gradient(135deg, #eac096, #ebe6df); "
                                                            id="num_expe" name="num_expe"
                                                            value="{{ $queryDoc->numero_de_exp }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- @dump($queryDoc->est_firma) --}}

                                            <div class="row">
                                                @if ($id_depen == 24 && $queryDoc->est_firma == 0)
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-user-friends text-indigo mr-1"></i>
                                                                Tipo de Remitente: <span class="text-danger">*</span></label>
                                                            <select id="tipo_remitente_m" name="tipo_remitente_m"
                                                                class="form-control">
                                                                <option value="natural">Persona Natural</option>
                                                                <option value="juridica">Entidad Externa (Jurídica)
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-8" id="div_persona_m_natural">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-user-circle text-olive mr-1"></i>
                                                                Usuario / Persona Natural: <span
                                                                    class="text-danger">*</span></label>
                                                            <select id="usuario_m" class="form-control select2"
                                                                name="usuario_m" style="width: 100%;"></select>
                                                            <span id="usuario_m_error" class="text-danger small"></span>
                                                        </div>
                                                    </div>

                                                    <div id="div_entidad_m_externa" class="col-sm-12" style="display:none;">
                                                        <div class="card card-outline card-info shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label><i
                                                                                    class="fas fa-building text-info mr-1"></i>
                                                                                Entidad remitente:</label>
                                                                            <select id="entidad_m" name="id_entidad_m_externa"
                                                                                class="form-control select2"></select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label><i
                                                                                    class="fas fa-id-card text-info mr-1"></i>
                                                                                N° Documento Externo:</label>
                                                                            <input type="text"
                                                                                name="numero_documento_externo_m"
                                                                                class="form-control"
                                                                                placeholder="Ej: Oficio N.º 123-2026-MPP">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 text-right">
                                                                        <a href="https://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaWeb.jsp"
                                                                            target="_blank"
                                                                            class="btn btn-link btn-sm text-info">
                                                                            <i class="fas fa-external-link-alt mr-1"></i>
                                                                            Consultar RUC en SUNAT
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row mt-3"
                                                style="background: linear-gradient(90deg, #fbdfc2, #ffffff);">
                                                <div class="col-sm-9" id="container_dependencias">
                                                    <div class="form-group">
                                                        <label for="dependencia_enviar">
                                                            <i class="fas fa-map-marker-alt text-success"></i>
                                                            Dependencia(s) a enviar: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="dependencia_enviar"
                                                            class="form-control select2 shadow @error('dependencia_enviar') is-invalid @enderror"
                                                            name="dependencia_enviar[]" multiple
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            @foreach ($dependencias as $dep)
                                                                <option value="{{ $dep->iddependencias }}"
                                                                    {{ in_array($dep->iddependencias, $dependenciasSelect) ? 'selected' : '' }}>
                                                                    {{ $dep->nombre_dependencia }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('dependencia_enviar')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12" id="container_docentes"
                                                    style="{{ in_array(2, $dependenciasSelect) ? '' : 'display: none;' }}">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-user-graduate text-primary"></i>
                                                            Seleccionar Docente(s)</label>
                                                        <select id="docentes_select" class="form-control select2"
                                                            name="docentes_especificos[]" multiple
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            @foreach ($docentesSeleccionados as $doc)
                                                                <option value="{{ $doc->id }}" selected>
                                                                    {{ $doc->text }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Nota: Al enviar a docentes, no se pueden
                                                            añadir
                                                            otras dependencias.</small>
                                                    </div>
                                                </div>

                                                {{-- <div class="col-sm-3 text-center">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-share-all text-muted"></i> ¿A
                                                            todas?</label>
                                                        <div class="d-flex justify-content-center pt-1">
                                                            <div
                                                                class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="customSwitch3" name="todasDepenSelects">
                                                                <label class="custom-control-label"
                                                                    for="customSwitch3"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-pen-fancy text-primary"></i> Asunto
                                                            <span class="text-danger">*</span></label>
                                                        <textarea id="asunto" class="form-control shadow @error('asunto') is-invalid @enderror" rows="2"
                                                            placeholder="Escriba el asunto del documento..." name="asunto"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>{{ old('asunto', $queryDoc->asunto) }}</textarea>
                                                        @error('asunto')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="para_su">
                                                            <i class="fas fa-directions" style="color: #6f42c1;"></i>
                                                            Para su: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="para_su" class="form-control shadow"
                                                            placeholder="Ingrese el asunto" name="para_su"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            <option value="0">Seleccione ...</option>
                                                            @foreach ($detalle_tramite as $detra)
                                                                <option value="{{ $detra->iddetalle_tramite }}"
                                                                    {{ $queryDoc->iddetalle_tramite == $detra->iddetalle_tramite ? 'selected' : '' }}>
                                                                    {{ $detra->nombre_detalle_tramite }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <span id="para_su_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-comment-dots text-secondary"></i>
                                                            Recomendaciones:</label>
                                                        <textarea id="recomendaciones" class="form-control shadow" rows="2" placeholder="Notas adicionales..."
                                                            name="Recomendaciones" {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>{{ $queryDoc->recomendacion }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row border-top pt-3 mt-2 bg-light rounded p-2 shadow-sm">
                                                <div class="col-sm-6 border-right">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                id="check_fisico" name="con_anexos_fisicos"
                                                                {{ $queryDoc->anexos_fisicos ? 'checked' : '' }}
                                                                {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            <label for="check_fisico" class="custom-control-label">
                                                                <i class="fas fa-archive text-warning"></i> ¿Contiene
                                                                anexos físicos?
                                                            </label>
                                                        </div>
                                                        <input type="text" id="detalle_fisico"
                                                            name="detalle_anexos_fisicos"
                                                            class="form-control mt-2 border-warning"
                                                            placeholder="Ej: 01 CD, Planos..."
                                                            value="{{ $queryDoc->anexos_fisicos }}"
                                                            style="display: {{ $queryDoc->anexos_fisicos ? 'block' : 'none' }};"
                                                            {{ $queryDoc->anexos_fisicos ? 'required' : '' }}
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="archivo_pdf">
                                                            <i class="fas fa-file-pdf text-danger"></i> ver / Reemplazar
                                                            Documento
                                                            (PDF):
                                                        </label>
                                                        @if ($pdfdocumento->isNotEmpty())
                                                            @foreach ($pdfdocumento as $pdf)
                                                                <div class="container">
                                                                    <diw
                                                                        class="row list-group-item d-flex justify-content-between align-items-center">
                                                                        <div class="col-sm-6">
                                                                            <span>
                                                                                <i
                                                                                    class="fas fa-paperclip text-danger mr-2"></i>
                                                                                {{ $pdf->nombre_del_documento }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <a href="{{ asset('documentos/documentos_director_pdf/' . $pdf->nombre_del_documento) }}"
                                                                                target="_blank"
                                                                                class="btn btn-sm btn-outline-primary">
                                                                                <i class="fas fa-eye"></i> Ver PDF
                                                                            </a>

                                                                            @if ($estadoMayor->idestado == 1)
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-warning btn-cambiar-pdf">
                                                                                    <i class="fas fa-sync-alt"></i> Cambiar
                                                                                </button>
                                                                            @endif

                                                                            <button type="button"
                                                                                class="btn btn-sm btn-info btn-ver-cambio"
                                                                                style="display: none;">
                                                                                <i class="fas fa-file-pdf"></i> Ver Nuevo
                                                                            </button>

                                                                            <input type="file"
                                                                                name="reemplazar_pdf[{{ $pdf->iddocumenpdf }}]"
                                                                                class="input-reemplazo-pdf"
                                                                                style="display: none;"
                                                                                accept="application/pdf">
                                                                        </div>
                                                                    </diw>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <span id="archivo_pdf_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                @if ($estadoMayor->idestado == 1)
                                                    <div class="col-sm-12 mt-2">
                                                        <button type="button" onclick="descargarWordBorrador()"
                                                            class="btn btn-outline-info btn-block shadow-sm">
                                                            <i class="fas fa-file-word"></i> GENERAR BORRADOR AUTOMÁTICO
                                                            PARA FIRMA
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        @if ($estadoMayor->idestado >= 2)
                                            <a href="{{ route('documentario.mesapar.index') }}" class="btn btn-danger"><i
                                                    class="fas fa-arrow-alt-circle-left"></i> Volver</a>
                                        @else
                                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                                Actualizar</button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- /.card-body -->
                </div>
            </div><!-- /.container-fluid -->
        </section>
    @endcanany
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .is-invalid+.select2-container .select2-selection {
            border-color: #dc3545 !important;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection__choice {
            background-color: #28a745 !important;
            /* verde Bootstrap */
            color: white !important;
            border: 1px solid #1e7e34 !important;
            white-space: normal !important;
            max-width: 100%;
        }

        /* Cambi color de la "x" */
        .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px;
        }

        /* Cambia el color de la "x" al pasar el mouse */
        .select2-selection__choice__remove:hover {
            color: rgb(246, 102, 102) !important;
        }

        .select2-selection__clear {
            color: rgb(247, 121, 121) !important;
            font-weight: bold;
            padding-left: 5px;
        }

        .select2-selection__clear:hover {
            color: red !important;
            cursor: pointer;
        }
    </style>
    @livewireStyles
@stop

@section('js')
    <script>
        window.dependenciaId = {{ $id_depen }}; // Esto se usa dentro de app.js
    </script>
    {{-- @vite('resources/js/app.js') --}}

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    {{-- <script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script> --}}
    @livewireScripts

    <script>
        // let dependenciaId = {{ $id_depen }};

        // document.addEventListener("DOMContentLoaded", function() {
        //     const audio = new Audio('{{ asset('sound/alert.mp3') }}');
        //     Echo.private('dependencia.' + dependenciaId)
        //         .listen('.noEditarDocumento', (e) => {
        //             audio.play().catch(err => console.log("Audio en espera de interacción ..."));
        //             Swal.fire({
        //                 title: "ALERTA!",
        //                 text: "Este documento ya fue recepcionado, no se puede editar ....",
        //                 icon: "error"
        //             }).then(() => {
        //                 window.location.href = window.location.href;
        //             });
        //         });
        // });

        // document.addEventListener("DOMContentLoaded", function() {
        //     Echo.private('dependencia.' + dependenciaId)
        //         .listen('.DocumentoRecibido', (e) => {
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: "top-end",
        //                 showConfirmButton: false,
        //                 timer: 3000,
        //                 timerProgressBar: true,
        //                 didOpen: (toast) => {
        //                     toast.onmouseenter = Swal.stopTimer;
        //                     toast.onmouseleave = Swal.resumeTimer;
        //                     var audio = new Audio('{{ asset('sound/noti.mp3') }}'); // Ruta sonido
        //                     audio.play();
        //                 },
        //                 didClose: () => {
        //                     $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
        //                         .cont_estados[0]
        //                         .cont_estado);
        //                 }
        //             });

        //             Toast.fire({
        //                 icon: "success",
        //                 title: "Nuevo documento recibido"
        //             });
        //         });
        // });
    </script>

    <script>
        $(document).ready(function() {
            // Escuchar el cambio en el tipo de remitente
            $('#tipo_remitente_m').on('change', function() {
                let valor = $(this).val();

                if (valor === 'natural') {
                    // Mostrar usuario, ocultar entidad
                    $('#div_persona_m_natural').fadeIn();
                    $('#div_entidad_m_externa').hide();

                    // Limpiar los valores del que se oculta para evitar envíos cruzados
                    $('#entidad_m').val(null).trigger('change');
                } else {
                    // Mostrar entidad, ocultar usuario
                    $('#div_entidad_m_externa').fadeIn();
                    $('#div_persona_m_natural').hide();

                    // Limpiar los valores del que se oculta
                    $('#usuario_m').val(null).trigger('change');
                }
            });

            // Resetear al cerrar el modal para que siempre inicie en Persona Natural
            $('#exampleModalCenter_m').on('hidden.bs.modal', function() {
                $('#tipo_remitente_m').val('natural').trigger('change');
            });

            $('.select2').select2({
                language: {
                    removeAllItems: function() {
                        return "Quitar todos los elementos";
                    }
                },
                placeholder: 'Seleccione dependencias',
                allowClear: true,
            });

            $(".mause").hover(
                function() {
                    $(this).css("color", "#ba9643");
                },
                function() {
                    $(this).css("color", "#4a3911");
                }
            );

            $('#usuario_m').select2({
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                placeholder: 'Buscar por DNI O APELLIDOS Y NOMBRES...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route('documentario.buscarUsuario') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(user) {
                                return {
                                    id: user.idusuario,
                                    text: user.text
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#entidad_m').select2({
                placeholder: 'Buscar entidad...',
                ajax: {
                    url: '{{ route('documentario.buscarEntidad') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            setTimeout(function() {
                $('#myalert').fadeOut();
            }, 5000);

            $('#tipo_documento').change(traer_num_expe);

            //////////////////////////////////////////

            $('#check_fisico').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#detalle_fisico').fadeIn().prop('required', true);
                } else {
                    $('#detalle_fisico').fadeOut().prop('required', false).val('');
                }
            });

            $('#dependencia_enviar').select2({
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Cargando...";
                    },
                    removeAllItems: function() {
                        return "Quitar todos los elementos";
                    }
                },
                placeholder: 'Seleccione dependencia(s)',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route('documentario.traerDepen') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        // console.log(data)
                        return {
                            results: data.map(function(depens) {
                                return {
                                    id: depens.iddependencias,
                                    text: depens.nombre_dependencia
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            inicializarBusquedaDocentes();

            let dataInicial = $('#dependencia_enviar').select2('data');
            let esDocenteInicial = dataInicial.some(item => item.text.trim() === 'Docente' || item.id == "2");

            if (esDocenteInicial) {
                ejecutarLogicaDocente($('#dependencia_enviar'));
            }

            // 4. Tu evento change normal
            $('#dependencia_enviar').on('change', function() {
                let data = $(this).select2('data');
                let esDocente = data.some(item => item.text.trim() === 'Docente');

                if (esDocente) {
                    ejecutarLogicaDocente($(this));
                }
            });

            // Encapsulamos la lógica en una función para no repetir código
            function ejecutarLogicaDocente(elemento) {
                let data = elemento.select2('data');
                let objetoDocente = data.find(item => item.text.trim() === 'Docente' || item.id == "2");
                let idDocenteArea = objetoDocente.id;

                if ($('#input_docente_shadow').length === 0) {
                    $('#form_regis_doc').append(
                        `<input type="hidden" id="input_docente_shadow" name="dependencia_enviar[]" value="${idDocenteArea}">`
                    );
                }

                elemento.val([idDocenteArea]).trigger('change.select2');
                elemento.prop('disabled', true);
                $('#container_docentes').fadeIn();

                if (!$('#btn-reset').length) {
                    elemento.closest('.form-group').append(
                        '<button type="button" id="btn-reset" class="btn btn-xs btn-outline-danger mt-1">Cambiar a otra dependencia</button>'
                    );
                }
            }

            // 2. Función para inicializar el segundo Select2 (Docentes)
            function inicializarBusquedaDocentes() {
                $('#docentes_select').select2({
                    placeholder: "Busque y seleccione uno o varios docentes",
                    width: '100%',
                    ajax: {
                        url: '{{ route('documentario.buscarDocentes') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    },
                });
            }

            // Botón para desbloquear y volver a elegir dependencias normales
            $(document).on('click', '#btn-reset', function() {
                $('#dependencia_enviar').prop('disabled', false).val(null).trigger('change');
                $('#container_docentes').hide();
                $('#docentes_select').val(null).trigger('change');
                $('#input_docente_shadow').remove();
                $(this).remove();
            });

            //para el arachivo pdf
            $('.btn-cambiar-pdf').on('click', function() {
                // Busca el input de archivo que está justo al lado (en el mismo div)
                $(this).siblings('.input-reemplazo-pdf').click();
            });

            //////////////////////////////////////////////////////////// mesa de partes natural y juridic
            @if ($id_depen == 24 && isset($queryDoc))

                let idEntidad = "{{ $queryDoc->id_entidad_externa }}";
                let idUsuario = "{{ $queryDoc->idusuario }}";

                // CASO PERSONA JURÍDICA
                if (idEntidad != "") {
                    $('#tipo_remitente_m').val('juridica').trigger('change');

                    // CREAMOS LA OPCIÓN MANUALMENTE PARA SELECT2
                    let nombreEntidad = "{{ $queryDoc->nombre_juridico }}";
                    let newOption = new Option(nombreEntidad, idEntidad, true, true);
                    $('#entidad_m').append(newOption).trigger('change');

                    // Llenamos el input del número de documento
                    $('input[name="numero_documento_externo_m"]').val("{{ $queryDoc->numero_documento_externo }}");
                }

                // CASO PERSONA NATURAL
                else if (idUsuario != "") {
                    $('#tipo_remitente_m').val('natural').trigger('change');

                    // CREAMOS LA OPCIÓN MANUALMENTE PARA SELECT2
                    let nombreUsuario = "{{ $queryDoc->nombre_persona }}";
                    let newOption = new Option(nombreUsuario, idUsuario, true, true);
                    $('#usuario_m').append(newOption).trigger('change');
                }
            @endif
        });

        $(document).on('change', '.input-reemplazo-pdf', function(e) {
            const file = e.target.files[0]; // El archivo real
            const btnCambiar = $(this).siblings('.btn-cambiar-pdf');
            const btnVerNuevo = $(this).siblings('.btn-ver-cambio');
            const spanContenedor = $(this).closest('li').find('span');

            if (file && file.type === "application/pdf") {
                let fileName = file.name;

                // 1. Estética del botón de cambio
                btnCambiar.removeClass('btn-outline-warning').addClass('btn-success')
                    .html('<i class="fas fa-check"></i> Seleccionado');

                // 2. Texto informativo del archivo nuevo
                spanContenedor.find('.temp-file-name').remove();
                spanContenedor.append('<br><small class="text-success temp-file-name">Reemplazar por: ' + fileName +
                    '</small>');

                // 3. GENERAR VISTA PREVIA
                const fileURL = URL.createObjectURL(file); // Crea el link temporal

                // Mostramos el botón y le asignamos la apertura del PDF
                btnVerNuevo.fadeIn().off('click').on('click', function() {
                    window.open(fileURL, '_blank');
                });

            } else if (file) {
                alert("Por favor, selecciona solo archivos PDF.");
                $(this).val('');
                btnVerNuevo.hide();
            }
        });

        function traer_num_expe() {
            $('#num_expe').val('');
            console.log($('#tipo_documento').val() + ' ' + $('#num_exR').val())

            if ($('#tipo_documento').val() == $('#num_exR').val()) {
                window.location.href = window.location.href;
            } else {
                let rutaTipoDocumen =
                    "{{ route('documentario.num_tipo_documento_expe', ['idtipo_docu' => ':id', 'emisor' => ':emi']) }}";
                let url1 = rutaTipoDocumen.replace(':id', $('#tipo_documento').val());
                let url = url1.replace(':emi', $('#emisor').val());
                let formData = $(this).serialize();
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: formData,
                    success: function(response) {
                        let num_expeRe = response['numero_de_exp'];
                        if (num_expeRe === undefined || num_expeRe === null || num_expeRe === '') {
                            num_expeRe = 1;
                            $('#num_expe').val(num_expeRe);
                            $('#num_ex').show();
                        } else {
                            num_expeRe = parseInt(num_expeRe);
                            if (isNaN(num_expeRe)) {
                                $('#num_ex').hide();
                            } else {
                                num_expeRe += 1;
                                $('#num_expe').val(num_expeRe);
                                $('#num_ex').show();
                            }
                        }
                    },
                    error: function() {
                        console.log('error al traer datos');
                    }
                });
            }
        }

        @if (session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error"
            });
        @endif

        @if (session('success'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif
    </script>
@stop
