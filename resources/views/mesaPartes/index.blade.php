@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @canany(['documentario.mesapar.index', 'alumno.matricula.index'])
        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-sign-in-alt"></i> <i class="fas fa-boxes"></i>
                                - CREAR DOCUMENTO -- {{ session('dependencia_id') }} {{ Auth::user()->id }}</h1>
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
    @endcanany
@stop

@section('content')
    @canany(['documentario.mesapar.index', 'alumno.matricula.index'])
        {{-- vistas para mesa de partes --}}
        @if ($id_depen == 24)
            @include('mesaPartes.partials.menu_mesa_partes')
        @else
            <div class="container-fluid">
                <!-- Button trigger modal -->
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
                                    <div class="modal-header" style="background: linear-gradient(135deg, #736001, #e6b884);">
                                        <h5 class="modal-title" id="exampleModalCenterTitle" style="color: white"><i
                                                class="fas fa-sign-in-alt"></i> <i class="fas fa-paste"></i> Nuevo registro</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <form action="{{ route('documentario.registrarDocu') }}" method="post" id="form_regis_doc"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="est_firma" value="1">
                                            <input type="hidden" id="emisor" name="emisor" value="{{ $id_depen }}">
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
                                                            <select id="tipo_documento" class="form-control select2 shadow"
                                                                name="tipo_documento">
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
                                                            <input class="form-control shadow" type="number" id="folio"
                                                                name="folio" onkeydown="return bloqueare(event)">
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

                                                <div class="row mt-3 mb-3"
                                                    style="background-image: linear-gradient(to right, rgb(236, 217, 185), rgba(255, 255, 255, 0.1));">
                                                    <div class="col-sm-9" id="container_dependencias">
                                                        <div class="form-group">
                                                            <label for="dependencia_enviar">
                                                                <i class="fas fa-map-marker-alt text-success"></i>
                                                                Dependencia(s) a enviar: <span class="text-danger">*</span>
                                                            </label>
                                                            <select id="dependencia_enviar"
                                                                class="form-control select2 shadow"
                                                                name="dependencia_enviar[]" multiple>
                                                            </select>
                                                            {{-- <select id="dependencia_enviar" name="dependencia">
                                                            </select> --}}
                                                            <span id="dependencia_enviar_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12" id="container_docentes" style="display: none;">
                                                        <div class="form-group">
                                                            <label class="text-info"><i class="fas fa-users"></i>
                                                                Seleccionar Docente(s)</label>
                                                            <select id="docentes_select" class="form-control select2"
                                                                name="docentes_especificos[]" multiple>
                                                            </select>
                                                            <small class="text-muted">Nota: Al enviar a docentes, no se pueden
                                                                añadir otras dependencias.</small>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12" id="container_egresados" style="display: none;">
                                                        <div class="form-group">
                                                            <label class="text-success"><i class="fas fa-user-graduate"></i>
                                                                Seleccionar Egresado(s)</label>
                                                            <select id="egresados_select" class="form-control select2"
                                                                name="egresados[]" multiple>
                                                            </select>
                                                            <small class="text-muted">Nota: Al enviar a egresados, no se pueden
                                                                añadir otras dependencias.</small>
                                                        </div>
                                                    </div>
                                                    {{-- <div id="container_usuarios" style="display:none;">
                                                        <label>Destinatarios</label>

                                                        <select id="usuarios_select" name="docentes_especificos[]" multiple>
                                                        </select>
                                                    </div> --}}

                                                    @if (session('active_role_name') == 'Dirección')
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
                                                    @endif
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
                                                                <label class="custom-file-label" for="archivo_pdf">Seleccionar
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

                <!-- tabla documentos -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-danger card-outline">
                            <div class="card-header" style="background-color: #dddddd">
                                <h3 class="card-title"><i class="fas fa-folder-open mr-2 text-danger"></i> <STROng>Tabla
                                        documentos -></STROng></h3>
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
                                                    <th style="text-align: center;">Dependencia</th>
                                                    <th style="text-align: center;">Persona</th>
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
        @endif
    @endcanany
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
        //     const notificationSound = new Audio('{{ asset('sound/noti.mp3') }}');

        //     Echo.private('dependencia.' + dependenciaId)
        //         .listen('.DocumentoRecibido', (e) => {
        //             notificationSound.play().catch(err => console.log("Audio bloqueado temporalmente"));
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: "top-end",
        //                 showConfirmButton: false,
        //                 timer: 3000,
        //                 timerProgressBar: true,
        //                 didOpen: (toast) => {
        //                     toast.onmouseenter = Swal.stopTimer;
        //                     toast.onmouseleave = Swal.resumeTimer;
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

        // document.addEventListener("DOMContentLoaded", function() {
        //     Echo.private('dependencia.' + dependenciaId)
        //         .listen('.noEditarDocumento', (e) => {
        //             $('#datatablesSimple').DataTable().ajax.reload();
        //         });
        // });

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
            /////////////////////////////////////////////////////////////para DOCUMENTOS DE recepcion
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

            $('#num_ex').hide();
            $('#exampleModalCenter').on('shown.bs.modal', function() {

                var fecha = new Date();
                fecha.setHours(fecha.getHours() - 5);
                var fecha_hora_actu = fecha.toISOString().slice(0, 19);
                $('#fecha_actual').val(fecha_hora_actu);

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

            // $('#tipo_documento').change(traer_num_expe);

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
                            $('#detalle_fisico').fadeOut().removeAttr('required').val(
                                '');
                            // 1. Limpiar el valor del input file
                            $('#archivo_pdf').val('');

                            // 2. Resetear el texto del label (importante en AdminLTE)
                            $('#archivo_pdf').next('.custom-file-label').html(
                                'Seleccionar PDF');

                            // 3. Limpiar mensajes de error si los hubiera
                            $('#archivo_pdf_error').text('');
                            $('#dependencia_enviar').val(null).trigger('change');
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

                //para quitar el boton de QUITAR A OTRA DEPENDENCIA
                $('#dependencia_enviar').prop('disabled', false).val(null).trigger('change');
                $('#container_docentes').hide();
                $('#docentes_select').val(null).trigger('change');
                $('#input_docente_shadow').remove();
                $('#btn-reset').remove();
            });

            // Variable para controlar si ya se cargó la pestaña de la oficina por primera vez
            let oficinaInicializada = false;

            // Escuchamos el clic en el enlace de la pestaña "De la oficina"
            $('#pills-roles-tab').on('shown.bs.tab', function(e) {

                if (!oficinaInicializada) {
                    let $botonFuts = $('.tipo-btn[data-tipo="2"]');

                    $('.tipo-btn')
                        .removeClass('bg-gradient-primary active')
                        .addClass('bg-gradient-info');

                    // Ahora activamos el de Futs
                    $botonFuts
                        .removeClass('bg-gradient-info')
                        .addClass('bg-gradient-primary active');

                    // 3. Ejecutamos la carga de la tabla
                    cargarTabla(2);

                    // 4. Marcamos como inicializada para que no se recargue cada vez que cambie de pestaña
                    oficinaInicializada = true;
                }
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

            if (dependenciaId == 24) {
                cargarTabla(1);
                $('.tipo-btn[data-tipo="1"]').click();
            } else {
                cargarTabla(2);
                $('.tipo-btn[data-tipo="2"]').click();
            }

            $('#dependencia_enviar').on('change', function() {
                let data = $(this).select2('data'); // Obtenemos los objetos seleccionados

                let esDocente = data.some(item => item.id.trim() === "2");
                let esEgresado = data.some(item => item.id.trim() === "5");


                if (esDocente) {
                    // 1. Buscamos el ID exacto que tiene la opción "Docente"
                    let objetoDocente = data.find(item => item.id.trim() === "2");
                    let idDocenteArea = objetoDocente.id;

                    if ($('#input_docente_shadow').length === 0) {
                        $('#form_regis_doc').append(
                            `<input type="hidden" id="input_docente_shadow" name="dependencia_enviar[]" value="${idDocenteArea}">`
                        );
                    }

                    // 2. Limpiamos cualquier otra dependencia que se haya colado y dejamos SOLO "Docente"
                    $(this).val([idDocenteArea]).trigger('change.select2');

                    // 3. Bloqueamos para que no pueda borrar "Docente" ni agregar "Director"
                    $(this).prop('disabled', true);

                    // 4. Mostramos el buscador de la tabla userprofile
                    $('#container_docentes').fadeIn();
                    inicializarBusquedaDocentes();

                    // Agregamos un botón de "X" para resetear si el usuario se equivocó
                    if (!$('#btn-reset').length) {
                        $(this).closest('.form-group').append(
                            '<button type="button" id="btn-reset" class="btn btn-xs btn-outline-danger mt-1">Cambiar a otra dependencia</button>'
                        );
                    }
                }
                if (esEgresado) {
                    // 1. Buscamos el ID exacto que tiene la opción "egresado"
                    let objetoEgresado = data.find(item => item.id.trim() === "5");
                    let idEgresadoArea = objetoEgresado.id;

                    if ($('#input_egresado_shadow').length === 0) {
                        $('#form_regis_doc').append(
                            `<input type="hidden" id="input_egresado_shadow" name="dependencia_enviar[]" value="${idEgresadoArea}">`
                        );
                    }

                    // 2. Limpiamos cualquier otra dependencia que se haya colado y dejamos SOLO "Docente"
                    $(this).val([idEgresadoArea]).trigger('change.select2');

                    // 3. Bloqueamos para que no pueda borrar "Docente" ni agregar "Director"
                    $(this).prop('disabled', true);

                    // 4. Mostramos el buscador de la tabla userprofile
                    $('#container_egresados').fadeIn();
                    inicializarBusquedaEgresados();

                    // Agregamos un botón de "X" para resetear si el usuario se equivocó
                    if (!$('#btn-reset').length) {
                        $(this).closest('.form-group').append(
                            '<button type="button" id="btn-reset-egresados" class="btn btn-xs btn-outline-danger mt-1">Cambiar a otra dependencia</button>'
                        );
                    }
                }
            });
            // $('#dependencia_enviar').on('change', function() {

            //     let dependencia = $(this).val();

            //     if (!dependencia) {
            //         $('#container_usuarios').hide();
            //         return;
            //     }

            //     $('#container_usuarios').show();

            //     cargarUsuarios(dependencia);

            // });
        });

        // function cargarUsuarios(idDependencia) {

        //     // $('#usuarios_select').empty();

        //     $('#usuarios_select').select2({
        //         placeholder: "Busque y seleccione uno o varios docentes",
        //         ajax: {
        //             url: '{{ route('documentario.buscarDocentes') }}',
        //             dataType: 'json',
        //             delay: 250,
        //             processResults: function(data) {
        //                 return {
        //                     results: data
        //                 };
        //             }
        //         },
        //         dropdownParent: $('#exampleModalCenter')
        //     });

        // }

        $('#tipo_documento').on('change', function() {
            const idTipo = $(this).val();
            const emisor = $('#emisor').val(); // Asegúrate de tener este ID en tu vista

            if (!idTipo) {
                $('#num_ex').hide();
                return;
            }

            // URL con los parámetros dinámicos
            let url = "{{ route('documentario.num_tipo_documento_expe', [':id', ':emi']) }}"
                .replace(':id', idTipo)
                .replace(':emi', emisor);

            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    $('#num_expe').val('Calculando...');
                },
                success: function(response) {
                    // Si no hay documentos previos, response será null o vacío
                    let ultimoNumero = (response && response.numero_de_exp) ? parseInt(response
                        .numero_de_exp) : 0;
                    let proximoNumero = ultimoNumero + 1;

                    $('#num_expe').val(proximoNumero);
                    $('#num_ex').fadeIn();

                    // Opcional: Actualizar fecha automática
                    var fecha_m = new Date();
                    fecha_m.setHours(fecha_m.getHours() - 5);
                    var fecha_hora_actu = fecha_m.toISOString().slice(0, 19);
                    $('#fecha_actual').val(fecha_hora_actu);
                }
            });
        });

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
                        data: "nombre_dependencia",
                        name: 'nombre_dependencia',
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> - ' + data + '</p>';
                        }
                    },
                    {
                        data: "nombre",
                        name: 'nombre',
                        render: function(data, type, row) {
                            if (data != null) {
                                return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                            } else {
                                return '<p> <i class="fa-solid fa-calendar-days"></i> ---- </p>';
                            }
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
                            $(td).css('background-color', '#f19e402f');
                        }
                    },
                    {
                        targets: 4,
                        width: "40%",
                        createdCell: function(td, cellData) {
                            $(td).css('background-color', '#f19e402f');
                        }
                    },
                    {
                        targets: 5,
                        width: "40%",
                        createdCell: function(td, cellData) {
                            $(td).css('background-color', '#f19e402f');
                        }
                    },
                    {
                        targets: 6,
                        width: "8%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    }
                ],
                responsive: true
            });

        }

        // 2. Función para inicializar el segundo Select2 (Docentes)
        function inicializarBusquedaDocentes() {
            $('#docentes_select').select2({
                placeholder: "Busque y seleccione uno o varios docentes",
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
                dropdownParent: $('#exampleModalCenter')
            });
        }

        function inicializarBusquedaEgresados() {
            $('#egresados_select').select2({
                placeholder: "Busque por N° DNI O APELLIDOS/NOMBRES y seleccione uno o varios egresados",
                ajax: {
                    url: '{{ route('documentario.buscarEgresados') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                dropdownParent: $('#exampleModalCenter')
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

        $(document).on('click', '#btn-reset-egresados', function() {
            $('#dependencia_enviar').prop('disabled', false).val(null).trigger('change');
            $('#container_egresados').hide();
            $('#egresados_select').val(null).trigger('change');
            $('#input_egresado_shadow').remove();
            $(this).remove();
        });
    </script>

    <script>
        function toggleEntidadExterna(checkbox) {
            const fields = document.getElementById('entidadExternaFields');
            fields.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
@stop
