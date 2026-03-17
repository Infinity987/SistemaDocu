@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @can('documentario.mesapar.index')
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-sign-in-alt"></i> <i class="fas fa-boxes"></i>
                                - CREAR DOCUMENTO {{ session('active_role_name') }}</h1>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">inicio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('documentario.mesapar.index')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-1">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">
                        <i class="fas fa-sign-in-alt"></i> Crear nuevo registro
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: rgb(176, 86, 12);">
                                    <h5 class="modal-title" id="exampleModalCenterTitle" style="color: white"><i
                                            class="fas fa-sign-in-alt"></i> <i class="fas fa-paste"></i> Nuevo registro</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('documentario.registrarDocu') }}" method="post" id="form_regis_doc">

                                    @csrf
                                    <div class="modal-body">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <input type="hidden" id="emisor" name="emisor"
                                                    value ="{{ $id_depen }}">
                                                <input type="hidden" id="id_usuTrabajador" name="id_usuTrabajador"
                                                    value ="{{ $id_usuTrabajador }}">


                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="tipo_documento">Tipo documento: <span
                                                                style="color: red">*</span> </label>
                                                        <select id="tipo_documento" class="form-control"
                                                            placeholder="Ingrese el asunto" name="tipo_documento">
                                                            <option value="0">Seleccione tipo documento ...</option>
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
                                                        <label for="num_expe">Fecha actual:</label>
                                                        <input class="form-control" type="datetime-local" id="fecha_actual"
                                                            name="fecha_actual" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2" id="">
                                                    <div class="form-group">
                                                        <label for="folio">Folio: <span style="color: red">*</span></label>
                                                        <input class="form-control" type="number" id="folio" name="folio"
                                                            onkeydown="return bloqueare(event)">
                                                        <span id="folio_error" class="text-danger"></span>
                                                    </div>
                                                </div>



                                                <div class="col-sm-2" id="num_ex">
                                                    <div class="form-group">
                                                        <label for="num_expe">N° expe. local:</label>
                                                        <input class="form-control" type="text" id="num_expe"
                                                            name="num_expe" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                @if ($id_depen == 24)
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Tipo de Remitente: <span style="color: red">*</span></label>
                                                            <select id="tipo_remitente" name="tipo_remitente"
                                                                class="form-control">
                                                                <option value="natural">Persona Natural</option>
                                                                <option value="juridica">Entidad Externa (Jurídica)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12" id="div_persona_natural">
                                                        <div class="form-group">
                                                            <label>Usuario / Persona Natural: <span
                                                                    style="color: red">*</span></label>
                                                            <select id="usuario" class="form-control select2"
                                                                name="usuario" style="width: 100%;"></select>
                                                            <span id="usuario_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div id="div_entidad_externa" class="col-sm-12" style="display:none;">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>Entidad remitente:</label>
                                                                    <select id="entidad" name="id_entidad_externa"
                                                                        class="form-control select2"></select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>N° Oficio/Informe Externo:</label>
                                                                    <input type="text" name="numero_documento_externo"
                                                                        class="form-control"
                                                                        placeholder="Ej: Oficio N.º 123-2026-MPP">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-3">
                                                                <a href="https://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaWeb.jsp"
                                                                    target="_blank" class="btn btn-outline-info btn-sm">
                                                                    <i class="fas fa-search"></i> Consultar RUC en SUNAT
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="dependencia_enviar">Dependencia(s) a enviar: <span
                                                                style="color: red">*</span></label>
                                                        <select id="dependencia_enviar" class="form-control select2-hidden"
                                                            name="dependencia_enviar[]" multiple>
                                                            {{-- <option value="0">Seleccione dependencia ...</option> --}}
                                                            {{-- @foreach ($dependencias as $depe)
                                                                @if ($depe->nombre_dependencia != $rol->nombre_dependencia && $depe->nombre_dependencia != 'Administrador Sistema')
                                                                    <option value="{{ $depe->iddependencias }}">
                                                                        {{ $depe->nombre_dependencia }}</option>
                                                                @endif
                                                            @endforeach --}}
                                                        </select>
                                                        <span id="dependencia_enviar_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2" id="">
                                                    <div class="form-group">
                                                        <label for="folio">Todas Depen.:</label>

                                                        <div class="form-group d-flex justify-content-center">
                                                            <div
                                                                class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="customSwitch3" name="todasDepenSelects">
                                                                <label class="custom-control-label"
                                                                    for="customSwitch3"></label>
                                                            </div>
                                                        </div>

                                                        <span id="folio_error" class="text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="row">

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Asunto <span style="color: red">*</span></label>
                                                        <textarea id="asunto" class="form-control" rows="3" placeholder="Escriba el asunto del documento ..."
                                                            name="asunto"></textarea>
                                                        <span id="asunto_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                             
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="para_su">Para su: <span
                                                                    style="color: red">*</span></label>
                                                            <select id="para_su" class="form-control"
                                                                placeholder="Ingrese el asunto" name="para_su">
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
                                                        <label>Recomendaciones:</label>
                                                        <textarea id="recomendaciones" class="form-control" rows="3" placeholder="Escriba el asunto del documento ..."
                                                            name="Recomendaciones"></textarea>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input" type="checkbox"
                                                                    id="check_fisico" name="con_anexos_fisicos">
                                                                <label for="check_fisico"
                                                                    class="custom-control-label">¿Contiene anexos
                                                                    físicos?</label>
                                                            </div>
                                                            <input type="text" id="detalle_fisico"
                                                                name="detalle_anexos_fisicos" class="form-control mt-2"
                                                                placeholder="01 CD, Planos..." style="display: none;">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="archivo_pdf">Subir Documento (PDF):</label>
                                                            <div class="input-group">
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input"
                                                                        id="archivo_pdf" name="archivo_pdf"
                                                                        accept="application/pdf">
                                                                    <label class="custom-file-label"
                                                                        for="archivo_pdf">Seleccionar PDF</label>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">Opcional si hay anexos físicos
                                                                pesados.</small>
                                                            <span id="archivo_pdf_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" onclick="descargarWordBorrador()"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-file-word"></i> Generar Borrador
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                                class="fas fa-window-close"></i> Cerrar</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                            Registrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center pb-2">
                @if ($id_depen == 24)
                    <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                        <button data-tipo="1" type="button" class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                                class="far fa-file-alt"></i>
                            Futs</button>
                    </div>
                @endif
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
    @endcan
@stop
{{-- @vite(['resources/js/app.js']) --}}

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />

    <style>
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-hidden {
            visibility: hidden;
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
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    @livewireScripts

    <script>
        let dependenciaId = {{ $id_depen }};


        // document.addEventListener("DOMContentLoaded", function() {

        //     Echo.channel('test-channel')
        //         .listen('TestEvent', (e) => {

        //             console.log("Evento recibido");

        //         });

        // });
        document.addEventListener("DOMContentLoaded", function() {
            console.log('llego docu');

            Echo.private('dependencia.' + dependenciaId)
                .listen('.DocumentoRecibido', (e) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                            var audio = new Audio('{{ asset('sound/noti.mp3') }}'); // Ruta sonido
                            audio.play();
                        },
                        didClose: () => {
                            $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e
                                .cont_estados[0]
                                .cont_estado);
                        }
                    });

                    Toast.fire({
                        icon: "success",
                        title: "Nuevo documento recibido"
                    });
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.noEditarDocumento', (e) => {
                    $('#datatablesSimple').DataTable().ajax.reload();
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.private('dependencia.' + dependenciaId)
                .listen('.editarDocumento', (e) => {
                    $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                });
        });
    </script>

    <script>
        let tabla;
        let tipoActivo = null;

        function bloqueare(event) {
            if (event.key === "e" || event.key === "E" || event.key === "-" || event.key === ".") {
                return false;
            }
        }

        $(document).ready(function() {

            $(document).ready(function() {
                // Escuchar el cambio en el tipo de remitente
                $('#tipo_remitente').on('change', function() {
                    let valor = $(this).val();

                    if (valor === 'natural') {
                        // Mostrar usuario, ocultar entidad
                        $('#div_persona_natural').fadeIn();
                        $('#div_entidad_externa').hide();

                        // Limpiar los valores del que se oculta para evitar envíos cruzados
                        $('#entidad').val(null).trigger('change');
                    } else {
                        // Mostrar entidad, ocultar usuario
                        $('#div_entidad_externa').fadeIn();
                        $('#div_persona_natural').hide();

                        // Limpiar los valores del que se oculta
                        $('#usuario').val(null).trigger('change');
                    }
                });

                // Resetear al cerrar el modal para que siempre inicie en Persona Natural
                $('#exampleModalCenter').on('hidden.bs.modal', function() {
                    $('#tipo_remitente').val('natural').trigger('change');
                });
            });

            $('#check_fisico').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#detalle_fisico').fadeIn().attr('required', true);
                } else {
                    $('#detalle_fisico').fadeOut().removeAttr('required').val('');
                }
            });

            // Para mostrar el nombre del archivo seleccionado en el input de AdminLTE
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            $('#entidad').select2({
                dropdownParent: $('#exampleModalCenter'),
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

            $('#num_ex').hide();
            $('#exampleModalCenter').on('shown.bs.modal', function() {

                var fecha = new Date();
                fecha.setHours(fecha.getHours() - 5);
                var fecha_hora_actu = fecha.toISOString().slice(0, 19);
                $('#fecha_actual').val(fecha_hora_actu);

                $('#usuario').select2({
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
                    dropdownParent: $('#exampleModalCenter'),
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
                    dropdownParent: $('#exampleModalCenter'),
                    ajax: {
                        url: '{{ route('documentario.traerDepen') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            console.log(data)
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
                    $('#dependencia_enviar').removeClass('select2-hidden');
                    e.stopPropagation();
                });

                $('#customSwitch3').on('change', function() {
                    const activado = $(this).is(':checked');
                    if (activado) {
                        $('#dependencia_enviar').prop('disabled', true).val(null).trigger('change');
                    } else {
                        $('#dependencia_enviar').prop('disabled', false);
                    }
                });
            });



            $('#editModal').on('hidden.bs.modal', function() {
                $('#usuario').select2('destroy');
                $('#dependencia_enviar').select2('destroy');
            });

            $('#tipo_documento').change(traer_num_expe);

            $('#form_regis_doc').submit(function(event) {
                event.preventDefault();

                var butonEnviardatos = $('#form_regis_doc button[type="submit"]');
                butonEnviardatos.prop('disabled', true);
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
                            $('#exampleModalCenter').modal('hide');
                        }, 50);

                        Swal.fire({
                            title: "Éxito!",
                            text: response.success,
                            icon: "success"
                        }).then(() => {

                            $('#form_regis_doc')[0].reset();
                            $('#datatablesSimple').DataTable().ajax.reload();
                            $('.text-danger').text('');
                            $('#usuario').val('0').trigger('change');
                            $('#num_ex').hide();
                        });
                        butonEnviardatos.prop('disabled', false);
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $('.text-danger').text('');

                        if (errors) {
                            if (errors.tipo_documento) {
                                $('#tipo_documento_error').text(errors.tipo_documento[0]);
                            }
                            if (errors.dependencia_enviar) {
                                $('#dependencia_enviar_error').text(errors.dependencia_enviar[
                                    0]);
                            }
                            if (errors.asunto) {
                                $('#asunto_error').text(errors.asunto[0]);
                            }
                            if (errors.para_su) {
                                $('#para_su_error').text(errors.para_su[0]);
                            }
                            if (errors.usuario) {
                                $('#usuario_error').text(errors.usuario[0]);
                            }
                            if (errors.folio) {
                                $('#folio_error').text(errors.folio[0]);
                            }
                        }
                        butonEnviardatos.prop('disabled', false);
                    }
                });
            });

            $('.tipo-btn').on('click', function() {
                let tipo = $(this).data('tipo');
                if (tipoActivo === tipo) return; // si ya está activo, no hace nada
                tipoActivo = tipo;

                // Resetear todos los botones
                $('.tipo-btn')
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

            if (dependenciaId == 19) {
                cargarTabla(1);
                $('.tipo-btn[data-tipo="1"]').click();
            } else {
                cargarTabla(2);
                $('.tipo-btn[data-tipo="2"]').click();
            }
        })

        function traer_num_expe() {
            $('#num_expe').val('');
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
                    var fecha = new Date();
                    fecha.setHours(fecha.getHours() - 5);
                    var fecha_hora_actu = fecha.toISOString().slice(0, 19);
                    $('#fecha_actual').val(fecha_hora_actu);
                },
                error: function() {
                    console.log('error al traer datos');
                }
            })
        }

        function descargarWordBorrador() {
            // 1. Obtener el formulario original
            let originalForm = document.getElementById('form_regis_doc');
            if (!originalForm) return;

            // 2. Crear un formulario temporal (clon)
            let tempForm = originalForm.cloneNode(true);

            // 3. Configurar el clon
            tempForm.style.display = 'none';
            tempForm.action = "{{ route('documentario.mesaPartes.word') }}";
            tempForm.target = "_blank";
            tempForm.method = "POST";

            // 4. Copiar los valores de los selects (cloneNode no copia los valores seleccionados)
            let selectsOriginales = originalForm.querySelectorAll('select');
            let selectsClonados = tempForm.querySelectorAll('select');
            selectsOriginales.forEach((select, index) => {
                selectsClonados[index].value = select.value;
            });

            // 5. Copiar valores de textareas
            let textsOriginales = originalForm.querySelectorAll('textarea');
            let textsClonados = tempForm.querySelectorAll('textarea');
            textsOriginales.forEach((text, index) => {
                textsClonados[index].value = text.value;
            });

            // 6. Ejecutar el envío y eliminar el clon
            document.body.appendChild(tempForm);
            tempForm.submit();

            setTimeout(() => {
                document.body.removeChild(tempForm);
            }, 500);
        }

        function cargarTabla(tipo) {

            if ($.fn.DataTable.isDataTable('#datatablesSimple')) {
                $('#datatablesSimple').DataTable().clear().destroy(); // destrucción segura
            }

            let ruta_doc_emitidos =
                "{{ route('documentario.mesapar.emitidos', ['idtipo_docu' => ':id', 'emisor' => ':emi']) }}";
            let urlruta_doc_emitidosEmi = ruta_doc_emitidos.replace(':id', tipo);
            let urlruta_doc_emitidosf = urlruta_doc_emitidosEmi.replace(':emi', $('#emisor').val());

            $('#datatablesSimple').DataTable({
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
                        render: function(data, type, row) {
                            return '<div class=""><p class="fw-semibold text-dark" style="text-align: center;">' +
                                '' + data + '</p></div>';
                        }
                    },
                    {
                        data: 'numero_de_exp',
                        name: 'numero_de_exp',
                        render: function(data, type, row) {
                            return '<div class=" bg-success-subtle text-dark-emphasis"><p class="fw-semibold text-dark" style="text-align: center;">' +
                                data + '</p></div>';
                        }
                    },
                    {
                        data: "fecha_ingreso",
                        name: 'fecha_ingreso',
                        render: function(data, type, row) {

                            return '<p>' + data + '</p>';
                        }
                    },
                    {
                        data: "asunto",
                        name: 'asunto',
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                        }
                    },
                    {
                        data: "btn"
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

    <script>
        function toggleEntidadExterna(checkbox) {
            const fields = document.getElementById('entidadExternaFields');
            fields.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
@stop
