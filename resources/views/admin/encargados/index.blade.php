@extends('adminlte::page')

@section('title', 'Encargados')

@section('content_header')
    @can('encargados.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fas fa-user-tie" aria-hidden="true"></i> - ENCARGADOS</h1>
                        </div>
                        <style>
                            @media (max-width: 767px) {
                                #titulo-rol {
                                    font-size: 20px !important;
                                    margin-top: 10px;
                                }
                            }
                        </style>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Usuarios</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('encargados.index')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left shadow">
                        <li class="breadcrumb-item"><a style="color: #097daa; text-decoration: none;"><i
                                    class="fas fa-info-circle"></i> Solo se debe tener un ESTADO activado en la tabla</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="container-fluid mb-2">
            <div class="row">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEncargados">
                        <i class="fa fa-plus" aria-hidden="true"></i> Agregar encargados
                    </button>
                </div>
            </div>
        </div>

        <div class="container-fluid mb-2">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="encargados" class="table table-bordered table-hover table-striped">
                            <thead class="text-white" style="background: linear-gradient(45deg, #a15d18, #543008);">
                                <tr>
                                    <th>#</th>
                                    <th>Director</th>
                                    <th>Resolucion Director</th>
                                    <th>Secretariado</th>
                                    <th>Año</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Encargados -->
        <div class="modal fade" id="modalEncargados" tabindex="-1" role="dialog" aria-labelledby="modalEncargadosLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #b86b1f, #4e2c05);">
                        <h5 class="modal-title" id="modalEncargadosLabel"><i class="fas fa-user-tie mr-1"></i> Asignar
                            Encargados</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('guardar.encargado') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="director">
                                    <i class="fas fa-user-tie text-primary mr-1"></i> Director
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select id="director" class="form-control select2" name="iduserProfile_direc" required>
                                        <option value="">Seleccione un director</option>
                                        @foreach ($docentes as $direc)
                                            <option value="{{ $direc->iduserProfile }}">{{ $direc->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reso_direc">
                                    <i class="fas fa-calendar-alt text-warning mr-1"></i> Resolución Director
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="reso_direc" id="reso_direc"
                                        placeholder="Ej. RDR 9999-2000-DREP" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="secretario">
                                    <i class="fas fa-user-cog text-info mr-1"></i> Secretario Académico
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select id="secretario" class="form-control select2" name="iduserProfile_secre" required>
                                        <option value="">Seleccione un secretario</option>
                                        @foreach ($docentes as $secre)
                                            <option value="{{ $secre->iduserProfile }}">{{ $secre->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="año_logo">
                                    <i class="fas fa-calendar-alt text-warning mr-1"></i> Año del logo
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="año_logo" id="año_logo"
                                        placeholder="Ej. 2025" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="logo">
                                    <i class="fas fa-image text-success mr-1"></i> Logo Institucional
                                    <span class="text-danger">(Formato PNG)</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" id="logo" class="custom-file-input" name="logo"
                                        accept="image/png" onchange="previewLogo(event)" required>
                                    <label class="custom-file-label" for="logo">Seleccionar archivo</label>
                                </div>
                                <div id="previewContainer" class="mt-3 d-none text-center">
                                    <img id="previewImage" src="" alt="Previsualización del logo"
                                        class="img-thumbnail mx-auto d-block" style="max-height: 120px;">
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </button>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal editar Encargados -->
        <div class="modal fade" id="modalEncargados_edit" tabindex="-1" role="dialog"
            aria-labelledby="modalEncargadosLabel_edit" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #b16214, #5a3307);">
                        <h5 class="modal-title" id="modalEncargadosLabel_edit"><i class="fas fa-pencil-alt"></i> Editar
                            Encargados</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('editarEncargado') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="idencargado_edit" id="idencargado_edit">
                        <input type="hidden" name="logo_edit_antigu" id="logo_edit_antigu">
                        <input type="hidden" name="año_edit_antigu" id="año_edit_antigu">


                        <div class="modal-body">
                            <div class="form-group">
                                <label for="director">
                                    <i class="fas fa-user-tie text-primary mr-1"></i> Director
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select id="director_edit" class="form-control select2" name="iduserProfile_direc_edit"
                                        required>
                                        <option value="">Seleccione un director</option>
                                        @foreach ($docentes as $direc)
                                            <option value="{{ $direc->iduserProfile }}">{{ $direc->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reso_direc_edit">
                                    <i class="fas fa-calendar-alt text-warning mr-1"></i> Resolución Director
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="reso_direc_edit" id="reso_direc_edit"
                                        placeholder="Ej. RDR 9999-2000-DREP" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="secretario">
                                    <i class="fas fa-user-cog text-info mr-1"></i> Secretario Académico
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select id="secretario_edit" class="form-control select2" name="iduserProfile_secre_edit"
                                        required>
                                        <option value="">Seleccione un secretario</option>
                                        @foreach ($docentes as $secre)
                                            <option value="{{ $secre->iduserProfile }}">{{ $secre->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="año_logo">
                                    <i class="fas fa-calendar-alt text-warning mr-1"></i> Año del logo
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="año_logo_edit" id="año_logo_edit"
                                        placeholder="Ej. 2025" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="logo">
                                    <i class="fas fa-image text-success mr-1"></i> Logo Institucional
                                    <span class="text-danger">(Formato PNG)</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" id="logo_edit" class="custom-file-input" name="logo_edit"
                                        accept="image/png" onchange="previewLogo_edit(event)">
                                    <label class="custom-file-label" for="logo">Seleccionar archivo</label>
                                </div>
                                <div id="previewContainer_edit" class="mt-3 d-none text-center">
                                    <img id="previewImage_edit" src="" alt="Previsualización del logo"
                                        class="img-thumbnail mx-auto d-block" style="max-height: 120px;">
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </button>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Actualizar
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    <script>
        function previewLogo(event) {
            const input = event.target;
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('previewImage');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                previewImage.src = '';
                previewContainer.classList.add('d-none');
            }
        }

        function previewLogo_edit(event) {
            const input = event.target;
            const previewContainer = document.getElementById('previewContainer_edit');
            const previewImage = document.getElementById('previewImage_edit');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                previewImage.src = '';
                previewContainer.classList.add('d-none');
            }
        }

        function cambiarEstado(id, nuevoEstado) {
            const estado = nuevoEstado ? 1 : 0;
            const baseUrlEstado = "{{ route('ajaxactualizarEstado', ['estado' => '__ESTADO__']) }}";
            const url = baseUrlEstado.replace('__ESTADO__', nuevoEstado);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.mensaje || 'El estado fue cambiado correctamente',
                        // timer: 1500,
                        showConfirmButton: true
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo actualizar el estado'
                    });
                }
            });
        }

        function abrirModalEditar(id) {
            const baseUrlEstado = "{{ route('gedDataEncargado', ['id' => ':id']) }}";
            const urlget = baseUrlEstado.replace(':id', id);

            $.ajax({
                url: urlget,
                method: 'GET',
                success: function(data) {

                    $('#idencargado_edit').val(data.idencargados);
                    $('#director_edit').val(data.iduserProfile_direc).trigger('change');
                    $('#reso_direc_edit').val(data.reso_direc);
                    $('#secretario_edit').val(data.iduserProfile_secre).trigger('change');

                    $('#año_logo_edit').val(data.año_logo);
                    $('#logo_edit_antigu').val(data.logo);
                    $('#año_edit_antigu').val(data.año_logo);


                    if (data.url) {
                        $('#previewImage_edit').attr('src', data.url).removeClass('d-none');
                        $('#previewContainer_edit').removeClass('d-none');
                    } else {
                        $('#previewContainer_edit').addClass('d-none');
                    }

                    // Mostrar el modal
                    $('#modalEncargados_edit').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del encargado.'
                    });
                }
            });
        }

        function eliminarEncargado(id) {
            Swal.fire({
                title: '¿Eliminar encargados?',
                text: 'Esta acción eliminará también el logo asociado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const baseUrlEstadoeli = "{{ route('eliminarEncargados', ['id' => ':id']) }}";
                    const urleli = baseUrlEstadoeli.replace(':id', id);

                    const btn = $(`#btnEliminar_${id}`);
                    const originalContent = btn.html();

                    // Mostrar spinner
                    btn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );
                    $.ajax({
                        url: urleli,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.mensaje || 'Encargado eliminado correctamente',
                                // timer: 1500,
                                showConfirmButton: true
                            });
                            $('#encargados').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar el encargado.'
                            });
                        },
                        complete: function() {
                            // Restaurar contenido original del botón
                            btn.html(originalContent);
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#encargados').DataTable({
                processing: true,
                serverSide: true, // Yajra trabaja mejor con serverSide activado
                ajax: '{{ route('ajaxEncargados') }}',
                columns: [{
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                        render: function() {
                            return `<i class="fas fa-arrow-circle-right"></i>`;
                        }
                    }, {
                        data: 'direc',
                        name: 'direc'
                    }, {
                        data: 'reso_direc',
                        name: 'reso_direc'
                    },
                    {
                        data: 'secre',
                        name: 'secre'
                    },
                    {
                        data: 'año_logo',
                        name: 'año_logo',
                        className: 'text-center'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = data === 1 || data === true ? 'checked' : '';
                            const idSwitch = `switchEstado_${row.idencargados}`;

                            return `
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" id="${idSwitch}" ${checked}
                                onchange="cambiarEstado(${row.idencargados}, this.checked)">
                                <label class="custom-control-label" for="${idSwitch}"></label>
                            </div>
                            `;
                        }
                    },


                    {
                        data: 'idencargados',
                        name: 'acciones',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(id) {
                            return `
                            <button title="Editar" class="btn btn-sm btn-warning" onclick="abrirModalEditar(${id})">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button id="btnEliminar_${id}" title="Eliminar" class="btn btn-sm btn-danger" onclick="eliminarEncargado(${id})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            `;
                        }
                    }
                ],
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna de manera ascendente",
                        sortDescending: ": activar para ordenar la columna de manera descendente"
                    }
                },

                responsive: true,
            });
        });
    </script>

    @if (session('alert'))
        <script>
            Swal.fire({
                icon: '{{ session('alert.icon') }}',
                title: '{{ session('alert.title') }}',
                text: '{{ session('alert.text') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    <script>
        $('#año_logo').on('keypress', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
            }
        });
    </script>
@stop
