@push('css')
    <style>
        /* Estilo personalizado para pestañas profesionales */
        .nav-pills-custom .nav-link {
            color: #495057;
            background: #fff;
            border: 0;
            border-radius: 8px;
            /* Bordes redondeados */
            padding: 0.8rem 1.5rem;
            margin-right: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Sombra suave */
        }

        .nav-pills-custom .nav-link.active {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            /* Sombra de énfasis */
            transform: translateY(-2px);
            /* Efecto de elevación */
        }

        .nav-pills-custom .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        .tab-content-custom {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-top: 15px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }
    </style>
@endpush

<div class="container-fluid">
    <ul class="nav nav-pills nav-pills-custom" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab">
                <i class="fas fa-file-alt mr-2"></i> Recepcionar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-roles-tab" data-toggle="pill" href="#pills-roles" role="tab">
                <i class="fas fa-user-shield mr-2"></i> De la oficina
            </a>
        </li>
    </ul>

    <div class="tab-content tab-content-custom shadow-lg" id="pills-tabContent"
        style="border-radius: 20px;
            background: linear-gradient(135deg, #ccc4bc, #ffffff);
            border: 1px solid #ced4da;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;">
        <!-- crear docu RECEPCIONAR -->
        <div class="tab-pane fade show active" id="pills-general" role="tabpanel">

            <!-- Button trigger modal -->
            <div class="row">
                <div class="col-12 mb-1">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success shadow-lg" data-toggle="modal"
                        data-target="#exampleModalCenter_m">
                        <i class="fas fa-sign-in-alt"></i> Crear nuevo registro
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter_m" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenter_mTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header"
                                    style="background: linear-gradient(135deg, #924900, #d49d5e); ">
                                    <h5 class="modal-title" id="exampleModalCenter_mTitle" style="color: white"><i
                                            class="fas fa-sign-in-alt"></i> <i class="fas fa-paste"></i> Nuevo
                                        registro</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form action="{{ route('documentario.registrarDocu_m') }}" method="post"
                                    id="form_regis_doc_m" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="est_firma" value="0">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <input type="hidden" id="emisor" name="emisor"
                                                    value="{{ $id_depen }}">
                                                <input type="hidden" id="id_usuTrabajador" name="id_usuTrabajador"
                                                    value="{{ $id_usuTrabajador }}">

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="tipo_documento_m">
                                                            <i class="fas fa-file-invoice text-primary mr-1"></i> Tipo
                                                            documento:
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="tipo_documento_m"
                                                            class="form-control select2-bootstrap4"
                                                            name="tipo_documento_m">
                                                            <option value="0" selected disabled>Seleccione tipo
                                                                documento ...</option>
                                                            @if ($rol->nombre_dependencia == 'Mesa de Partes')
                                                                <option value="1">Fut</option>
                                                            @endif
                                                            <option value="2">Oficios</option>
                                                            <option value="3">Informe</option>
                                                            <option value="4">Requerimiento</option>
                                                            <option value="5">Memorándum</option>
                                                            <option value="6">Memorándum Multi.</option>
                                                            <option value="7">Resoluciones</option>
                                                            <option value="8">Otros</option>
                                                        </select>
                                                        <span id="tipo_documento_m_error"
                                                            class="error invalid-feedback"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="fecha_actual_m">
                                                            <i class="fas fa-calendar-check text-info mr-1"></i> Fecha
                                                            actual:
                                                        </label>
                                                        <input class="form-control bg-light" type="datetime-local"
                                                            id="fecha_actual_m" name="fecha_actual_m" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="folio_m">
                                                            <i class="fas fa-pager text-warning mr-1"></i> Folio:
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input class="form-control" type="number" id="folio_m"
                                                            name="folio_m" onkeydown="return bloqueare(event)"
                                                            placeholder="0">
                                                        <span id="folio_m_error" class="text-danger small"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2" id="num_ex_m">
                                                    <div class="form-group">
                                                        <label for="num_expe_m">
                                                            <i class="fas fa-fingerprint text-secondary mr-1"></i> N°
                                                            Exp:
                                                        </label>
                                                        <input class="form-control bg-light" type="text"
                                                            id="num_expe_m" name="num_expe_m" readonly
                                                            placeholder="Auto">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                @if ($id_depen == 24)
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-user-friends text-indigo mr-1"></i>
                                                                Tipo de Remitente: <span
                                                                    class="text-danger">*</span></label>
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
                                                            <span id="usuario_m_error"
                                                                class="text-danger small"></span>
                                                        </div>
                                                    </div>

                                                    <div id="div_entidad_m_externa" class="col-sm-12"
                                                        style="display:none;">
                                                        <div class="card card-outline card-info shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label><i
                                                                                    class="fas fa-building text-info mr-1"></i>
                                                                                Entidad remitente:</label>
                                                                            <select id="entidad_m"
                                                                                name="id_entidad_m_externa"
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
                                                                            <i
                                                                                class="fas fa-external-link-alt mr-1"></i>
                                                                            Consultar RUC en SUNAT
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="dependencia_enviar_m">
                                                            <i class="fas fa-route text-orange mr-1"></i>
                                                            Dependencia(s) a enviar:
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="dependencia_enviar_m" class="form-control select2"
                                                            name="dependencia_enviar_m[]" multiple="multiple"
                                                            data-placeholder="Seleccione dependencias..."></select>
                                                        <span id="dependencia_enviar_m_error"
                                                            class="text-danger small"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-align-left text-primary mr-1"></i>
                                                            Asunto <span class="text-danger">*</span></label>
                                                        <textarea id="asunto_m" class="form-control" rows="3" placeholder="Resumen del trámite..." name="asunto_m"></textarea>
                                                        <span id="asunto_m_error" class="text-danger small"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="para_su_m"><i
                                                                class="fas fa-tasks text-success mr-1"></i> Acción a
                                                            realizar (Para su): <span
                                                                class="text-danger">*</span></label>
                                                        <select id="para_su_m" class="form-control select2-bootstrap4"
                                                            name="para_su_m">
                                                            <option value="0">Seleccione ...</option>
                                                            @foreach ($detalle_tramite as $detra)
                                                                <option value="{{ $detra->iddetalle_tramite }}">
                                                                    {{ $detra->nombre_detalle_tramite }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="para_su_m_error" class="text-danger small"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-comment-dots text-gray mr-1"></i>
                                                            Recomendaciones / Observaciones:</label>
                                                        <textarea id="recomendaciones_m" class="form-control" rows="2" placeholder="Notas adicionales (opcional)..."
                                                            name="Recomendaciones_m"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center bg-light p-3 border rounded">
                                                <div class="col-sm-6 border-right">
                                                    <div class="form-group mb-0">
                                                        <div class="custom-control custom-switch">
                                                            <input class="custom-control-input" type="checkbox"
                                                                id="check_fisico_m" name="con_anexos_fisicos_m">
                                                            <label for="check_fisico_m" class="custom-control-label">
                                                                <i class="fas fa-box-open text-brown mr-1"></i>
                                                                ¿Contiene anexos físicos?
                                                            </label>
                                                        </div>
                                                        <input type="text" id="detalle_fisico_m"
                                                            name="detalle_anexos_fisicos_m"
                                                            class="form-control form-control-sm mt-2"
                                                            placeholder="Ej: 01 CD, Planos..." style="display: none;">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group mb-0">
                                                        <label for="archivo_pdf_m" class="mb-1"><i
                                                                class="fas fa-file-pdf text-danger mr-1"></i> Documento
                                                            Digital (PDF):</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="archivo_pdf_m" name="archivo_pdf_m"
                                                                accept="application/pdf">
                                                            <label class="custom-file-label"
                                                                for="archivo_pdf_m">Elegir archivo...</label>
                                                        </div>
                                                        <span id="archivo_pdf_m_error"
                                                            class="text-danger small"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="text-center mt-4">
                                                <button type="button" onclick="descargarWordBorrador()"
                                                    class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                    <i class="fas fa-file-word mr-1"></i> Generar Borrador Word
                                                </button>
                                            </div> --}}
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light justify-content-between">
                                        <button type="button" class="btn btn-danger shadow-sm" data-dismiss="modal">
                                            <i class="fas fa-times-circle mr-1"></i> Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-success shadow px-4">
                                            <i class="fas fa-save mr-1"></i> Registrar Documento
                                        </button>
                                    </div>
                                </form>

                                <script>
                                    // Script para que se vea el nombre del archivo PDF en AdminLTE/Bootstrap
                                    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                                        var fileName = document.getElementById("archivo_pdf_m").files[0].name;
                                        var nextSibling = e.target.nextElementSibling;
                                        nextSibling.innerText = fileName;
                                    });

                                    // Toggle para el detalle físico
                                    document.getElementById('check_fisico_m').addEventListener('change', function() {
                                        const detailInput = document.getElementById('detalle_fisico_m');
                                        detailInput.style.display = this.checked ? 'block' : 'none';
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tipos de documentos -->
            <div class="row justify-content-center pb-2">
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo_m="1" type="button"
                        class="btn btn-block bg-gradient-info btn-sm tipo-btn_m"><i class="far fa-file-alt"></i>
                        Futs</button>
                </div>
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo_m="2" type="button"
                        class="btn btn-block bg-gradient-info btn-sm tipo-btn_m"><i class="far fa-file-alt"></i>
                        Oficios, informes, cartas, etc</button>
                </div>
            </div>

            <!-- tabla documentos -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-danger card-outline">
                        <div class="card-header" style="background-color: #eeeeee">
                            <h3 class="card-title"><i class="fas fa-list-ol"></i> Tabla documentos recepcionados</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    {{-- <div class="table-responsive"> --}}
                                    <table id="datatablesSimple_m" class="table table-hover" style="width: 100%;">
                                        <thead>
                                            <tr style="background-color:#f19e40">
                                                <th style="width: 5%; text-align: center;">N° Expe.</th>
                                                <th style="width: 5%; text-align: center;">Contador</th>
                                                <th style="width: 10%; text-align: center;">Fecha y hora</th>
                                                <th style="width: 40%; text-align: center;">Asunto</th>
                                                <th style="width: 8%; text-align: center;">Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    {{-- </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- crear docu DE LA OFICINA -->
        <div class="tab-pane fade" id="pills-roles" role="tabpanel">
            <!-- Button trigger modal -->
            <div class="row mb-2">
                <div class="col-12 mb-1">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success shadow-lg" data-toggle="modal"
                        data-target="#exampleModalCenter">
                        <i class="fas fa-sign-in-alt"></i> Crear nuevo registro
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header"
                                    style="background: linear-gradient(135deg, #736001, #e6b884);">
                                    <h5 class="modal-title" id="exampleModalCenterTitle" style="color: white"><i
                                            class="fas fa-sign-in-alt"></i> <i class="fas fa-paste"></i> Nuevo
                                        registro</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form action="{{ route('documentario.registrarDocu') }}" method="post"
                                    id="form_regis_doc" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="est_firma" value="1">
                                        <input type="hidden" id="emisor" name="emisor"
                                            value="{{ $id_depen }}">
                                        <input type="hidden" id="id_usuTrabajador" name="id_usuTrabajador"
                                            value="{{ $id_usuTrabajador }}">

                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="tipo_documento">
                                                            <i class="fas fa-file-contract text-primary"></i> Tipo
                                                            documento: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="tipo_documento"
                                                            class="form-control select2 shadow" name="tipo_documento">
                                                            <option value="0">Seleccione tipo documento ...
                                                            </option>
                                                            @if ($rol->nombre_dependencia == 'Mesa de Partes')
                                                                <option value="1">Fut</option>
                                                            @endif
                                                            <option value="2">Oficios</option>
                                                            <option value="3">Informe</option>
                                                            <option value="4">Requerimiento</option>
                                                            <option value="5">Memorándum</option>
                                                            <option value="6">Memorándum Multi.</option>
                                                            <option value="7">Resoluciones</option>
                                                            <option value="8">Otros</option>
                                                        </select>
                                                        <span id="tipo_documento_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="fecha_actual">
                                                            <i class="fas fa-calendar-alt text-info"></i> Fecha actual:
                                                        </label>
                                                        <input class="form-control bg-light" type="datetime-local"
                                                            id="fecha_actual" name="fecha_actual" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="folio">
                                                            <i class="fas fa-list-ol text-warning"></i> Folio: <span
                                                                class="text-danger">*</span>
                                                        </label>
                                                        <input class="form-control shadow" type="number"
                                                            id="folio" name="folio"
                                                            onkeydown="return bloqueare(event)">
                                                        <span id="folio_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2 shadow"
                                                    style="background: linear-gradient(90deg, #eac096, #ffffff); ">
                                                    <div class="form-group">
                                                        <label for="num_expe">
                                                            <i class="fas fa-fingerprint text-secondary"></i> N° exp.
                                                            local:
                                                        </label>
                                                        <input class="form-control bg-light" type="text"
                                                            style="background: linear-gradient(135deg, #eac096, #ebe6df); "
                                                            id="num_expe" name="num_expe" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-9">
                                                    <div class="form-group">
                                                        <label for="dependencia_enviar">
                                                            <i class="fas fa-map-marker-alt text-success"></i>
                                                            Dependencia(s) a enviar: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="dependencia_enviar"
                                                            class="form-control select2 shadow"
                                                            name="dependencia_enviar[]" multiple>
                                                        </select>
                                                        <span id="dependencia_enviar_error"
                                                            class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3 text-center">
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
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><i class="fas fa-pen-fancy text-primary"></i> Asunto
                                                            <span class="text-danger">*</span></label>
                                                        <textarea id="asunto" class="form-control shadow" rows="2" placeholder="Escriba el asunto del documento..."
                                                            name="asunto"></textarea>
                                                        <span id="asunto_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="para_su">
                                                            <i class="fas fa-directions" style="color: #6f42c1;"></i>
                                                            Para su: <span class="text-danger">*</span>
                                                        </label>
                                                        <select id="para_su" class="form-control shadow"
                                                            name="para_su">
                                                            <option value="0">Seleccione ...</option>
                                                            @foreach ($detalle_tramite as $detra)
                                                                <option value="{{ $detra->iddetalle_tramite }}">
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
                                                            name="Recomendaciones"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row border-top pt-3 mt-2 bg-light rounded p-2 shadow-sm">
                                                <div class="col-sm-6 border-right">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                id="check_fisico" name="con_anexos_fisicos">
                                                            <label for="check_fisico" class="custom-control-label">
                                                                <i class="fas fa-archive text-warning"></i> ¿Contiene
                                                                anexos físicos?
                                                            </label>
                                                        </div>
                                                        <input type="text" id="detalle_fisico"
                                                            name="detalle_anexos_fisicos"
                                                            class="form-control mt-2 border-warning"
                                                            placeholder="Ej: 01 CD, Planos..." style="display: none;">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="archivo_pdf">
                                                            <i class="fas fa-file-pdf text-danger"></i> Subir Documento
                                                            (PDF):
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="archivo_pdf" name="archivo_pdf"
                                                                accept="application/pdf">
                                                            <label class="custom-file-label"
                                                                for="archivo_pdf">Seleccionar
                                                                PDF...</label>
                                                        </div>
                                                        <span id="archivo_pdf_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 mt-2">
                                                    <button type="button" onclick="descargarWordBorrador()"
                                                        class="btn btn-outline-info btn-block shadow-sm">
                                                        <i class="fas fa-file-word"></i> GENERAR BORRADOR AUTOMÁTICO
                                                        PARA FIRMA
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-default border" data-dismiss="modal">
                                            <i class="fas fa-times text-danger"></i> Cerrar
                                        </button>
                                        <button type="submit" class="btn btn-success px-4 shadow">
                                            <i class="fas fa-save"></i> REGISTRAR TRÁMITE
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tipos de documentos -->
            <div class="row justify-content-center pb-2">

                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="2" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Oficios</button>
                </div>
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="3" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Informes</button>
                </div>
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="4" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Requerimientos</button>
                </div>

                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="5" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Memorándum</button>
                </div>
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="6" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Memorándum Multi.</button>
                </div>
                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="7" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Resoluciones</button>
                </div>

                <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                    <button data-tipo="8" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                            class="far fa-file-alt"></i>
                        Otros</button>
                </div>
            </div>

            <!-- tabla documentos -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-danger card-outline">
                        <div class="card-header" style="background-color: #dddddd">
                            <h3 class="card-title"><i class="fas fa-list-ol"></i> Tabla documentos ddddd</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="datatablesSimple" class="table table-hover">
                                        <thead>
                                            <tr style="background-color:#f19e40">
                                                <th style="text-align: center;">N° Expe.</th>
                                                <th style="text-align: center;">Contador</th>
                                                <th style="text-align: center;">Fecha y hora</th>
                                                <th style="text-align: center;">Asunto</th>
                                                <th style="text-align: center;">Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $('#num_ex_m').hide();

        $(document).ready(function() {
            cargarTabla_m(1);
            $('.tipo-btn_m[data-tipo_m="1"]').click();

            $('.tipo-btn_m').on('click', function() {
                let tipo = $(this).data('tipo');
                if (tipoActivo === tipo) return; // si ya está activo, no hace nada
                tipoActivo = tipo;

                // Resetear todos los botones
                $('.tipo-btn_m')
                    .removeClass('bg-gradient-primary')
                    .addClass('bg-gradient-info')
                    .removeClass('active');

                // Activar el botón presionado
                $(this)
                    .removeClass('bg-gradient-info')
                    .addClass('bg-gradient-primary')
                    .addClass('active');
                cargarTabla(tipo);
            });

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

            $('#entidad_m').select2({
                dropdownParent: $('#exampleModalCenter_m'),
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

            $('#exampleModalCenter_m').on('shown.bs.modal', function() {
                var fecha_m = new Date();
                fecha_m.setHours(fecha_m.getHours() - 5);
                var fecha_hora_actu = fecha_m.toISOString().slice(0, 19);
                $('#fecha_actual_m').val(fecha_hora_actu);

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
                    dropdownParent: $('#exampleModalCenter_m'),
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
                }).on('select2:open', function(e) {
                    e.stopPropagation();
                });

                $('#dependencia_enviar_m').select2({
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
                    dropdownParent: $('#exampleModalCenter_m'),
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
                }).on('select2:open', function(e) {
                    $('#dependencia_enviar_m').removeClass('select2-hidden');
                    e.stopPropagation();
                });

                $('#customSwitch3_m').on('change', function() {
                    const activado = $(this).is(':checked');
                    if (activado) {
                        $('#dependencia_enviar_m').prop('disabled', true).val(null).trigger(
                            'change');
                    } else {
                        $('#dependencia_enviar_m').prop('disabled', false);
                    }
                });

                $('#check_fisico_m').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#detalle_fisico_m').fadeIn().attr('required', true);
                    } else {
                        $('#detalle_fisico_m').fadeOut().removeAttr('required').val('');
                    }
                });
            });
        });

        // $('#tipo_documento_m').change(traer_num_expe_m);

        $('.tipo-btn_m').on('click', function() {
            let tipo = $(this).data('tipo_m');
            if (tipoActivo === tipo) return; // si ya está activo, no hace nada
            tipoActivo = tipo;

            // Resetear todos los botones
            $('.tipo-btn_m')
                .removeClass('bg-gradient-primary')
                .addClass('bg-gradient-info')
                .removeClass('active');

            // Activar el botón presionado
            $(this)
                .removeClass('bg-gradient-info')
                .addClass('bg-gradient-primary')
                .addClass('active');
            cargarTabla_m(tipo);
        });


        $('#form_regis_doc_m').submit(function(event) {
            event.preventDefault();

            var butonEnviardatos_m = $('#form_regis_doc_m button[type="submit"]');
            butonEnviardatos_m.prop('disabled', true);
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    setTimeout(() => {
                        document.activeElement.blur();
                        $('body').trigger('focus');
                        $('#exampleModalCenter_m').modal('hide');
                    }, 50);

                    Swal.fire({
                        title: "Éxito!",
                        text: response.success,
                        icon: "success"
                    }).then(() => {

                        $('#form_regis_doc_m')[0].reset();
                        $('#datatablesSimple_m').DataTable().ajax.reload();
                        $('.text-danger_m').text('');
                        $('#usuario_m').val('0').trigger('change');
                        $('#num_ex_m').hide();
                        $('#detalle_fisico_m').fadeOut().removeAttr('required').val('');
                        // 1. Limpiar el valor del input file
                        $('#archivo_pdf_m').val('');

                        // 2. Resetear el texto del label (importante en AdminLTE)
                        $('#archivo_pdf_m').next('.custom-file-label').html('Seleccionar PDF');

                        // 3. Limpiar mensajes de error si los hubiera
                        $('#archivo_pdf_m_error').text('');
                        $('#dependencia_enviar_m').val(null).trigger('change');
                    });
                    butonEnviardatos_m.prop('disabled', false);
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;

                    $('.text-danger_m').text('');

                    if (errors) {
                        if (errors.tipo_documento_m) {
                            $('#tipo_documento_m_error').text(errors.tipo_documento_m[0]);
                        }
                        if (errors.dependencia_enviar_m) {
                            $('#dependencia_enviar_m_error').text(errors.dependencia_enviar_m[
                                0]);
                        }
                        if (errors.asunto_m) {
                            $('#asunto_m_error').text(errors.asunto_m[0]);
                        }
                        if (errors.para_su_m) {
                            $('#para_su_m_error').text(errors.para_su_m[0]);
                        }
                        if (errors.usuario_m) {
                            $('#usuario_m_error').text(errors.usuario_m[0]);
                        }
                        if (errors.folio_m) {
                            $('#folio_m_error').text(errors.folio_m[0]);
                        }
                    }
                    butonEnviardatos_m.prop('disabled', false);
                }
            });
        });

        $('#tipo_documento_m').on('change', function() {
            const idTipo = $(this).val();
            const emisor = $('#emisor').val(); // Asegúrate de tener este ID en tu vista

            if (!idTipo) {
                $('#num_ex_m').hide();
                return;
            }

            // URL con los parámetros dinámicos
            let url = "{{ route('documentario.num_tipo_documento_expe_m', [':id', ':emi']) }}"
                .replace(':id', idTipo)
                .replace(':emi', emisor);

            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    $('#num_expe_m').val('Calculando...');
                },
                success: function(response) {
                    // Si no hay documentos previos, response será null o vacío
                    let ultimoNumero = (response && response.numero_de_exp) ? parseInt(response
                        .numero_de_exp) : 0;
                    let proximoNumero = ultimoNumero + 1;

                    $('#num_expe_m').val(proximoNumero);
                    $('#num_ex_m').fadeIn();

                    // Opcional: Actualizar fecha automática
                    var fecha_m = new Date();
                    fecha_m.setHours(fecha_m.getHours() - 5);
                    var fecha_hora_actu = fecha_m.toISOString().slice(0, 19);
                    $('#fecha_actual_m').val(fecha_hora_actu);
                }
            });
        });

        // function traer_num_expe_m() {
        //     $('#num_expe_m').val('');
        //     let rutaTipoDocumen =
        //         "{{ route('documentario.num_tipo_documento_expe_m', ['idtipo_docu' => ':id', 'emisor' => ':emi']) }}";
        //     let url1 = rutaTipoDocumen.replace(':id', $('#tipo_documento_m').val());
        //     let url = url1.replace(':emi', $('#emisor').val());
        //     let formData = $(this).serialize();
        //     $.ajax({
        //         type: 'GET',
        //         url: url,
        //         data: formData,
        //         success: function(response) {
        //             let num_expeRe = response['numero_de_exp'];
        //             if (num_expeRe === undefined || num_expeRe === null || num_expeRe === '') {
        //                 num_expeRe = 1;
        //                 $('#num_expe_m').val(num_expeRe);
        //                 $('#num_ex_m').show();
        //             } else {
        //                 num_expeRe = parseInt(num_expeRe);
        //                 if (isNaN(num_expeRe)) {
        //                     $('#num_ex_m').hide();
        //                 } else {
        //                     num_expeRe += 1;
        //                     $('#num_expe_m').val(num_expeRe);
        //                     $('#num_ex_m').show();
        //                 }
        //             }
        //             var fecha = new Date();
        //             fecha.setHours(fecha.getHours() - 5);
        //             var fecha_hora_actu = fecha.toISOString().slice(0, 19);
        //             $('#fecha_actual_m').val(fecha_hora_actu);
        //         },
        //         error: function() {
        //             console.log('error al traer datos');
        //         }
        //     })
        // }

        function cargarTabla_m(tipo) {

            if ($.fn.DataTable.isDataTable('#datatablesSimple_m')) {
                $('#datatablesSimple_m').DataTable().clear().destroy(); // destrucción segura
            }

            let ruta_doc_emitidos =
                "{{ route('documentario.mesapar.emitidos_m', ['idtipo_docu' => ':id', 'emisor' => ':emi']) }}";
            let urlruta_doc_emitidosEmi = ruta_doc_emitidos.replace(':id', tipo);
            let urlruta_doc_emitidosf = urlruta_doc_emitidosEmi.replace(':emi', $('#emisor').val());

            $('#datatablesSimple_m').DataTable({
                scrollX: true,
                autoWidth: false,
                aaSorting: [],
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "Ningún valor encontrado",
                    info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Ningún valor encontrado",
                    infoFiltered: "(filtrados desde _MAX_ registros totales)",
                    search: "Buscar:",
                    loadingRecords: "Cargando...",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    emptyTable: "Ningun registro en la tabla",
                },
                processing: true,
                serverSide: true,
                ajax: urlruta_doc_emitidosf,
                columns: [{
                        data: "iddocumentos",
                        name: 'iddocumentos',
                        searchable: true,
                        render: function(data, type, row) {
                            return '<div class=""><p class="fw-semibold text-dark" style="text-align: center;">' +
                                '' + data + '</p></div>';
                        }
                    },
                    {
                        data: 'numero_de_exp',
                        name: 'numero_de_exp',
                        searchable: true,
                        render: function(data, type, row) {
                            return '<div class=" bg-success-subtle text-dark-emphasis"><p class="fw-semibold text-dark">' +
                                data + ' - ' + row.nombre_documento + '</p></div>';
                        }
                    },
                    {
                        data: "fecha_ingreso",
                        name: 'fecha_ingreso',
                        searchable: true,
                        render: function(data, type, row) {
                            return '<p>' + data + '</p>';
                        }
                    },
                    {
                        data: "asunto",
                        name: 'asunto',
                        searchable: true,
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                        }
                    },
                    {
                        data: "btn",
                        searchable: false
                    }
                ],
                columnDefs: [{
                        targets: 0,
                        width: "5%",
                        createdCell: function(td) {
                            $(td).css({
                                'background-color': "#f19e40",
                                'white-space': 'nowrap',
                                'overflow': 'hidden',
                                'text-overflow': 'ellipsis'
                            });
                        }
                    },
                    {
                        targets: 1,
                        width: "5%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e407f");
                        }
                    },
                    {
                        targets: 2,
                        width: "10%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    },
                    {
                        targets: 3,
                        width: "40%",
                        createdCell: function(td, cellData) {
                            $(td).html(`
                            <div style="
                                max-width: 500px;
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                display: block;
                            " title="${cellData}">
                                ${cellData}
                            </div>
                            `);
                            $(td).css('background-color', '#f19e402f');
                        }
                    },
                    {
                        targets: 4,
                        width: "8%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    }
                ],
                responsive: true
            });
        }
    </script>
@endpush
