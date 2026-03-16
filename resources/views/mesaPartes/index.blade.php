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
                                <form action="{{ route('documentario.registrarDocu') }}" method="post" id="form_regis_doc"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <input type="hidden" id="emisor" name="emisor"
                                                    value ="{{ $id_depen }}">
                                                <input type="hidden" id="id_usuTrabajador" name="id_usuTrabajador"
                                                    value ="{{ $id_usuTrabajador }}">
                                                <input type="hidden" name="firma_x" id="firma_x">
                                                <input type="hidden" name="firma_y" id="firma_y">
                                                <input type="hidden" name="firmas_json" id="firmas_json">

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

                                                @if ($id_depen == 19)
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Usuario: <span style="color: red">*</span></label>
                                                            <select id="usuario" class="form-control select2"
                                                                name="usuario" style="width: 100%;"></select>
                                                            <span id="usuario_error" class="text-danger"></span>
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

                                                @if ($id_depen == 2)
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
                                                @endif



                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Recomendaciones:</label>
                                                        <textarea id="recomendaciones" class="form-control" rows="3" placeholder="Escriba el asunto del documento ..."
                                                            name="Recomendaciones"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="entregaOpciones">Modo de Entrega</label>
                                                        <select class="form-control" id="entregaOpciones"
                                                            name="entregaOpciones">
                                                            <option value="">Seleccione la opcion</option>
                                                            <option value="1">Entregar documentos en físico</option>
                                                            <option value="2">Entregar documentos virtual</option>
                                                            <option value="3">Entregar documentos virtual y fisico
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <!-- Este es el área para subir archivos, estará oculta inicialmente -->
                                                    <div class="form-group" id="subirArchivo" style="display: none;">
                                                        <label for="archivo">Subir Archivo</label>
                                                        <input type="file" class="form-control" id="archivo_virtual"
                                                            name="archivo_virtual">
                                                        <canvas id="pdf-canvas" width="600" height="800"
                                                            style="border: 1px solid #ccc;"></canvas>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center align-items-center my-2">
                                                    <button type="button" id="btn-anterior"
                                                        class="btn btn-sm btn-secondary mr-2">Anterior</button>
                                                    <span>Página <span id="pagina-actual">1</span> de <span
                                                            id="total-paginas">1</span></span>
                                                    <button type="button" id="btn-siguiente"
                                                        class="btn btn-sm btn-secondary ml-2">Siguiente</button>
                                                </div>


                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="pagina_firma">¿En qué página deseas que aparezca la
                                                            firma?</label>
                                                        <select multiple class="form-control" id="pagina_firma"
                                                            name="pagina_firma[]">
                                                            <!-- Aquí podrías generar dinámicamente las opciones desde JS -->
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">{{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>

                                                    </div>
                                                </div>


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
                @if ($id_depen == 19)
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

        document.addEventListener('DOMContentLoaded', function() {
            const entregaOpciones = document.getElementById('entregaOpciones');
            const subirArchivoDiv = document.getElementById('subirArchivo');
            const paginaFirmaInput = document.getElementById('pagina_firma');
            const paginaFirmaDiv = paginaFirmaInput.closest('.form-group');

            const mostrarFirmaInicial = entregaOpciones.value === '2' || entregaOpciones.value === '3';
            paginaFirmaDiv.style.display = mostrarFirmaInicial ? 'block' : 'none';
            paginaFirmaInput.required = mostrarFirmaInicial;

            if (subirArchivoDiv) {
                subirArchivoDiv.style.display = mostrarFirmaInicial ? 'block' : 'none';
            }

            entregaOpciones.addEventListener('change', function() {
                const mostrarFirma = this.value === '2' || this.value === '3';
                paginaFirmaDiv.style.display = mostrarFirma ? 'block' : 'none';
                paginaFirmaInput.required = mostrarFirma;

                if (subirArchivoDiv) {
                    subirArchivoDiv.style.display = mostrarFirma ? 'block' : 'none';
                }
            });

            // ================================
            // Código relacionado al PDF:
            // ================================

            let archivoPDF = null;
            let paginaActual = 1;
            let totalPaginas = 1;
            let coordenadasFirmas = {};

            const archivoInput = document.getElementById('archivo_virtual');
            const canvas = document.getElementById('pdf-canvas');

            archivoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type === 'application/pdf') {
                    const fileReader = new FileReader();

                    fileReader.onload = function() {
                        const typedarray = new Uint8Array(this.result);
                        pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                            archivoPDF = pdf;
                            totalPaginas = pdf.numPages;

                            paginaActual = parseInt(document.getElementById('pagina_firma')
                                .value || 1);
                            document.getElementById('total-paginas').textContent = totalPaginas;
                            document.getElementById('pagina-actual').textContent = paginaActual;

                            renderizarPagina(paginaActual);
                        });
                    };

                    fileReader.readAsArrayBuffer(file);
                }
            });

            document.getElementById('pagina_firma').addEventListener('change', function() {
                const nuevaPagina = parseInt(this.value || 1);
                if (archivoPDF && nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
                    paginaActual = nuevaPagina;
                    document.getElementById('pagina-actual').textContent = paginaActual;
                    renderizarPagina(paginaActual);
                }
            });

            canvas.addEventListener('click', function(e) {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const pagina = paginaActual;
                if (!coordenadasFirmas[pagina]) {
                    coordenadasFirmas[pagina] = [];
                }

                const firmaIndex = coordenadasFirmas[pagina].findIndex(firma => {
                    return x >= firma.x + 105 && x <= firma.x + 120 && y >= firma.y - 5 && y <=
                        firma.y + 10;
                });

                if (firmaIndex !== -1) {
                    coordenadasFirmas[pagina].splice(firmaIndex, 1);
                    renderizarPagina(pagina);
                    return;
                }

                coordenadasFirmas[pagina].push({
                    x,
                    y
                });
                renderizarPagina(pagina);
            });

            function renderizarPagina(pagina) {
                archivoPDF.getPage(pagina).then(page => {
                    const scale = 0.7;
                    const viewport = page.getViewport({
                        scale
                    });

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const context = canvas.getContext('2d');

                    page.render({
                        canvasContext: context,
                        viewport
                    }).promise.then(() => {
                        if (coordenadasFirmas[pagina]) {
                            coordenadasFirmas[pagina].forEach(({
                                x,
                                y
                            }) => {
                                context.fillStyle = 'rgba(255,0,0,0.3)';
                                context.fillRect(x, y, 120, 40);
                                context.strokeStyle = 'red';
                                context.lineWidth = 2;
                                context.strokeRect(x, y, 120, 40);
                                context.fillStyle = 'black';
                                context.font = '12px Arial';
                                context.fillText('Aquí irá la firma', x + 10, y + 25);

                                context.fillStyle = 'white';
                                context.fillRect(x + 105, y - 5, 15, 15);
                                context.fillStyle = 'red';
                                context.fillText('✖', x + 107, y + 7);
                            });
                        }
                    });
                }).catch(err => {
                    console.error('Página inválida:', err);
                });
            }

            document.getElementById('btn-anterior').addEventListener('click', function() {
                if (paginaActual > 1) {
                    paginaActual--;
                    actualizarPagina();
                }
            });

            document.getElementById('btn-siguiente').addEventListener('click', function() {
                if (paginaActual < totalPaginas) {
                    paginaActual++;
                    actualizarPagina();
                }
            });

            function actualizarPagina() {
                document.getElementById('pagina_firma').value = paginaActual;
                document.getElementById('pagina-actual').textContent = paginaActual;
                renderizarPagina(paginaActual);
            }

            // Enviar formulario
            document.getElementById('form_regis_doc').addEventListener('submit', function() {
                document.getElementById('firmas_json').value = JSON.stringify(coordenadasFirmas);
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
@stop
