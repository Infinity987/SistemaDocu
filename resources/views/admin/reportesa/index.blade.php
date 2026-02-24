@extends('adminlte::page')

@section('title', 'Docente curso')

@section('content_header')
    @can('asignarCurso.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fa fa-users" aria-hidden="true"></i> - Reporte Acta de Evaluación</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Inicio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('asignarCurso.index')
        @livewire('Admin.reporte-acta-evaluacion')
    @endcan
@stop

@section('css')
    @livewireStyles

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }

        /* Fondo del menú desplegable */
        .select2-container .select2-dropdown {
            background-color: #ececec;
            /* color más oscuro */
            color: #ffffff;
            /* texto blanco */
            border: 1px solid #444;
        }

        /* Estilo de cada opción */
        .select2-container .select2-results__option {
            padding: 8px 12px;
            font-size: 14px;
            color: #000000;
            background-color: #f3f3f3;
        }

        /* Hover sobre opciones */
        .select2-container .select2-results__option--highlighted {
            background-color: #343a40;
            color: #ffffff;
        }
    </style>
@stop


@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('change', '#semestre_acad', function() {
            $('.error-selecdSemesAca').fadeOut();
        });
    </script>

    <script>
        function initSelect2() {
            $('.select2-docente').each(function() {
                const $select = $(this);
                const cursoId = $select.data('curso-id');
                const selectedValue = $select.data('selected');
                const selectedText = $select.data('selected-text');
                const component = Livewire.find($select.closest('[wire\\:id]').attr('wire:id'));

                // Destruir instancia previa si existe
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    language: {
                        noResults: function() {
                            return "No se encontraron resultados";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    },
                    placeholder: 'Seleccionar docente',
                    width: '100%',
                    ajax: {
                        url: '{{ route('buscarDocente') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                curso_id: cursoId
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results.map(item => ({
                                    id: item.iddocen,
                                    text: item.nombre
                                }))
                            };
                        }
                    }
                });

                // Si hay uno seleccionado, agrégalo manualmente
                if (selectedValue && selectedText) {
                    const newOption = new Option(selectedText, selectedValue, true, true);
                    $select.append(newOption).trigger('change');
                }

                $select.off('change').on('change', function() {
                    const val = $(this).val();
                    if (component) {
                        component.set(`asignaciones.${cursoId}`, val);
                    }
                });
            });
        }

        document.addEventListener('livewire:init', () => {
            // Inicializar Select2 al cargar
            initSelect2();

            // Escuchar el evento personalizado para re-inicializar Select2 tras cambios
            window.addEventListener('cursos-actualizados', () => {
                console.log('📚 Cursos actualizados, reiniciando Select2');
                setTimeout(() => {
                    initSelect2();
                }, 200); // timeout para que el DOM esté listo
            });

            window.addEventListener('verPdf', event => {
                const cursoId = event.detail.cursoId;
                const tipo = event.detail.tipo;

                Livewire.dispatch('iddocente_curso', {
                    cursoId: cursoId,
                    tipo: tipo,
                });
            });

            window.addEventListener('abrirPdf', event => {
                const url = event.detail.url
                window.open(url, '_blank');
            });

            window.addEventListener('mensaje-error_traer_ciclo', event => {
                Swal.fire({
                    title: 'Analisando!',
                    text: event.detail.mensaje,
                    icon: 'info'
                });
            });

            window.addEventListener('mensaje-error', event => {
                Swal.fire({
                    title: 'Faltan docentes',
                    text: event.detail.mensaje,
                    icon: 'warning'
                });
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('mensaje', (event) => {
                Swal.fire({
                    title: "BUEN TRABAJO!",
                    text: event[0].msm,
                    icon: "success"
                });
            });
        });
    </script>
@stop
