@extends('adminlte::page')

@section('title', 'Editar postulante')

@section('content_header')
    <div class="callout callout-danger mb-0 estiTitulo">
        <section class="content-header p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-edit"></i> Editar Datos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @can('admin.verpostulantes')
                                <li class="breadcrumb-item"><a style="color: #4a3911; text-decoration: none;" class="mause"
                                        href="{{ route('admin.verpostulantes') }}">Ver
                                        @can('admin.verpostulantes')
                                            POSTULANTES
                                        @endcan

                                        @can('admin.users.index')
                                            ESTUDIANTES
                                        @endcan
                                    </a></li>
                            @endcan
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>
@stop

@section('content')
    @can('verDetalle.postulante')
        @if (count($datospostuss) > 0)
            <div class="row m-4">
                <!-- Botón Editar Contraseña -->
                <div class="col-md-2 mb-2">
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#exampleModalp">
                        <i class="fas fa-key"></i> Editar Contraseña
                    </button>
                </div>
            </div>

            <!-- Modal para editar contraseña -->
            <div class="modal fade" id="exampleModalp" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #924900, #d49d5e);">
                            <h5 class="modal-title" id="modalLabel">
                                <i class="fas fa-key"></i> Editar Contraseña
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @livewire('update-password', ['id' => $idusu->id])
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($datospostuss as $datospost)
                {{-- @can('admin.home')
                    <div class="row">
                        <!-- Botón Editar Contraseña -->
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#exampleModalp">
                                <i class="fas fa-key"></i> Editar Contraseña fff
                            </button>
                        </div>
                    </div>

                    <!-- Modal para editar contraseña -->
                    <div class="modal fade" id="exampleModalp" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background: linear-gradient(135deg, #924900, #d49d5e);">
                                    <h5 class="modal-title" id="modalLabel">
                                        <i class="fas fa-key"></i> Editar Contraseña
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @livewire('update-password', ['id' => $idusu->id])
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan --}}
                <form action="{{ route('postulante.update') }}" method="post" id="formPostulante" enctype="multipart/form-data"
                    style="position: relative;">
                    <div id="form-loader" class="d-flex justify-content-start align-items-start"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 10; display: none;">
                        <div class="text-center w-100">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2" style="font-size: 16px; font-weight: bold;">Cargando formulario...</p>
                        </div>
                    </div>
                    @csrf
                    @method('PUT')
                    <div class="container">
                        <div class="row">
                            <input type="hidden" id="dni" name="dni" value="{{ $idpostulante }}">
                            <div class="col-sm-4 mb-2">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-layer-group"></i> Tipo Documento
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="tipodocumento" name="tipodocumento" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="1" {{ $datospost->tipodocumento == 1 ? 'Selected' : '' }}>DNI
                                        </option>
                                        <option value="2" {{ $datospost->tipodocumento == 2 ? 'Selected' : '' }}>CARNET DE
                                            EXTRANJERÍA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4 mb-3" style="background: linear-gradient(135deg, #d6c8c8, #fcfbfa); ">
                                <label for="edad" class="form-label" style="color: #91560d"><i class="fas fa-id-card"></i>
                                    N° de documento:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="number" class="form-control" id="dni_a" name="dni_a"
                                    value="{{ $datospost->idpostulante }}" autocomplete="off" min="1"
                                    oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="10" required>
                            </div>

                            <div class="col-sm-4 mb-2">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-user"></i>
                                        Nacionalidad
                                        <span style="color: red">*</span>
                                    </label>
                                    <input type="text" list="lista-nacionalidad" class="form-control" id="nacionalidad"
                                        name="nacionalidad" autocomplete="off" value="{{ $datospost->nacionalidad }}"
                                        required>

                                    <datalist id="lista-nacionalidad">
                                        <option value="Peruana">
                                        <option value="Colombiana">
                                        <option value="Venezolana">
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 mb-2">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-venus-mars"></i> Sexo
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="genero_postu" name="genero_postu" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="1" {{ $datospost->genero_postulante == 1 ? 'Selected' : '' }}>
                                            MASCULINO
                                        </option>
                                        <option value="2" {{ $datospost->genero_postulante == 2 ? 'Selected' : '' }}>
                                            FEMENINO
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2 mb-3">
                                <label for="edad" class="form-label" style="color: #91560d"><i
                                        class="fas fa-sort-numeric-up"></i>
                                    Edad:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="number" class="form-control" id="edad" name="edad"
                                    value="{{ $datospost->edad_postulante }}" autocomplete="off" min="1"
                                    oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="3" required>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="edad" class="form-label" style="color: #91560d"><i class="fas fa-users"></i>
                                    Identidad Etnica
                                    <span style="color: red">*</span>
                                </label>
                                <select id="identidadetnica" name="identidadetnica"
                                    class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                    required>
                                    @foreach ($idenEtnicas as $idenEtnica)
                                        <option value="{{ $idenEtnica->id }}"
                                            {{ $idenEtnica->id == $datospost->id_identidad_etnica ? 'selected' : '' }}>
                                            {{ $idenEtnica->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <label for="apellido_paterno" class="form-label" style="color: #91560d"><i
                                        class="fas fa-sort-alpha-up"></i>
                                    Apellido Paterno:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                                    value="{{ $datospost->apellidos_pater_postulante }}" autocomplete="off" required>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label style="color: #91560d" for="apellido_materno" class="form-label"><i
                                        class="fas fa-sort-alpha-up"></i>
                                    Apellido Materno:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                    value="{{ $datospost->apellidos_mater_postulante }}" autocomplete="off" required>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label style="color: #91560d" for="nombres" class="form-label"><i
                                        class="fas fa-sort-alpha-up"></i>
                                    Nombres:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombres" name="nombres"
                                    value="{{ $datospost->nombres_postulante }}" autocomplete="off" required>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-3 mb-2">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fab fa-usps"></i>
                                        Estado Civil
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="est_civil" name="est_civil" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        @foreach ($est_civil as $est_civi)
                                            <option value="{{ $est_civi->id }}"
                                                {{ $est_civi->id == $datospost->id_est_civil ? 'selected' : '' }}>
                                                {{ $est_civi->est_civil }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-baby"></i>
                                        ¿Con hijos?
                                    </label>
                                    <select id="con_hijos" name="con_hijos" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="0" {{ $datospost->con_hijos == 0 ? 'selected' : '' }}>NO</option>
                                        <option value="1" {{ $datospost->con_hijos == 1 ? 'selected' : '' }}>SI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 mb-3 d-none" id="additionalField4">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                        ¿Cuantos hijos?
                                    </label>
                                    <input type="number" min="1"
                                        oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="3"
                                        class="form-control" id="cant_hijos" name="cant_hijos"
                                        value="{{ $datospost->cant_hijos }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-2 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-plus"></i>
                                        Beneficiario REBRED <span style="color: red">*</span>
                                    </label>
                                    <select id="rebred" name="rebred" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="0" {{ $datospost->rebred == 0 ? 'selected' : '' }}>NO</option>
                                        <option value="1" {{ $datospost->rebred == 1 ? 'selected' : '' }}>SI</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-3 d-none" id="additionalField3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                        N° Resolucion REBRED
                                    </label>
                                    <input type="text" class="form-control" id="num_reso_rebred" name="num_reso_rebred"
                                        value="{{ $datospost->num_reso_rebred }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-medal"></i>
                                        Con BECA <span style="color: red">*</span>
                                    </label>
                                    <select id="con_beca" name="con_beca" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="0" {{ $datospost->con_beca == 0 ? 'selected' : '' }}>NO</option>
                                        <option value="1" {{ $datospost->con_beca == 1 ? 'selected' : '' }}>SI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3 mb-3 d-none" id="additionalField5">
                                <label for="moda_beca" class="form-label" style="color: #91560d"><i
                                        class="fas fa-users"></i>
                                    Modalidad beca
                                    <span style="color: red">*</span>
                                </label>
                                <select id="moda_beca" name="moda_beca" class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger">
                                    <option value="">Seleccione</option>
                                    @foreach ($moda_beca as $moda_bec)
                                        <option value="{{ $moda_bec->id }}"
                                            {{ $moda_bec->id == $datospost->id_moda_beca ? 'selected' : '' }}>
                                            {{ $moda_bec->modalidad_beca }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3 mb-3 d-none" id="additionalField6">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                        N° Resolucion BECA
                                    </label>
                                    <input type="text" class="form-control" id="num_reso_beca" name="num_reso_beca"
                                        value="{{ $datospost->reso_beca }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-2 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-building"></i>
                                        Con trabajo <span style="color: red">*</span>
                                    </label>
                                    <select id="con_trabajo" name="con_trabajo" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="1" {{ $datospost->con_trabajo == 1 ? 'selected' : '' }}>SI
                                        </option>
                                        <option value="0" {{ $datospost->con_trabajo == 0 ? 'selected' : '' }}>NO
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 mb-3 d-none" id="additionalField7">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-tablets"></i>
                                        Tipo trabajo
                                    </label>
                                    <select id="tipo_trabajo" name="tipo_trabajo" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;">
                                        <option value="">Seleccione</option>
                                        <option value="1" {{ $datospost->tipo_trabajo == 1 ? 'selected' : '' }}>FORMAL
                                        </option>
                                        <option value="2" {{ $datospost->tipo_trabajo == 2 ? 'selected' : '' }}>INFORMAL
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-user-graduate"></i>
                                        Con estudios Previos <span style="color: red">*</span>
                                    </label>
                                    <select id="con_estudios" name="con_estudios" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="1" {{ $datospost->con_estudios == 1 ? 'selected' : '' }}>SI
                                        </option>
                                        <option value="0" {{ $datospost->con_estudios == 0 ? 'selected' : '' }}>NO
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4 mb-3 d-none" id="additionalField8">
                                <label for="estu_previos" class="form-label" style="color: #91560d"><i
                                        class="fas fa-users"></i>
                                    Estudios Previos
                                </label>
                                <select id="estu_previos" name="estu_previos" class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger">
                                    <option>Seleccione</option>
                                    @foreach ($estu_previos as $estu_previo)
                                        <option value="{{ $estu_previo->id }}"
                                            {{ $estu_previo->id == $datospost->id_estu_previos ? 'selected' : '' }}>
                                            {{ $estu_previo->nom_estuprevios }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="col-sm-4 mb-3 d-none" id="additionalField9">
                            <div class="form-group">
                                <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                    N° Resolucion APROBACION METAS
                                </label>
                                <input type="text" class="form-control" id="num_reso_metas" name="num_reso_metas"
                                    value="{{ $datospost->num_reso_metas }}" autocomplete="off">
                            </div>
                        </div> --}}
                        </div>

                        <div class="row">
                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-user-graduate"></i>
                                        Con Proyecto de investigación <span style="color: red">*</span>
                                    </label>
                                    <select id="con_proyectoe" name="con_proyectoe"
                                        class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                        style="width: 100%;" required>
                                        <option value="">Seleccione</option>
                                        <option value="0" {{ $datospost->con_proyecto == 0 ? 'selected' : '' }}>NO
                                        </option>
                                        <option value="1" {{ $datospost->con_proyecto == 1 ? 'selected' : '' }}>SI
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-9 mb-3 hidden" id="additionalFieldpie">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                        Nombre proyecto Investigación
                                    </label>
                                    <input type="text" class="form-control" id="nom_proyectoe" name="nom_proyectoe"
                                        placeholder="Ingrese el nombre del proyecto de investigación"
                                        value="{{ $datospost->nom_proyecto }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 card pl-4 pr-4 pt-4 shadow-sm mb-4"
                                style="background: linear-gradient(135deg, #ffb773, #f7f2e9);">
                                <h5 class="mb-3"><i class="fas fa-map-marked-alt"></i> Lugar de
                                    nacimiento</h5>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label style="color: #683d08" for="fecha_nacimiento" class="form-label"><i
                                                class="fas fa-calendar-alt"></i>
                                            Fecha
                                            de Nacimiento:
                                            <span style="color: red">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="fecha_nacimiento"
                                            name="fecha_nacimiento" value="{{ $datospost->fecha_de_nacimiento_postu }}"
                                            autocomplete="off" required>
                                    </div>

                                    <input type="hidden" value="{{ $datospost->distrito_nacimiento }}"
                                        id="distrito_nacimiento">
                                    <input type="hidden" value="{{ $datospost->provincia_nacimiento }}"
                                        id="provincia_nacimiento">
                                    <input type="hidden" value="{{ $datospost->departamento_nacimiento }}"
                                        id="departamento_nacimiento">

                                    <div class="col-sm-3 mb-3">
                                        <label for="departamento">Departamento <span
                                                style="color: rgb(180, 0, 0);">(*)</span></label>
                                        <select id="departamento" class="form-control" name="departamento">
                                            <option value="">Seleccione un departamento</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 mb-3">
                                        <label for="provincia">Provincia <span
                                                style="color: rgb(180, 0, 0);">(*)</span></label>
                                        <select id="provincia" class="form-control" name="provincia">
                                            <option value="">Seleccione una provincia</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 mb-3">
                                        <label for="distrito">Distrito <span style="color: rgb(180, 0, 0);">(*)</span></label>
                                        <select id="distrito" class="form-control" name="distrito">
                                            <option value="">Seleccione un distrito</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-language"></i>
                                        Lengua materna
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="lengua_materna" name="lengua_matern"
                                        class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                        style="width: 100%;" required>
                                        <option>Seleccione su lengua materna</option>
                                        @foreach ($lenguasmater as $item)
                                            <option value="{{ $item->id_lengua }}"
                                                {{ $item->id_lengua == $datospost->lengua_mater ? 'selected' : '' }}>
                                                {{ $item->nombre }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label style="color: #91560d" for="lengua_secundaria" class="form-label"><i
                                        class="fas fa-language"></i>
                                    Lengua
                                    Secundaria:</label>
                                <input type="text" class="form-control" id="lengua_secundaria" name="lengua_secundaria"
                                    value="{{ $datospost->lengua_secun }}" autocomplete="off"
                                    placeholder="Si no tienes dejar en blanco">
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label style="color: #91560d" for="celular" class="form-label"><i
                                        class="fas fa-mobile-alt"></i> N°
                                    Celular:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="text" class="form-control" id="celular" name="celular" maxlength="9"
                                    value="{{ $datospost->celular }}" autocomplete="off" required>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label style="color: #91560d" for="celular" class="form-label"><i class="fas fa-at"></i>
                                    Correo electronico:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="text" class="form-control" id="correo_electronico" name="correo_electronico"
                                    value="{{ $datospost->correo }}" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fa fa-wheelchair"></i>
                                        Discapacidad
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="discapa_postu" name="discapa_postu"
                                        class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                        style="width: 100%;" required>
                                        <option>¿Tienes Alguna Discapacidad?</option>
                                        <option value="1" {{ $datospost->discapacidad == 1 ? 'selected' : '' }}>SI
                                        </option>
                                        <option value="0" {{ $datospost->discapacidad == 0 ? 'selected' : '' }}>NO
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3 d-none" id="additionalField">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-table"></i>
                                        Tipo de Discapacidad
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="tipo_discapacidad" name="tipo_discapacidad"
                                        class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"
                                        style="width: 100%;">
                                        <option>Seleccione su discapacidad</option>
                                        @foreach ($discapacidad as $item)
                                            <option value="{{ $item->id_discapacidad }}"
                                                {{ $item->id_discapacidad == $datospost->tipo_discapacidad ? 'selected' : '' }}>
                                                {{ $item->nombre_discapacidad }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-3 d-none" id="additionalField2">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-sort-numeric-up-alt"></i>
                                        Numero conadis
                                        <span style="color: red">* (OBLIGATORIO)</span>
                                    </label>
                                    <input type="text" class="form-control" id="num_conadis" name="num_conadis"
                                        value="{{ $datospost->num_conadis }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 card pl-4 pr-4 pt-4 shadow-sm mb-4"
                                style="background: linear-gradient(135deg, #ffb773, #f7f2e9);">
                                <h5 class="mb-3"><i class="fas fa-house-user"></i> Domicilio
                                    actual</h5>
                                <div class="row">
                                    <div class="col-sm-3 mb-3">
                                        <label style="color: #91560d" for="direccion" class="form-label"><i
                                                class="fas fa-map-marker-alt"></i> <i class="fas fa-home"></i> Direccion de
                                            Domicilio
                                            Actual:
                                            <span style="color: red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="direccion" name="direccion"
                                            value="{{ $datospost->direccion_domicilio }}" autocomplete="off" required>
                                    </div>

                                    <input type="hidden" value="{{ $datospost->distrito_domicilio }}"
                                        id="distrito_domicilio">
                                    <input type="hidden" value="{{ $datospost->provincia_domicilio }}"
                                        id="provincia_domicilio">
                                    <input type="hidden" value="{{ $datospost->departamento_domicilio }}"
                                        id="departamento_domicilio">

                                    <div class="col-sm-3 mb-3">
                                        <label for="depadomicilio" class="form-label">Departamento
                                            <span style="color: rgb(180, 0, 0);">(*)</label>
                                        <select id="depadomicilio" class="form-control" name="depadomicilio">
                                            <option value="">Seleccione departamento</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 mb-3">
                                        <label for="providomicilio" class="form-label">Provincia
                                            <span style="color: rgb(180, 0, 0);">(*)</label>
                                        <select id="providomicilio" class="form-control" name="providomicilio">
                                            <option value="">Seleccione provincia</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 mb-3">
                                        <label for="distridomicilio" class="form-label">Distrito
                                            <span style="color: rgb(180, 0, 0);">(*)</label>
                                        <select id="distridomicilio" class="form-control" name="distridomicilio">
                                            <option value="">Seleccione distrito</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row">

                            <input type="hidden" value="{{ $datospost->distrito_colegio }}" id="distrito_colegio">
                            <input type="hidden" value="{{ $datospost->provincia_colegio }}" id="provincia_colegio">
                            <input type="hidden" value="{{ $datospost->departamento_colegio }}" id="departamento_colegio">
                            <input type="hidden" value="{{ $datospost->idubigeo_colegio }}" id="idubigeo_colegio">
                            <input type="hidden" value="{{ $datospost->codigo_modular }}" id="codigo_modular">
                            <input type="hidden" value="{{ $datospost->direccion_colegio }}" id="direccion_colegio">
                            <input type="hidden" value="{{ $datospost->idtipo_colegio }}" id="idtipo_colegio">

                            <div class="col-sm-12 mb-3">
                                <label style="color: #91560d" for="lugar_colegio" class="form-label"><i
                                        class="fas fa-chalkboard-teacher"></i>
                                    Lugar del Colegio:
                                    <span style="color: red">*</span>
                                </label>

                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="depacolegio" class="form-label">Departamento Colegio <span
                                        style="color: rgb(180, 0, 0);">(*)</span></label>
                                <select id="depacolegio" class="form-control" name="depacolegio">
                                    <option value="">Seleccione departamento</option>
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="provicolegio" class="form-label">Provincia Colegio <span
                                        style="color: rgb(180, 0, 0);">(*)</span></label>
                                <select id="provicolegio" class="form-control" name="provicolegio">
                                    <option value="">Seleccione provincia</option>
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="districolegio" class="form-label">Distrito Colegio <span
                                        style="color: rgb(180, 0, 0);">(*)</span></label>
                                <select id="districolegio" class="form-control" name="districolegio">
                                    <option value="">Seleccione distrito</option>
                                </select>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label for="nombrecolegio" class="form-label">Colegio <span
                                        style="color: rgb(180, 0, 0);">(*)</span></label>
                                <select id="nombrecolegio" class="form-control" name="nombrecolegio">
                                    <option value="">Seleccione colegio</option>
                                </select>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="codimodu" class="form-label">Código Modular</label>
                                <input type="text" class="form-control" id="codimodu" name="codimodu" readonly>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="direcole" class="form-label">Dirección Colegio</label>
                                <input type="text" class="form-control" id="direcole" name="direcole" readonly>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label style="color: #91560d" for="anio_promocion" class="form-label"><i
                                        class="fas fa-sort-numeric-up"></i> Año
                                    de Promoción:
                                    <span style="color: red">*</span>
                                </label>
                                <input type="number" class="form-control" id="anio_promocion" name="anio_promocion"
                                    value="{{ $datospost->año_de_termino_colegio }}" autocomplete="off" min="1"
                                    oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="4" required>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label style="color: #91560d"><i class="fas fa-list"></i> Tipo
                                        de colegio
                                        <span style="color: red">*</span>
                                    </label>
                                    <select id="tipo_colegio" name="tipo_colegio" class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                        <option>Seleccione el tipo de colegio</option>
                                        @foreach ($tipo_cole as $item)
                                            <option value="{{ $item->idtipo_colegio }}"
                                                {{ $item->idtipo_colegio == $datospost->idtipo_colegio ? 'selected' : '' }}>
                                                {{ $item->tipo_de_colegio }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row mb-5">
                            <input type="hidden" value="{{ $datospost->foto_postulante }}" id="foto_postulante">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="customFile">Fotografia</label>

                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="imagen" name="imagen"
                                            onchange="previewImage(event, '#imgPreview')">

                                        <label class="custom-file-label" for="customFile">Seleccionar foto</label>
                                    </div>
                                    <input type="hidden" id="foto" name="foto">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <center><img id="imgPreview" width="200px" height="200px"
                                        src="{{ asset($datospost->foto_postulante ?: 'fotos_postulantes/default-user.jpg') }}"
                                        style="border-radius: 10px; border: 2px solid #ccc;"></center>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">

                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
                    </div>
                </form>
            @endforeach
        @else
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <p style="font-size: 25px;"><strong>Atención:</strong> Este postulante no registró sus datos; APELLIDOS Y
                    NOMBRES, SEXO, DIRECCION, ..., etc,
                    solo registró usuario (DNI) y contraseña, para cambiar la contraseña en caso se aya olvidado el postulante,
                    click en el boton de abajo.</p>
            </div>
            <div class="row">
                <!-- Botón Editar Contraseña -->
                <div class="col-md-2 mb-2">
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#exampleModalp">
                        <i class="fas fa-key"></i> Editar Contraseña
                    </button>
                </div>
            </div>

            <!-- Modal para editar contraseña -->
            <div class="modal fade" id="exampleModalp" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #924900, #d49d5e);">
                            <h5 class="modal-title" id="modalLabel">
                                <i class="fas fa-key"></i> Editar Contraseña
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @livewire('update-password', ['id' => $idusu->id])
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
@stop

@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/estiloTitulo.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@stop


@section('js')
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('edad').addEventListener('input', function() {
            if (this.value < 1) {
                this.value = '';
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            let fotourl = $('#foto_postulante').val();
            let fotoNom = fotourl.split('/').pop();
            let fotoRuta = 'fotos_postulantes/' + fotoNom;
            $('#foto').val(fotoRuta);
        });
    </script>


    <script>
        function previewImage(event, querySelector) {

            //Recuperamos el input que desencadeno la acción
            const input = event.target;

            //Recuperamos la etiqueta img donde cargaremos la imagen
            $imgPreview = document.querySelector(querySelector);

            // Verificamos si existe una imagen seleccionada
            if (!input.files.length) return

            //Recuperamos el archivo subido
            file = input.files[0];

            //Creamos la url
            objectURL = URL.createObjectURL(file);

            //Modificamos el atributo src de la etiqueta img
            $imgPreview.src = objectURL;

        }
    </script>

    <script>
        function previewImagene(event, querySelector) {

            //Recuperamos el input que desencadeno la acción
            const input = event.target;

            //Recuperamos la etiqueta img donde cargaremos la imagen
            $imgPreview = document.querySelector(querySelector);

            // Verificamos si existe una imagen seleccionada
            if (!input.files.length) return

            //Recuperamos el archivo subido
            file = input.files[0];

            //Creamos la url
            objectURL = URL.createObjectURL(file);

            //Modificamos el atributo src de la etiqueta img
            $imgPreview.src = objectURL;

        }
    </script>

    <script>
        @if (session('success'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error"
            });
        @endif
    </script>

    <script>
        $(document).ready(function() {
            $('#form-loader').show(); // Mostrar loader interno
            $('#formPostulante :input').prop('disabled', true); // Opcional: desactivar campos
            // $('#form-loader').show();
            // $('#formPostulante').hide();
            ////////////// discapacidad
            const $discapa = $('#discapa_postu');
            const $fieldDet = $('#additionalField');
            const $fieldNum = $('#additionalField2');
            const $tipoDisc = $('#tipo_discapacidad');
            const $numConadis = $('#num_conadis');

            function toggleDiscapacidad(val) {
                if (val === "1") {
                    $fieldDet.removeClass('d-none');
                    $fieldNum.removeClass('d-none');
                    $tipoDisc.prop('disabled', false);
                    $numConadis.prop('disabled', false);
                } else {
                    $fieldDet.addClass('d-none');
                    $fieldNum.addClass('d-none');
                    $tipoDisc.val('').prop('disabled', true);
                    $numConadis.val('').prop('disabled', true);
                }
            }

            // Al cargar la página
            toggleDiscapacidad($discapa.val());

            // Al cambiar la selección
            $discapa.on('change', function() {
                toggleDiscapacidad($(this).val());
            });

            ////////////// fin discapacidad

            /////////////////////////// CON PROYECTO
            const $con_proye = $('#con_proyectoe');
            const $additionalFieldpie = $('#additionalFieldpie');
            const $nom_proye = $('#nom_proyectoe');

            // Mostrar u ocultar al cargar
            if ($con_proye.val() === "1") {
                $additionalFieldpie.removeClass('d-none');
                $nom_proye.prop('disabled', false);
            } else {
                $additionalFieldpie.addClass('d-none');
                $nom_proye.prop('disabled', true);
            }

            // Cambios dinámicos
            $con_proye.on('change', function() {
                if ($(this).val() === "1") {
                    $additionalFieldpie.removeClass('d-none');
                    $nom_proye.prop('disabled', false);
                } else {
                    $additionalFieldpie.addClass('d-none');
                    $nom_proye.val('');
                    $nom_proye.prop('disabled', true);
                }
            });
            /////////////////////////// FIN CON PROYECTO

            ///////////////// con estudios superio
            const $conEstudios = $('#con_estudios');
            const $fieldEstudios = $('#additionalField8');
            const $fieldResol = $('#additionalField9');
            const $estuPrevios = $('#estu_previos');
            const $numResoMetas = $('#num_reso_metas');

            // Función para mostrar/ocultar según el valor
            function toggleEstudios(val) {
                if (val === "1") {
                    $fieldEstudios.removeClass('d-none');
                    $fieldResol.removeClass('d-none');
                    $estuPrevios.prop('disabled', false);
                    $numResoMetas.prop('disabled', false);
                } else {
                    $fieldEstudios.addClass('d-none');
                    $fieldResol.addClass('d-none');
                    $estuPrevios.val('').prop('disabled', true);
                    $numResoMetas.val('').prop('disabled', true);
                }
            }

            // 1) Al cargar la página
            toggleEstudios($conEstudios.val());

            // 2) Al cambiar el select
            $conEstudios.on('change', function() {
                toggleEstudios($(this).val());
            });
            ///////////////// FIN con estudios superio

            /////////////////////////// CON HIJOS
            const $conHijos = $('#con_hijos');
            const $additionalField4 = $('#additionalField4');
            const $cantHijos = $('#cant_hijos');

            // Mostrar u ocultar al cargar
            if ($conHijos.val() === "1") {
                $additionalField4.removeClass('d-none');
                $cantHijos.prop('disabled', false);
            } else {
                $additionalField4.addClass('d-none');
                $cantHijos.prop('disabled', true);
            }

            // Cambios dinámicos
            $conHijos.on('change', function() {
                if ($(this).val() === "1") {
                    $additionalField4.removeClass('d-none');
                    $cantHijos.prop('disabled', false);
                } else {
                    $additionalField4.addClass('d-none');
                    $cantHijos.val('');
                    $cantHijos.prop('disabled', true);
                }
            });
            /////////////////////////// FIN CON HIJOS

            ////////////////////// REBRED
            const $rebred = $('#rebred');
            const $additionalField3 = $('#additionalField3');
            const $numReso = $('#num_reso_rebred');

            // Mostrar u ocultar al cargar
            if ($rebred.val() === "1") {
                $additionalField3.removeClass('d-none');
                $numReso.prop('disabled', false);
            } else {
                $additionalField3.addClass('d-none');
                $numReso.prop('disabled', true);
            }

            // Cambios dinámicos
            $rebred.on('change', function() {
                if ($(this).val() === "1") {
                    $additionalField3.removeClass('d-none');
                    $numReso.prop('disabled', false);
                } else {
                    $additionalField3.addClass('d-none');
                    $numReso.val('');
                    $numReso.prop('disabled', true);
                }
            });
            ////////////////////// FIN REBRED

            ////////////////////// CON BECA
            const $conBeca = $('#con_beca');
            const $modalidadBeca = $('#additionalField5');
            const $resolucionBeca = $('#additionalField6');
            const $numResoBeca = $('#num_reso_beca');
            const $modaBeca = $('#moda_beca');

            // Mostrar u ocultar al cargar
            if ($conBeca.val() === "1") {
                $modalidadBeca.removeClass('d-none');
                $resolucionBeca.removeClass('d-none');
                $modaBeca.prop('disabled', false);
                $numResoBeca.prop('disabled', false);
            } else {
                $modalidadBeca.addClass('d-none');
                $resolucionBeca.addClass('d-none');
                $modaBeca.prop('disabled', true);
                $numResoBeca.prop('disabled', true);
            }

            // Cambios dinámicos
            $conBeca.on('change', function() {
                if ($(this).val() === "1") {
                    $modalidadBeca.removeClass('d-none');
                    $resolucionBeca.removeClass('d-none');
                    $modaBeca.prop('disabled', false);
                    $numResoBeca.prop('disabled', false);
                } else {
                    $modalidadBeca.addClass('d-none');
                    $resolucionBeca.addClass('d-none');
                    $modaBeca.val('');
                    $numResoBeca.val('');
                    $modaBeca.prop('disabled', true);
                    $numResoBeca.prop('disabled', true);
                }
            });
            ////////////////////// FIN CON BECA

            ////////////////// CON TRABAJO
            const $conTrabajo = $('#con_trabajo');
            const $tipoTrabajoField = $('#additionalField7');
            const $tipoTrabajo = $('#tipo_trabajo');

            // Mostrar u ocultar al cargar
            if ($conTrabajo.val() === "1") {
                $tipoTrabajoField.removeClass('d-none');
                $tipoTrabajo.prop('disabled', false);
            } else {
                $tipoTrabajoField.addClass('d-none');
                $tipoTrabajo.prop('disabled', true);
            }

            // Cambios dinámicos
            $conTrabajo.on('change', function() {
                if ($(this).val() === "1") {
                    $tipoTrabajoField.removeClass('d-none');
                    $tipoTrabajo.prop('disabled', false);
                } else {
                    $tipoTrabajoField.addClass('d-none');
                    $tipoTrabajo.val('');
                    $tipoTrabajo.prop('disabled', true);
                }
            });
            ////////////////// FIN CON TRABAJO

            //////////////////////////////////////// AJAX
            //////////////////////////////////////// AJAX
            //////////////////////////////////////// AJAX //
            // {{-- ////////////////// naci --}}
            $.get('{{ route('departamento') }}', function(data) {
                $('#departamento').append(data.map(d =>
                    `<option value="${d.departamento}">${d.departamento}</option>`));
            });

            $('#departamento').change(function() {
                let departamento = $(this).val();
                $('#provincia').empty().append('<option value="">Seleccione una provincia</option>');
                $('#distrito').empty().append('<option value="">Seleccione un distrito</option>');

                let url = "{{ route('provincia', ':departamento') }}".replace(':departamento',
                    encodeURIComponent(
                        departamento));

                if (departamento) {
                    $.get(url, function(data) {
                        $('#provincia').append(data.map(p =>
                            `<option value="${p.provincia}">${p.provincia}</option>`));
                    });
                }
            });

            $('#provincia').change(function() {
                let provincia = $(this).val();
                $('#distrito').empty().append('<option value="">Seleccione un distrito</option>');

                let url = "{{ route('distrito', ':distrito') }}".replace(':distrito',
                    encodeURIComponent(
                        provincia));

                if (provincia) {
                    $.get(url, function(data) {
                        $('#distrito').append(data.map(d =>
                            `<option value="${d.Ubigeo}">${d.distrito}</option>`));
                    });
                }
            });

            //////NACIMIENTO
            $.get('{{ route('departamento') }}', function(data) {
                let departamento_nacimiento = $('#departamento_nacimiento').val()

                $('#departamento').empty().append(
                    '<option value="">Seleccione un departamento</option>');
                data.forEach(d => {
                    let selected = d.departamento === departamento_nacimiento ? "selected" : "";
                    $('#departamento').append(
                        `<option value="${d.departamento}" ${selected}>${d.departamento}</option>`
                    );
                });

                let departamento = departamento_nacimiento;
                let urlProv = "{{ route('provincia', ':departamento') }}".replace(':departamento',
                    encodeURIComponent(departamento));
                $.get(urlProv, function(data) {
                    let provincia_nacimiento = $('#provincia_nacimiento').val()

                    $('#provincia').empty().append(
                        '<option value="">Seleccione una provincia</option>');
                    data.forEach(p => {
                        let selected = p.provincia === provincia_nacimiento ?
                            "selected" :
                            "";
                        $('#provincia').append(
                            `<option value="${p.provincia}" ${selected}>${p.provincia}</option>`
                        );
                    });

                    let provincia = provincia_nacimiento;
                    let urlDist = "{{ route('distrito', ':provincia') }}".replace(
                        ':provincia',
                        encodeURIComponent(provincia));
                    $.get(urlDist, function(data) {
                        let distrito_nacimiento = $('#distrito_nacimiento').val()

                        $('#distrito').empty().append(
                            '<option value="">Seleccione un distrito</option>'
                        );
                        data.forEach(d => {
                            let selected = d.distrito === distrito_nacimiento ?
                                "selected" : "";
                            $('#distrito').append(
                                `<option value="${d.Ubigeo}" ${selected}>${d.distrito}</option>`
                            );
                        });
                    });
                });
            });

            // {{-- /////////////////////////////////////// domi --}}
            $.get('{{ route('departamento') }}', function(data) {
                $('#depadomicilio').append(data.map(d =>
                    `<option value="${d.departamento}">${d.departamento}</option>`));
            });

            $('#depadomicilio').change(function() {
                let depadomicilio = $(this).val();
                $('#providomicilio').empty().append(
                    '<option value="">Seleccione una provincia</option>');
                $('#distridomicilio').empty().append(
                    '<option value="">Seleccione un distrito</option>');

                let url = "{{ route('provincia', ':departamento') }}".replace(':departamento',
                    encodeURIComponent(
                        depadomicilio));

                if (depadomicilio) {
                    $.get(url, function(data) {
                        $('#providomicilio').append(data.map(p =>
                            `<option value="${p.provincia}">${p.provincia}</option>`));
                    });
                }
            });

            $('#providomicilio').change(function() {
                let providomicilio = $(this).val();
                $('#distridomicilio').empty().append(
                    '<option value="">Seleccione un distrito</option>');

                let url = "{{ route('distrito', ':distrito') }}".replace(':distrito',
                    encodeURIComponent(
                        providomicilio));

                if (providomicilio) {
                    $.get(url, function(data) {
                        $('#distridomicilio').append(data.map(d =>
                            `<option value="${d.Ubigeo}">${d.distrito}</option>`));
                    });
                }
            });

            $.get('{{ route('departamento') }}', function(data) {
                let departamento_domicilio = $('#departamento_domicilio').val()

                $('#depadomicilio').empty().append(
                    '<option value="">Seleccione un departamento</option>');
                data.forEach(d => {
                    let selected = d.departamento === departamento_domicilio ? "selected" : "";
                    $('#depadomicilio').append(
                        `<option value="${d.departamento}" ${selected}>${d.departamento}</option>`
                    );
                });

                let departamentoss = departamento_domicilio;
                let urlProv = "{{ route('provincia', ':departamento') }}".replace(
                    ':departamento',
                    encodeURIComponent(departamentoss));
                $.get(urlProv, function(data) {
                    let provincia_domicilio = $('#provincia_domicilio').val()

                    $('#providomicilio').empty().append(
                        '<option value="">Seleccione una provincia</option>');
                    data.forEach(p => {
                        let selected = p.provincia === provincia_domicilio ?
                            "selected" :
                            "";
                        $('#providomicilio').append(
                            `<option value="${p.provincia}" ${selected}>${p.provincia}</option>`
                        );
                    });

                    let provinciado = provincia_domicilio;
                    let urlDist = "{{ route('distrito', ':provincia') }}".replace(
                        ':provincia',
                        encodeURIComponent(provinciado));
                    $.get(urlDist, function(data) {
                        let distrito_domicilio = $('#distrito_domicilio').val()

                        $('#distridomicilio').empty().append(
                            '<option value="">Seleccione un distrito</option>'
                        );
                        data.forEach(d => {
                            let selected = d.distrito === distrito_domicilio ?
                                "selected" : "";
                            $('#distridomicilio').append(
                                `<option value="${d.Ubigeo}" ${selected}>${d.distrito}</option>`
                            );
                        });
                    });
                });
            });

            // {{-- ///////////////// cole --}}
            $.get('{{ route('departamento') }}', function(data) {
                $('#depacolegio').append(data.map(d =>
                    `<option value="${d.departamento}">${d.departamento}</option>`));
            });

            $('#depacolegio').change(function() {
                let depacolegio = $(this).val();
                $('#provicolegio').empty().append('<option value="">Seleccione una provincia</option>');
                $('#districolegio').empty().append('<option value="">Seleccione un distrito</option>');
                $('#nombrecolegio').empty().append('<option value="">Seleccione colegio</option>');
                $('#codimodu').val('');
                $('#direcole').val('');

                let url = "{{ route('provincia', ':departamento') }}".replace(':departamento',
                    encodeURIComponent(
                        depacolegio));

                if (depacolegio) {
                    $.get(url, function(data) {
                        $('#provicolegio').append(data.map(p =>
                            `<option value="${p.provincia}">${p.provincia}</option>`));
                    });
                }
            });

            $('#provicolegio').change(function() {
                let provicolegio = $(this).val();
                $('#districolegio').empty().append('<option value="">Seleccione un distrito</option>');

                let url = "{{ route('distrito', ':distrito') }}".replace(':distrito',
                    encodeURIComponent(
                        provicolegio));

                if (provicolegio) {
                    $.get(url, function(data) {
                        $('#districolegio').append(data.map(d =>
                            `<option value="${d.Ubigeo}">${d.distrito}</option>`));
                    });
                }
            });

            $('#districolegio').change(function() {
                let districolegio = $(this).val();
                $('#nombrecolegio').empty().append('<option value="">Seleccione colegio</option>');

                let url = "{{ route('colegio', [':cole', ':tipo']) }}".replace(':cole',
                    encodeURIComponent(
                        districolegio)).replace(':tipo', 1);

                if (districolegio) {
                    $.get(url, function(data) {
                        $('#nombrecolegio').append(data.map(d =>
                            `<option value="${d.Codigo_Modular}">${d.Nombre_ie}</option>`
                        ));
                    });
                }
            });

            $('#nombrecolegio').change(function() {
                let cod_modular = $(this).val();
                // $('#nombrecolegio').empty().append('<option value="">Seleccione colegio</option>');

                let url = "{{ route('colegio', [':cole', ':tipo']) }}".replace(':cole',
                    encodeURIComponent(
                        cod_modular)).replace(':tipo', 2);

                if (cod_modular) {
                    $.get(url, function(data) {
                        $('#codimodu').val(data[0].Codigo_Modular);
                        $('#direcole').val(data[0].Direccion);
                    });
                }
            });

            //////////////
            $.get('{{ route('departamento') }}', function(data) {
                let departamento_colegio = $('#departamento_colegio').val()

                $('#depacolegio').empty().append(
                    '<option value="">Seleccione un departamento</option>');
                data.forEach(d => {
                    let selected = d.departamento === departamento_colegio ?
                        "selected" : "";

                    $('#depacolegio').append(
                        `<option value="${d.departamento}" ${selected}>${d.departamento}</option>`
                    );
                });

                let departamentoco = departamento_colegio;
                let urlProv = "{{ route('provincia', ':departamento') }}".replace(
                    ':departamento',
                    encodeURIComponent(departamentoco));
                $.get(urlProv, function(data) {
                    let provincia_colegio = $('#provincia_colegio').val()
                    $('#provicolegio').empty().append(
                        '<option value="">Seleccione una provincia</option>');
                    data.forEach(p => {
                        let selected = p.provincia === provincia_colegio ? "selected" :
                            "";
                        $('#provicolegio').append(
                            `<option value="${p.provincia}" ${selected}>${p.provincia}</option>`
                        );
                    });

                    let provinciaco = provincia_colegio;
                    let urlDist = "{{ route('distrito', ':provincia') }}".replace(
                        ':provincia',
                        encodeURIComponent(provinciaco));
                    $.get(urlDist, function(data) {
                        let distrito_colegio = $('#distrito_colegio').val()
                        $('#districolegio').empty().append(
                            '<option value="">Seleccione un distrito</option>'
                        );
                        data.forEach(d => {
                            let selected = d.distrito === distrito_colegio ?
                                "selected" : "";
                            $('#districolegio').append(
                                `<option value="${d.Ubigeo}" ${selected}>${d.distrito}</option>`
                            );
                        });

                        let distritoco = $('#idubigeo_colegio').val();
                        let urlcole =
                            "{{ route('colegio', [':cole', ':tipo']) }}"
                            .replace(':cole',
                                encodeURIComponent(
                                    distritoco)).replace(':tipo', 1);

                        $.get(urlcole, function(data) {
                            $('#nombrecolegio').empty().append(
                                '<option value="">Seleccione colegio</option>'
                            );
                            let codigo_modular = $('#codigo_modular').val();
                            data.forEach(d => {
                                let selected = d.Codigo_Modular ===
                                    codigo_modular ?
                                    "selected" : "";

                                $('#nombrecolegio').append(
                                    `<option value="${d.Codigo_Modular}" ${selected}>${d.Nombre_ie}</option>`
                                );
                                $('#codimodu').val(codigo_modular);
                                $('#direcole').val($('#direccion_colegio')
                                    .val());
                                $('#tipocole').val($('#idtipo_colegio')
                                    .val());
                            });
                            $('#form-loader').fadeOut();
                            $('#formPostulante :input').prop('disabled', false);

                            // $('#form-loader').fadeOut();
                            // $('#formPostulante').fadeIn();

                            $('#form-loader').remove();
                        });
                    });
                });
            });

            // $('#form-loader').fadeOut();
            // $('#formPostulante').fadeIn();
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".mause").hover(
                function() {
                    $(this).css("color", "#ba9643");
                },
                function() {
                    $(this).css("color", "#4a3911");
                }
            );
        });
    </script>
@stop
