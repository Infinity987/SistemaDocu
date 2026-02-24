@extends('adminlte::page')

@section('title', 'Horario')

@section('content_header')
    @can('semestre.index')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-layer-group"></i> - Semestre Académico</h1>
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
    @can('semestre.index')
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left shadow">
                        <li class="breadcrumb-item"><a style="color: #097daa; text-decoration: none;"><i
                                    class="fas fa-info-circle"></i> Solo se debe tener activado 1 semestre académico que esta en
                                la columna (Est. SEMESTRE ACADEMICO)</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="container-fluid pl-0">
            <div class="row mb-3">
                <div class="col-sm-6 ">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#miModal">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>

            <!-- Modal agregar-->
            <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:rgb(0, 119, 162); color: white">
                            <h5 class="modal-title" id="miModalTitle"><i class="fas fa-plus"></i> Agregar
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="formModal" action="{{ route('savesemestreac') }}" method="post">
                                @csrf
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            {{-- semestre aca --}}
                                            <div class="card card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Semestre
                                                        Académico</h3>

                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body" style="background-color: rgb(225, 238, 250);">
                                                    <div class="chart">
                                                        <div class="form-group row">
                                                            <label for="anio" class="col-sm-2 col-form-label">Año:</label>
                                                            <div class="col-sm-3 mb-3">
                                                                <input type="text" id="anio" name="anio"
                                                                    pattern="\d{4}" maxlength="4"
                                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                                    class="form-control" required placeholder="ingrese Año">
                                                                <small class="text-danger" id="error-anio"></small>
                                                            </div>
                                                            <label for="periodo" class="col-sm-2 col-form-label">Tipo:</label>
                                                            <div class="col-sm-5 mb-3">
                                                                <select class="form-control" name="periodo" id="periodo"
                                                                    required>
                                                                    <option value="">Seleccione</option>
                                                                    <option value="I">I</option>
                                                                    <option value="II">II</option>
                                                                    <option value="Extraordinario">Extraordinario</option>
                                                                </select>
                                                                <small class="text-danger" id="error-periodo"></small>
                                                            </div>

                                                            <label for="inicio" class="col-sm-4 col-form-label"><i
                                                                    class="fas fa-hourglass-start"></i>
                                                                Fecha de Inicio:</label>
                                                            <div class="col-sm-8 mb-3">
                                                                <input type="date" id="inicio" name="inicio"
                                                                    class="form-control" required>
                                                                <small class="text-danger" id="error-inicio"></small>
                                                            </div>
                                                            <label for="fin" class="col-sm-4 col-form-label"><i
                                                                    class="fas fa-hourglass-end"></i>
                                                                Fecha de cierre:</label>
                                                            <div class="col-sm-8">
                                                                <input type="date" id="fin" name="fin"
                                                                    class="form-control" required>
                                                                <small class="text-danger" id="error-fin"></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /.card-body -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        {{-- matricu --}}
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-user-check"></i> Matrícula</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(234, 247, 231);">
                                                <div class="chart">
                                                    <div class="form-group row">
                                                        <label for="iniciom" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-start"></i>
                                                            Fecha de Inicio:</label>
                                                        <div class="col-sm-6 mb-3">
                                                            <input type="date" id="iniciom" name="iniciom"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-iniciom"></small>
                                                        </div>
                                                        <label for="finm" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-end"></i>
                                                            Fecha límite:</label>
                                                        <div class="col-sm-6">
                                                            <input type="date" id="finm" name="finm"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-finm"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        {{-- ASISTEN --}}
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-calendar-check"></i> Asistencia</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(236, 247, 231);">
                                                <div class="chart">
                                                    <div class="form-group row">
                                                        <label for="fech_inicio_asis" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-start"></i>
                                                            Fecha de Inicio:</label>
                                                        <div class="col-sm-6 mb-3">
                                                            <input type="date" id="fech_inicio_asis"
                                                                name="fech_inicio_asis" class="form-control">
                                                            <small class="text-danger" id="error-fech_inicio_asis"></small>
                                                        </div>
                                                        <label for="fech_fin_asis" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-end"></i>
                                                            Fecha límite:</label>
                                                        <div class="col-sm-6">
                                                            <input type="date" id="fech_fin_asis" name="fech_fin_asis"
                                                                class="form-control">
                                                            <small class="text-danger" id="error-fech_fin_asis"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>
                                Cerrar</button>
                            <button type="button" class="btn btn-success" id="btnEnviar"><i class="fas fa-save"></i>
                                Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="callout callout-success card-success card-outline pt-0 pl-0 pr-0">
                        <div class="card-header" style="background-color: rgb(187, 237, 189)">
                            <h3 class="card-title"><i class="fas fa-object-group"></i> Historial semestre académico</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="myTable" class="display">
                                    <thead>
                                        <tr>
                                            <th colspan="3">

                                            </th>
                                            <th colspan="3" class="text-center"
                                                style="background-color: rgba(40, 167, 69, 0.15)">
                                                SEMESTRE ACADEMICO
                                            </th>
                                            <th colspan="3" class="text-center"
                                                style="background-color: rgba(253, 126, 20, 0.15)">
                                                MATRICULA
                                            </th>
                                            <th colspan="2" class="text-center"
                                                style="background-color: rgba(15, 133, 184, 0.293)">
                                                ASISTENCIA
                                            </th>
                                            <th colspan="1">

                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background-color: rgba(17, 103, 178, 0.15)">#</th>
                                            <th class="text-center" style="background-color: rgba(17, 103, 178, 0.15)">Año
                                            </th>
                                            <th class="text-center" style="background-color: rgba(17, 103, 178, 0.15)">Periodo
                                            </th>
                                            <th class="text-center" style="background-color: rgba(40, 167, 69, 0.15)">
                                                Inicio</th>
                                            <th class="text-center" style="background-color: rgba(40, 167, 69, 0.15)">
                                                Fin</th>
                                            <th class="text-center" style="background-color: rgba(40, 167, 69, 0.15)">Est.
                                                </th>
                                            <th class="text-center" style="background-color: rgba(253, 126, 20, 0.15)">
                                                Inicio</th>
                                            <th class="text-center" style="background-color: rgba(253, 126, 20, 0.15)">
                                                Fin</th>
                                            <th class="text-center" style="background-color: rgba(253, 126, 20, 0.15)">Est.
                                                </th>

                                            <th class="text-center" style="background-color: rgba(15, 133, 184, 0.293)">
                                                Inicio</th>
                                            <th class="text-center" style="background-color: rgba(15, 133, 184, 0.293)">
                                                Fin</th>

                                            <th class="text-center" style="background-color: rgba(17, 103, 178, 0.15)">
                                                Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal editar-->
        <div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:rgb(162, 0, 100); color: white">
                        <h5 class="modal-title" id="editarModalTitle"><i class="far fa-object-group"></i> <i
                                class="fas fa-edit"></i> Editar Semestre académico
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formModalEdit" action="{{ route('actualizarSemes') }}" method="post">
                            @csrf
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {{-- semestre aca --}}
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Semestre Académico
                                                </h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(225, 238, 250);">
                                                <div class="chart">
                                                    <input type="hidden" name="var_ididsemestre_academico"
                                                        id="var_ididsemestre_academico">
                                                    <div class="form-group row">
                                                        <label for="anio2" class="col-sm-2 col-form-label">Año:</label>
                                                        <div class="col-sm-3 mb-3">
                                                            <input type="text" id="anio2" name="anio2"
                                                                pattern="\d{4}" maxlength="4"
                                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-anio2"></small>
                                                        </div>
                                                        <label for="periodo2" class="col-sm-2 col-form-label">Tipo:</label>
                                                        <div class="col-sm-5 mb-3">
                                                            <select class="form-control" name="periodo2" id="periodo2"
                                                                required>
                                                                <option value="">Seleccione</option>
                                                                <option value="I">I</option>
                                                                <option value="II">II</option>
                                                                <option value="Extraordinario">Extraordinario</option>
                                                            </select>
                                                            <small class="text-danger" id="error-periodo2"></small>
                                                        </div>

                                                        <label for="inicio2" class="col-sm-4 col-form-label"><i
                                                                class="fas fa-hourglass-start"></i>
                                                            Fecha de Inicio:</label>
                                                        <div class="col-sm-8 mb-3">
                                                            <input type="date" id="inicio2" name="inicio2"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-inicio2"></small>
                                                        </div>
                                                        <label for="fin2" class="col-sm-4 col-form-label"><i
                                                                class="fas fa-hourglass-end"></i>
                                                            Fecha de cierre:</label>
                                                        <div class="col-sm-8">
                                                            <input type="date" id="fin2" name="fin2"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-fin2"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        {{-- matricu --}}
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-user-check"></i> Matrícula</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(234, 247, 231);">
                                                <div class="chart">
                                                    <div class="form-group row">
                                                        <label for="iniciom2" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-start"></i>
                                                            Fecha de Inicio:</label>
                                                        <div class="col-sm-6 mb-3">
                                                            <input type="date" id="iniciom2" name="iniciom2"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-iniciom2"></small>
                                                        </div>
                                                        <label for="finm2" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-end"></i>
                                                            Fecha límite:</label>
                                                        <div class="col-sm-6">
                                                            <input type="date" id="finm2" name="finm2"
                                                                class="form-control" required>
                                                            <small class="text-danger" id="error-finm2"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        {{-- ASISTEN --}}
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-calendar-check"></i> Asistencia</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(236, 247, 231);">
                                                <div class="chart">
                                                    <div class="form-group row">
                                                        <label for="fech_inicio_asise" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-start"></i>
                                                            Fecha de Inicio:</label>
                                                        <div class="col-sm-6 mb-3">
                                                            <input type="date" id="fech_inicio_asise"
                                                                name="fech_inicio_asise" class="form-control">
                                                            <small class="text-danger" id="error-fech_inicio_asise"></small>
                                                        </div>
                                                        <label for="fech_fin_asise" class="col-sm-6 col-form-label"><i
                                                                class="fas fa-hourglass-end"></i>
                                                            Fecha límite:</label>
                                                        <div class="col-sm-6">
                                                            <input type="date" id="fech_fin_asise" name="fech_fin_asise"
                                                                class="form-control">
                                                            <small class="text-danger" id="error-fech_fin_asise"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>
                            Cerrar</button>
                        <button type="button" class="btn btn-success" id="btnEnviarEdit"><i class="fas fa-save"></i>
                            Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@stop

@section('css')
    @livewireStyles

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />

@stop
@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            //compara las fechas de semes y matricu
            $('#fin').on('blur', function() {
                let inicio = $('#inicio').val();
                let fin = $(this).val();

                if (fin && inicio) {
                    if (inicio > fin) {
                        $('#error-fin').text(
                            'La fecha de cierre debe ser mayor o igual que la fecha inicial.');
                        $(this).val('');
                    } else {
                        $('#error-fin').text('');
                    }
                }
            });

            $('#inicio').on('blur', function() {
                $('#fin').trigger('blur');
            });

            $('#finm').on('blur', function() {
                let iniciom = $('#iniciom').val();
                let finm = $(this).val();

                if (finm && iniciom) {
                    if (iniciom > finm) {
                        $('#error-finm').text(
                            'La fecha límite debe ser mayor o igual que la fecha inicial.');
                        $(this).val('');
                    } else {
                        $('#error-finm').text('');
                    }
                }
            });

            $('#iniciom').on('blur', function() {
                $('#finm').trigger('blur');
            });


            //la tabla de los semes y matri
            let tablaSemes = $('#myTable').DataTable({
                // processing: true,
                // serverSide: true,
                ordering: true,
                language: {
                    decimal: ",",
                    thousands: ".",
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles en la tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                },
                ajax: '{{ route('listsemes') }}',
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<span>#</span>';
                        },
                    },
                    {
                        data: 'año',
                        name: 'año'
                    },
                    {
                        data: 'periodo',
                        name: 'periodo',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'fecha_inicio',
                        name: 'fecha_inicio',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(40,167,69,0.3)",
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'fecha_fin',
                        name: 'fecha_fin',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(40,167,69,0.3)",
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let checked = row.estado ? 'checked' : '';
                            let id = `switchEstado-${row.idsemestre_academico}`;
                            return `
                                <div class="form-group mb-0">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                            class="custom-control-input toggle-estado"
                                            id="${id}"
                                            data-id="${row.idsemestre_academico}"
                                            ${checked}>
                                        <label class="custom-control-label" for="${id}"></label>
                                    </div>
                                </div>`;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(40, 167, 69, 0.15)",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'fecha_iniciom',
                        name: 'fecha_iniciom',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(253, 126, 20, 0.3)", // naranja (warning) con 30% opacidad
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'fecha_finm',
                        name: 'fecha_finm',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(253, 126, 20, 0.3)", // naranja (warning) con 30% opacidad
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let checked = row.estadom ? 'checked' : '';
                            let id = `switchEstadom-${row.idsemestre_academico}`;
                            return `
                                <div class="form-group mb-0">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                            class="custom-control-input mtoggle-estado"
                                            id="${id}"
                                            data-id="${row.idsemestre_academico}"
                                            ${checked}>
                                        <label class="custom-control-label" for="${id}"></label>
                                    </div>
                                </div>`;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(253, 126, 20, 0.15)", // verde success transparente
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'fech_inicio_asis',
                        name: 'fech_inicio_asis',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(15, 133, 184, 0.293)",
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'fech_fin_asis',
                        name: 'fech_fin_asis',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                "background-color": "rgba(15, 133, 184, 0.293)",
                                "color": "black",
                                "text-align": "center"
                            });
                        }
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            //ACTIVAR O DESACTIVAR semestre acade
            $(document).on('change', '.toggle-estado', function() {
                let id = $(this).data('id');
                let estado = $(this).is(':checked') ? 1 : 0;
                let url = '{{ route('actuEstado', ['id' => ':id']) }}'.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        estado: estado,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: response.icon,
                            title: response.msm,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: response.icon,
                            title: response.msm,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            //ACTIVAR O DESACTIVAR Matricula
            $(document).on('change', '.mtoggle-estado', function() {
                let id = $(this).data('id');
                let estadom = $(this).is(':checked') ? 1 : 0;
                let url = '{{ route('actuEstadomatricu', ['id' => ':id']) }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        estadom: estadom,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: response.icon,
                            title: response.msm,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: response.icon,
                            title: response.msm,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            //para el formulario agrega
            $('input[name="anio"]').on('input', function() {
                $('#error-anio').text('');
            });

            $('select[name="periodo"]').on('change', function() {
                $('#error-periodo').text('');
            });

            $('input[name="inicio"]').on('input', function() {
                $('#error-inicio').text('');
            });

            $('input[name="fin"]').on('input', function() {
                $('#error-fin').text('');
            });

            $('input[name="iniciom"]').on('input', function() {
                $('#error-iniciom').text('');
            })

            $('input[name="finm"]').on('input', function() {
                $('#error-finm').text('');
            })

            $('#btnEnviar').click(function(e) {
                e.preventDefault();

                let form = $('#formModal');
                let url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {

                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.msm,
                            showConfirmButton: true,
                            timer: 7000
                        });
                        $('#formModal')[0].reset();
                        $('#miModal').modal('hide');
                        tablaSemes.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.anio) {
                                $('#error-anio').text(errors.anio[0]);
                            }
                            if (errors.periodo) {
                                $('#error-periodo').text(errors.periodo[0]);
                            }
                            if (errors.inicio) {
                                $('#error-inicio').text(errors.inicio[0]);
                            }
                            if (errors.fin) {
                                $('#error-fin').text(errors.fin[0]);
                            }
                            if (errors.iniciom) {
                                $('#error-iniciom').text(errors.iniciom[0]);
                            }
                            if (errors.finm) {
                                $('#error-finm').text(errors.finm[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: response.icon,
                                text: response.msm,
                                title: response.title,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    }
                });
            });

            //eliminar
            $(document).on('click', '.btn-eliminar', function(e) {
                e.preventDefault();

                let idsemestre_academico = $(this).data('idsemestre_academico');
                let url = $(this).data('url');
                let token = $('meta[name="csrf-token"]').attr('content');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: token
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: response.icon,
                                    title: response.title,
                                    text: response.msm,
                                    showConfirmButton: true
                                });
                                $('#myTable').DataTable().ajax.reload(null, false);
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: response.icon,
                                    title: response.title,
                                    text: response.msm,
                                    showConfirmButton: true
                                });
                            }
                        })
                    }
                });
            });

            //editar
            $(document).on('click', '.btn-editar', function() {
                let idsemestre_academico = $(this).data('idsemestre_academico');
                let url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#error-fin2').text('');
                        $('#var_ididsemestre_academico').val(data.verEditSe[0]
                            .idsemestre_academico);
                        $('#editarModal input[name="anio2"]').val(data.verEditSe[0].año);
                        $('#editarModal select[name="periodo2"]').val(data.verEditSe[0]
                            .periodo);
                        $('#editarModal input[name="inicio2"]').val(data.verEditSe[0]
                            .fecha_inicio);
                        $('#editarModal input[name="fin2"]').val(data.verEditSe[0].fecha_fin);
                        $('#editarModal input[name="iniciom2"]').val(data.verEditSe[0]
                            .fecha_ini_matricula);
                        $('#editarModal input[name="finm2"]').val(data.verEditSe[0]
                            .fecha_fin_matricula);

                        $('#editarModal input[name="fech_inicio_asise"]').val(data.verEditSe[0]
                            .fech_inicio_asis);
                        $('#editarModal input[name="fech_fin_asise"]').val(data.verEditSe[0]
                            .fech_fin_asis);

                        $('#editarModal').modal('show');
                    }
                });


            });

            $('#fin2').on('blur', function() {
                let inicio2 = $('#inicio2').val();
                let fin2 = $(this).val();

                if (fin2 && inicio2) {
                    if (inicio2 > fin2) {
                        $('#error-fin2').text(
                            'La fecha de cierre debe ser mayor o igual que la fecha inicial.');
                        $(this).val('');
                    } else {
                        $('#error-fin2').text('');
                    }
                }
            });

            $('#inicio2').on('blur', function() {
                $('#fin').trigger('blur');
            });

            $('#finm2').on('blur', function() {
                let iniciom2 = $('#iniciom2').val();
                let finm2 = $(this).val();

                if (finm2 && iniciom2) {
                    if (iniciom2 > finm2) {
                        $('#error-finm2').text(
                            'La fecha límite debe ser mayor o igual que la fecha inicial.');
                        $(this).val('');
                    } else {
                        $('#error-finm2').text('');
                    }
                }
            });

            $('#iniciom2').on('blur', function() {
                $('#finm2').trigger('blur');
            });

            //para el formulario editar
            $('input[name="anio2"]').on('input', function() {
                $('#error-anio2').text('');
            });

            $('select[name="periodo2"]').on('change', function() {
                $('#error-periodo2').text('');
            });

            $('input[name="inicio2"]').on('input', function() {
                $('#error-inicio2').text('');
            });

            $('input[name="fin2"]').on('input', function() {
                $('#error-fin2').text('');
            });

            $('input[name="iniciom2"]').on('input', function() {
                $('#error-iniciom2').text('');
            })

            $('input[name="finm2"]').on('input', function() {
                $('#error-finm2').text('');
            })

            $('#btnEnviarEdit').click(function(e) {
                e.preventDefault();

                let form = $('#formModalEdit');
                let url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: response.msm,
                            showConfirmButton: true,
                        });
                        $('#formModal')[0].reset();
                        $('#editarModal').modal('hide');
                        tablaSemes.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.anio2) {
                                $('#error-anio2').text(errors.anio2[0]);
                            }
                            if (errors.periodo2) {
                                $('#error-periodo2').text(errors.periodo2[0]);
                            }
                            if (errors.inicio2) {
                                $('#error-inicio2').text(errors.inicio2[0]);
                            }
                            if (errors.fin2) {
                                $('#error-fin2').text(errors.fin2[0]);
                            }
                            if (errors.iniciom2) {
                                $('#error-iniciom2').text(errors.iniciom2[0]);
                            }
                            if (errors.finm2) {
                                $('#error-finm2').text(errors.finm2[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.msm,
                                showConfirmButton: true,
                            });
                        }
                    }
                });
            });
        });
    </script>
@stop
