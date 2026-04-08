@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @can('docente.horario')
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
    @endcan
@stop

@section('content')
    @can('docente.horario')
        <section class="content">
            <div class="container-fluid">
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
                                <form action="{{ route('documentario.mesapar.updateDocuEmi') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="iddocumentos" value="{{ $queryDoc->iddocumentos }}">
                                    <input type="hidden" name="iddependencias_emior"
                                        value="{{ $queryMovi->iddependencias_emior }}">
                                    <input type="hidden" name="iddependencias_receptor"
                                        value="{{ $queryMovi->iddependencias_receptor }}">
                                    <div class="modal-body pt-0 pb-0">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="tipo_documento">Tipo documento: <span
                                                                style="color: red">*</span>
                                                        </label>
                                                        <select id="tipo_documento" class="form-control"
                                                            placeholder="Ingrese el asunto" name="tipo_documento"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                            <option value="0"
                                                                {{ $queryDoc->idtipo_documento == 0 ? 'selected' : '' }}>
                                                                Seleccione
                                                                tipo
                                                                documento ...</option>
                                                            <option value="1"
                                                                {{ $queryDoc->idtipo_documento == 1 ? 'selected' : '' }}>
                                                                Fut</option>
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
                                                        <span id="tipo_documento_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                @if ($id_depen == 19)
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Usuario: <span style="color: red">*</span></label>
                                                            <select id="usuario" class="form-control select2" name="usuario"
                                                                style="width: 100%;"
                                                                {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>

                                                                <option value="{{ $usu->idusuario }}" selected>
                                                                    {{ $usu->nombres }}
                                                                </option>
                                                            </select>
                                                            <span id="usuario_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                @endif


                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="folio">Folio: <span style="color: red">*</span></label>
                                                        <input class="form-control" type="number" id="folio" name="folio"
                                                            onkeydown="return bloqueare(event)" value="{{ $queryDoc->folio }}"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>
                                                        <span id="folio_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2" id="num_ex">
                                                    <div class="form-group">
                                                        <label for="num_expe">Núm. expe local:</label>
                                                        <input class="form-control" type="text" id="num_expe"
                                                            name="num_expe" value="{{ $queryDoc->numero_de_exp }}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="dependencia_enviar">Dependencia a enviar: <span
                                                                style="color: red">*</span></label>
                                                        <select id="dependencia_enviar" class="form-control select2"
                                                            placeholder="" name="dependencia_enviar[]"
                                                            {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }} multiple>
                                                            @foreach ($dependencias as $depe)
                                                                @if ($depe->nombre_dependencia != $rol->nombre_dependencia && $depe->nombre_dependencia != 'Administrador Sistema')
                                                                    <option value="{{ $depe->iddependencias }}"
                                                                        {{ in_array($depe->iddependencias, $dependenciasSelect) ? 'selected' : '' }}>
                                                                        {{ $depe->nombre_dependencia }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <span id="dependencia_enviar_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                @if ($id_depen == 2)
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="para_su">Para su: <span
                                                                    style="color: red">*</span></label>
                                                            <select id="para_su" class="form-control"
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
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Asunto <span style="color: red">*</span></label>
                                                        <textarea id="asunto" class="form-control" rows="2" placeholder="Escriba el asunto del documento ..."
                                                            name="asunto" {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>{{ $queryDoc->asunto }}</textarea>
                                                        <span id="asunto_error" class="text-danger"></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Recomendaciones:</label>
                                                        <textarea id="recomendaciones" class="form-control" rows="2" placeholder="Escriba el asunto del documento ..."
                                                            name="Recomendaciones" {{ $estadoMayor->idestado >= 2 ? 'disabled' : '' }}>{{ $queryDoc->recomendacion }}</textarea>
                                                    </div>
                                                </div>
                                                @if ($pdfdocumento->isNotEmpty())
                                                    <div class="card card-info">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                <i class="fas fa-file-pdf"></i> Documentos Adjuntos
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                @foreach ($pdfdocumento as $pdf)
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                                        <span>
                                                                            <i class="fas fa-paperclip text-danger mr-2"></i>
                                                                            {{ $pdf->nombre_del_documento }}
                                                                        </span>
                                                                        <a href="{{ asset('documentos/documentos_director_pdf/' . $pdf->nombre_del_documento) }}"
                                                                            target="_blank"
                                                                            class="btn btn-sm btn-outline-primary">
                                                                            Ver PDF
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
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
    @endcan
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    {{-- <script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script> --}}
    @livewireScripts

    <script>
        const userId = "{{ auth()->id() }}";
        document.addEventListener("DOMContentLoaded", function() {
            const audio = new Audio('{{ asset('sound/alert.mp3') }}');

            Echo.private('App.Models.User.' + userId)
                .listen('.noEditarDocumento', (e) => {
                    audio.play().catch(err => console.log("Audio en espera de interacción"));
                    Swal.fire({
                        title: "ALERTA!",
                        text: "Este documento ya fue recepcionado, no se puede editar ...",
                        icon: "error"
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                });
        });

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

            setTimeout(function() {
                $('#myalert').fadeOut();
            }, 5000);

            $('#tipo_documento').change(traer_num_expe);
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
