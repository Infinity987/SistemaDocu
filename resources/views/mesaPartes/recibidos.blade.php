@extends('adminlte::page')

@section('title', "$rol->nombre_dependencia")

@section('content_header')
    @can('documentario.mesapar.bandeja')
        <div class="semaforo-container">
            <div class="semaforo verde">
                <span id="badgeVerde">{{ $cont_fecha[0]->verde == 0 ? '' : $cont_fecha[0]->verde }}</span>
            </div>
            <p>Documentos con un día</p>

            <div class="semaforo amarillo">
                <span id="badgeAmarillo"><span
                        id="badgeVerde">{{ $cont_fecha[0]->amarillo == 0 ? '' : $cont_fecha[0]->amarillo }}</span></span>
            </div>
            <p>Documentos con dos días</p>

            <div class="semaforo rojo">
                <span id="badgeRojo"><span
                        id="badgeVerde">{{ $cont_fecha[0]->rojo == 0 ? '' : $cont_fecha[0]->rojo }}</span></span>
            </div>
            <p>Documentos con más de dos días</p>
        </div>


        <div class="callout callout-danger">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-sign-in-alt"></i> <i class="fas fa-inbox"></i> - BANDEJA DOCUMENTARIA</h1>
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
    @can('documentario.mesapar.bandeja')
        <div class="container-fluid">
            <div class="row">
                <input type="hidden" id="emisor" name="emisor" value ="{{ $depen }}">
                {{--    TABLE    --}}
                <div class="col-12">
                    <div class="row">
                        <div class="container-fluid mt-2">
                            <div class="card card-success card-outline">
                                <div class="card-header m-0 p-1" style="background-color: #f0f0f0">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="container">
                                                <div class="row justify-content-center">
                                                    <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                                                        <button data-tipo="1" type="button"
                                                            class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                                                                class="fas fa-sign-in-alt"></i>
                                                            Recibidos <span id="badgeNotificaciones1"
                                                                class="badge badge-danger">{{ $cont_est[0]->cont_estado == 0 ? '' : $cont_est[0]->cont_estado }}</span></button>
                                                    </div>
                                                    <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                                                        <button data-tipo="2" type="button"
                                                            class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                                                                class="fas fa-user-clock"></i>
                                                            Pendientes <span id="badgeNotificaciones2"
                                                                class="badge badge-danger">{{ $cont_est[1]->cont_estado == 0 ? '' : $cont_est[1]->cont_estado }}</span></button>
                                                    </div>
                                                    <div class="col-lg-2 col-sm-6 pb-1 pt-1">
                                                        <button data-tipo="3" type="button"
                                                            class="btn btn-block bg-gradient-info btn-sm tipo-btn"><i
                                                                class="fas fa-calendar-check"></i>
                                                            Atendidos <span id="badgeNotificaciones3"
                                                                class="badge badge-danger">{{ $cont_est[2]->cont_estado == 0 ? '' : $cont_est[2]->cont_estado }}</span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="overflow-x: auto;">
                                    <table id="datatablesS" class="table table-hover">
                                        <thead>
                                            <tr style="background-color:#f19e40">
                                                <th style="text-align: center;">Oficina</th>
                                                <th style="text-align: center;">N° Expe.</th>
                                                <th style="text-align: center;">Fecha envio</th>
                                                <th style="text-align: center;">Fecha recep.</th>

                                                <th style="text-align: center;">Asunto</th>
                                                <th style="text-align: center;">Motivo</th>
                                                <th style="text-align: center;">PDF</th>
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

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-align-justify"></i> Formulario para
                        Emitir Documentos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('documentario.enviardocumentos.responder') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="iddocument" value="">
                        <input type="hidden" name="dependencia" value="{{ $rol->iddependencias }}">
                        <input type="hidden" name="firma_x" id="firma_x">
                        <input type="hidden" name="firma_y" id="firma_y">
                        <input type="hidden" name="firmas_json" id="firmas_json">

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="folio">Folio: <span style="color: red">*</span></label>
                                <input class="form-control" type="number" id="folio" name="folio">
                                <span id="folio_error" class="text-danger"></span>
                            </div>
                        </div>

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
                                <label for="nombreproceso">Seleccionar la oficina a destino:</label>
                                <select id="oficina_destino" name="oficina_destino"
                                    class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                    style="width: 100%;">
                                    <option>Seleccione la oficina</option>
                                    @foreach ($dependencias as $oficinas)
                                        <option value="{{ $oficinas->iddependencias }}">
                                            {{ $oficinas->nombre_dependencia }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($id_depen == 2)


                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="tramite_documento">Seleccionar que se hara con el documento:</label>
                                    <select id="tramite_documento" name="tramite_documento"
                                        class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;">
                                        <option>Seleccione la opcion</option>
                                        @foreach ($detalledocumento as $detalle)
                                            <option value="{{ $detalle->iddetalle_tramite }}">
                                                {{ $detalle->nombre_detalle_tramite }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        @endif

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="recomendaciones">RECOMENDACIONES</label>
                                <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="4" required></textarea>
                            </div>
                        </div>

                        @if ($id_depen == 2)
                            <button type="button" id="generarPdfBtn" class="btn btn-info"><i
                                    class="fas fa-file-pdf"></i> Generar PDF</button>
                            <div id="pdfViewer" style="margin-top: 20px;"></div>
                        @endif

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="entregaOpciones">Modo de Entrega</label>
                                <select class="form-control" id="entregaOpciones" name="entregaOpciones">
                                    <option>Seleccione la opcion</option>
                                    <option value="1">Entregar documentos en físico</option>
                                    <option value="2">Entregar documentos virtual</option>
                                    <option value="3">Entregar documentos virtual y fisico</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <!-- Este es el área para subir archivos, estará oculta inicialmente -->
                            <div class="form-group" id="subirArchivo" style="display: none;">
                                <label for="archivo">Subir Archivo</label>
                                <input type="file" class="form-control" id="archivo_virtual" name="archivo_virtual">
                                <canvas id="pdf-canvas" width="600" height="800"
                                    style="border: 1px solid #ccc;"></canvas>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center my-2">
                            <button type="button" id="btn-anterior"
                                class="btn btn-sm btn-secondary mr-2">Anterior</button>
                            <span>Página <span id="pagina-actual">1</span> de <span id="total-paginas">1</span></span>
                            <button type="button" id="btn-siguiente"
                                class="btn btn-sm btn-secondary ml-2">Siguiente</button>
                        </div>


                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="pagina_firma">¿En qué página deseas que aparezca la
                                    firma?</label>
                                <select multiple class="form-control" id="pagina_firma" name="pagina_firma[]">
                                    <!-- Aquí podrías generar dinámicamente las opciones desde JS -->
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>

                            </div>
                        </div>



                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                    class="fas fa-window-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

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
    </style>
    <style>
        .semaforo-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .semaforo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .rojo {
            background-color: red;
        }

        .amarillo {
            background-color: yellow;
            color: black;
        }

        .verde {
            background-color: green;
        }

        .tabla-container {
            border: 1px solid #ccc;
            width: 80%;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
    </style>

    @livewireStyles
@stop


@section('js')

    <script>
        window.dependenciaId = {{ $depen }}; // Esto se usa dentro de app.js
    </script>
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    @livewireScripts

    <script>
        let dependenciaId = {{ $depen }};
        document.addEventListener("DOMContentLoaded", function() {
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
                                .cont_estados[0].cont_estado);
                            $('#badgeNotificaciones1').text(e.cont_estados[0].cont_estado == 0 ?
                                '' : e.cont_estados[0]
                                .cont_estado);
                            $('#badgeNotificaciones2').text(e.cont_estados[1].cont_estado == 0 ?
                                '' : e.cont_estados[1]
                                .cont_estado);
                            $('#badgeNotificaciones3').text(e.cont_estados[2].cont_estado == 0 ?
                                '' : e.cont_estados[2]
                                .cont_estado);
                            // $('#badgeVerde').text(e.cont_fechas[0].verde);
                            // $('#badgeAmarillo').text(e.cont_fechas[0].amarillo);
                            // $('#badgeRojo').text(e.cont_fechas[0].rojo);
                            console.log('recargar datatable ahora peroj jjjjj');

                            $('#datatablesS').DataTable().ajax.reload();
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
                .listen('.editarDocumento', (e) => {
                    $('#badge-alerts').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                    $('#badgeNotificaciones1').text(e.cont_estados[0].cont_estado == 0 ? '' : e.cont_estados[0]
                        .cont_estado);
                    $('#badgeNotificaciones2').text(e.cont_estados[1].cont_estado == 0 ? '' : e.cont_estados[1]
                        .cont_estado);
                    $('#badgeNotificaciones3').text(e.cont_estados[2].cont_estado == 0 ? '' : e.cont_estados[2]
                        .cont_estado);
                    $('#badgeVerde').text(e.cont_fechas[0].verde);
                    $('#badgeAmarillo').text(e.cont_fechas[0].amarillo);
                    $('#badgeRojo').text(e.cont_fechas[0].rojo);
                    $('#datatablesS').DataTable().ajax.reload();
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const entregaOpciones = document.getElementById('entregaOpciones');
            const subirArchivoDiv = document.getElementById('subirArchivo');
            const paginaFirmaInput = document.getElementById('pagina_firma');
            const paginaFirmaDiv = paginaFirmaInput.closest('.form-group');

            const mostrarFirma = entregaOpciones.value === '2' || entregaOpciones.value === '3';

            paginaFirmaDiv.style.display = mostrarFirma ? 'block' : 'none';
            paginaFirmaInput.required = mostrarFirma;

            if (subirArchivoDiv) {
                subirArchivoDiv.style.display = mostrarFirma ? 'block' : 'none';
            }

            entregaOpciones.addEventListener('change', function() {
                const opcionSeleccionada = this.value;
                const mostrarFirma = opcionSeleccionada === '2' || opcionSeleccionada === '3';

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
        let tipoActivo = null;

        $(document).ready(function() {

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
            $('.tipo-btn[data-tipo="1"]').click();
        });

        function cargarTabla(tipo) {
            if ($.fn.DataTable.isDataTable('#datatablesS')) {
                $('#datatablesS').DataTable().clear().destroy(); // destrucción segura
            }

            let ruta_doc_emitidos = "{{ route('documentario.bandeja.bandejaEstado', ['idtipo_estado' => ':id', 'emisor' => ':emi']) }}";
            let urlruta_doc_emitidosEmi = ruta_doc_emitidos.replace(':id', tipo);
            let urlruta_doc_emitidosf = urlruta_doc_emitidosEmi.replace(':emi', $('#emisor').val());

            let tabless = $('#datatablesS').DataTable({
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
                        data: "nom_emiso",
                        name: 'nom_emiso',
                        render: function(data, type, row) {
                            return '<div class=""><p class="fw-semibold text-dark">' +
                                '<i class="fas fa-sign-in-alt"></i> ' + data + '</p></div>';
                        }
                    },
                    {
                        data: 'iddocument',
                        name: 'iddocument',
                        render: function(data, type, row) {
                            return '<div class=" bg-success-subtle text-dark-emphasis"><p class="fw-semibold text-dark" style="text-align: center;">' +
                                data + '</p></div>';
                        }
                    },
                    {
                        data: "fecha_de_envio",
                        name: 'fecha_de_envio',
                        render: function(data, type, row) {

                            return '<p>' + data + '</p>';
                        }
                    },
                    {
                        data: "fecha_de_recepcion",
                        name: 'fecha_de_recepcion',
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                        }
                    },
                    {
                        data: "asunt",
                        name: 'asunt',
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                        }
                    },
                    {
                        data: "nom_deta_trami",
                        name: 'nom_deta_trami',
                        render: function(data, type, row) {
                            return '<p> <i class="fa-solid fa-calendar-days"></i> ' + data + '</p>';
                        }
                    },
                    {
                        data: "nombrepdf",
                        name: "nombrepdf",
                        render: function(data) {
                            if (data) {
                                return `
          <a href="/documentos/documentos_director_pdf/${data}" target="_blank"
             class="btn btn-sm btn-outline-danger" title="Ver PDF">
            <i class="fas fa-file-pdf"></i>
          </a>`;
                            } else {
                                return `<span class="text-muted">-</span>`;
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
                                'max-width': '100px',
                                'white-space': 'normal',
                                'word-wrap': 'break-word'
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
                        width: "8%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    },
                    {
                        targets: 4,
                        width: "50%",
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
                        targets: 5,
                        width: "8%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    },
                    {
                        targets: 6, // si PDF es la 7ª columna (índice base 0)
                        width: "12%",
                        className: "text-center",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    },
                    {
                        targets: 7,
                        width: "10%",
                        createdCell: function(td) {
                            $(td).css('background-color', "#f19e402f");
                        }
                    }
                ],
                responsive: true
            });

            if (tipo == 1) {
                tabless.column(3).visible(false);
                tabless.draw();
            }
        }

        @if (session('error'))
            Swal.fire({
                title: "ERROR!",
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
        $('#formModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // Botón que activó el modal
            const iddocument = button.data('id'); // Obtiene el valor
            const dependencia = button.data('dependencia');
            // Opcional: asignarlo a un campo oculto dentro del modal
            $(this).find('input[name="iddocument"]').val(iddocument);
        });
    </script>
@stop
