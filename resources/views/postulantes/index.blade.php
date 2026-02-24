@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />


    @livewireStyles

@stop

@section('content')

    <style>
        .hidden {
            display: none;
        }
    </style>

    @if ($mostrardatos)
        <!-- START ALERTS AND CALLOUTS -->


        <div class="container-fluid">
            <div class="row justify-content-center">

            </div>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    {{-- @dump($user->hasAnyRole('postulante')) --}}
                    @if ($user->hasAnyRole('postulante'))
                        @if (count($ver_si_esta_inscri) > 0)
                            <div class="card card-info shadow-lg">
                                <div class="card-header text-center">
                                    <h1 class="card-title font-weight-bold" style="font-size: 30px;"><i class="fas fa-paperclip"></i> Constancia
                                        de
                                        postulación al {{ $ver_si_esta_inscri[0]->nombre_proceso }} con la
                                        modalidad:
                                        {{ $ver_si_esta_inscri[0]->nombre_modalidad }} </h1>
                                </div>
                                <div class="card-body text-center">
                                    <p class="lead"> Estimado postulante, puede visualizar su constancia de
                                        inscripción en el siguiente enlace: </p>

                                    <form action="{{ route('pdf.fichainscritosconstancia') }}" method="POST"
                                        enctype="multipart/form-data" target="_blank">
                                        @csrf
                                        <button title="Constancia de inscripción" class="btn btn-lg btn-danger"
                                            type="submit" id="idpostu" name="idpostu"
                                            value="{{ $ver_si_esta_inscri[0]->idinscripcion }}">
                                            <i class="fas fa-file-pdf"></i> Ver
                                            Constancia en PDF
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                                <h4><i class="icon fas fa-exclamation-triangle"></i> COMUNICADO!</h4>
                                Usted acaba de realizar su pre-inscripción. Debe acercarse a la oficina de GAMANIEL
                                BLANCO
                                MURILLO para
                                terminar su inscripción con los siguientes requisitos:
                                <ul>
                                    <li>CERTIFICADO DE ESTUDIOS</li>
                                    <li>ACTA DE NACIMIENTO</li>
                                    <li>COPIA DE DNI</li>
                                    <li>FOTOGRAFÍA TAMAÑO CARNET (EN CASO NO PUSO LA FOTO AL MOMENTO DE REGISTRARSE O
                                        QUIERE
                                        ACTUALIZAR SU
                                        FOTOGRAFIA, LLEVAR LA FOTO EN FORMATO DIGITAL TAMBIÉN)</li>
                                </ul>
                            </div>
                        @endif
                    @else
                        <div class="card shadow-lgshadow-lg">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #924900, #d49d5e); color: white;">
                                <h3 class="card-title">
                                    <i class="fas fa-user-cog"></i> Opciones de Usuario
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <!-- Botón Editar Datos -->
                                    <div class="col-md-5 mb-2">
                                        {{-- <a href="{{ route('verDetalle.postulante', ['idpostulante' => $userIddni]) }}" --}}
                                        <a href="{{ route('verDetalle.postulante', ['idpostulante' => $userIddni]) }}"
                                            class="btn btn-info btn-block">
                                            <i class="fas fa-user-edit"></i> Editar Datos
                                        </a>
                                    </div>

                                    <!-- Botón Editar Contraseña -->
                                    <div class="col-md-5 mb-2">
                                        <button type="button" class="btn btn-info btn-block" data-toggle="modal"
                                            data-target="#exampleModalp">
                                            <i class="fas fa-key"></i> Editar Contraseña
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal para editar contraseña -->
                        <div class="modal fade" id="exampleModalp" tabindex="-1" role="dialog"
                            aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header text-white"
                                        style="background: linear-gradient(135deg, #924900, #d49d5e);">
                                        <h5 class="modal-title" id="modalLabel">
                                            <i class="fas fa-key"></i> Editar Contraseña
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"
                                            aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @livewire('update-password', ['id' => $user->id])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- /.card -->
                </div>
                <!-- /.col -->


                <!-- /.col -->
            </div>

        </div>
        <!-- /.row -->
        <!-- END ALERTS AND CALLOUTS -->
        <style>
            .container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                font-family: Arial, sans-serif;
            }

            .part {
                border: 1px solid #ccc;
                padding: 15px;
                border-radius: 8px;
                background-color: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .part h3 {
                background-color: #007bff;
                color: #fff;
                padding: 10px;
                border-radius: 5px;
                text-align: center;
                font-size: 18px;
                margin-bottom: 15px;
            }

            .form-label {
                font-weight: bold;
                margin-bottom: 5px;
                display: block;
            }

            .form-control {
                width: 100%;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                box-sizing: border-box;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .col-sm-4,
            .col-sm-6,
            .col-sm-2 {
                flex: 1;
                min-width: 150px;
            }

            img {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>

        @foreach ($postulantesdatos as $item)
            <div class="container-fluid">
                <div class="part">
                    <h3 style="text-align: center;">DATOS POSTULANTE</h3>
                    <br>
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <label for="dni" class="form-label">DNI POSTULANTE:</label>
                            <input type="text" class="form-control" id="dni" name="dni"
                                value="{{ $item->idpostulante }}" disabled>
                        </div>
                        <div class="col-sm-2 mb-1">
                            <label for="edadpostu" class="form-label">EDAD:</label>
                            <input type="text" class="form-control" id="edadpostu" name="edadpostu"
                                value="{{ $item->edad_postulante }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="apellidopater" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidopater" name="apellidopater"
                                value="{{ $item->apellidos_pater_postulante }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="apellidomater" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidomater" name="apellidomater"
                                value="{{ $item->apellidos_mater_postulante }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-7 mb-3">
                            <label for="nombrespostu" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="nombrespostu" name="nombrespostu"
                                value="{{ $item->nombres_postulante }}" disabled>
                        </div>
                        <div class="col-sm-2 mb-3">
                            <label for="fechanacimien" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechanacimien" name="fechanacimien"
                                value="{{ $item->fecha_de_nacimiento_postu }}" disabled>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <label for="celularpostu" class="form-label">Celular</label>
                            <input type="text" class="form-control" id="celularpostu" name="celularpostu"
                                value="{{ $item->celular }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <label for="correopostu" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correopostu" name="correopostu"
                                value="{{ $item->correo }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="lenguamaterna" class="form-label">Lengua Materna</label>
                            <input type="text" class="form-control" id="lenguamaterna" name="lenguamaterna"
                                value="{{ $item->lengua_mater }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="lenguasecundaria" class="form-label">Lengua Secundaria</label>
                            <input type="text" class="form-control" id="lenguasecundaria" name="lenguasecundaria"
                                value="{{ $item->lengua_secun }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Sección de foto -->
                        <div class="col-sm-12">
                            <h4 style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px;">FOTO DE
                                POSTULANTE
                            </h4>
                        </div>
                        <div class="col-sm-8 mb-6 d-flex justify-content-center align-items-center" style="height: 100%;">
                            <img src="{{ asset($item->foto_postulante) }}" alt="Foto del postulante"
                                style="max-width: 100%; height: auto; display: block;">
                        </div>
                    </div>
                </div>
                <div class="part">
                    <h3 style="text-align: center;">DATOS COMPLEMENTARIOS</h3>
                    <br>
                    <div class="row">
                        <!-- Sección de Nacimiento -->
                        <div class="col-sm-12">
                            <h4 style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px;">Datos
                                de Nacimiento
                            </h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <label for="distrinacimiento" class="form-label">Distrito Nacimiento</label>
                            <input type="text" class="form-control" id="distrinacimiento" name="distrinacimiento"
                                value="{{ $item->distrito_nacimiento }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="provinacimiento" class="form-label">Provincia Nacimiento</label>
                            <input type="text" class="form-control" id="provinacimiento" name="provinacimiento"
                                value="{{ $item->provincia_nacimiento }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="depanacimiento" class="form-label">Departamento Nacimiento</label>
                            <input type="text" class="form-control" id="depanacimiento" name="depanacimiento"
                                value="{{ $item->departamento_nacimiento }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sección de Domicilio -->
                        <div class="col-sm-12 mt-3">
                            <h4 style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px;">Datos
                                de Domicilio
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <label for="distridomicilio" class="form-label">Distrito de Domicilio</label>
                            <input type="text" class="form-control" id="distridomicilio" name="distridomicilio"
                                value="{{ $item->distrito_domicilio }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="providomicilio" class="form-label">Provincia de Domicilio</label>
                            <input type="text" class="form-control" id="providomicilio" name="providomicilio"
                                value="{{ $item->provincia_domicilio }}" disabled>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="depadomicilio" class="form-label">Departamento de Domicilio</label>
                            <input type="text" class="form-control" id="depadomicilio" name="depadomicilio"
                                value="{{ $item->departamento_domicilio }}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label for="diredomi" class="form-label">Dirección Domicilio</label>
                            <input type="text" class="form-control" id="diredomi" name="diredomi"
                                value="{{ $item->direccion_domicilio }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="part">
                    <h3 style="text-align: center;">DATOS COLEGIO</h3>
                    <br>
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label for="nombrecole" class="form-label">Nombre Colegio</label>
                            <input type="text" class="form-control" id="nombrecole" name="nombrecole"
                                value="{{ $item->colegio }}" disabled>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="codimodu" class="form-label">Código Modular</label>
                            <input type="text" class="form-control" id="codimodu" name="codimodu"
                                value="{{ $item->codigo_modular }}" disabled>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="añotermicole" class="form-label">Año de Término Colegio</label>
                            <input type="text" class="form-control" id="añotermicole" name="añotermicole"
                                value="{{ $item->año_de_termino_colegio }}" disabled>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <h4 style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px;">LUGAR
                                COLEGIO</h4>
                        </div>
                        <div class="col-sm-9 mb-3">
                            <label for="districolegio" class="form-label">Distrito Colegio</label>
                            <input type="text" class="form-control" id="districolegio" name="districolegio"
                                value="{{ $item->distrito_colegio }}" disabled>
                        </div>
                        <div class="col-sm-9 mb-3">
                            <label for="provicolegio" class="form-label">Provincia Colegio</label>
                            <input type="text" class="form-control" id="provicolegio" name="provicolegio"
                                value="{{ $item->provincia_colegio }}" disabled>
                        </div>
                        <div class="col-sm-9 mb-3">
                            <label for="depacolegio" class="form-label">Departamento Colegio</label>
                            <input type="text" class="form-control" id="depacolegio" name="depacolegio"
                                value="{{ $item->departamento_colegio }}" disabled>
                        </div>
                        <div class="col-sm-9 mb-3">
                            <label for="direcole" class="form-label">Dirección Colegio</label>
                            <input type="text" class="form-control" id="direcole" name="direcole"
                                value="{{ $item->direccion_colegio }}" disabled>
                        </div>

                    </div>

                </div>

            </div>
        @endforeach

    @endif


    @if ($mostrarFormulario)
        <!-- /.row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #924900, #d49d5e); ">
                        <h3 class="card-title"><i class="fas fa-user-edit"></i> <i class="fas fa-sign-out-alt"></i>
                            REGISTRO DE DATOS DEL
                            POSTULANTE</h3>
                    </div>
                    <div class="card-body p-0">

                        <div class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <!-- your steps here -->
                                <div class="step" data-target="#logins-part">
                                    <button type="button" class="step-trigger" role="tab"
                                        aria-controls="logins-part" id="logins-part-trigger">
                                        <span class="bs-stepper-circle">1</span>
                                        <span class="bs-stepper-label">Datos Personales</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#colegio-part">
                                    <button type="button" class="step-trigger" role="tab"
                                        aria-controls="colegio-part" id="colegio-part-trigger">
                                        <span class="bs-stepper-circle">2</span>
                                        <span class="bs-stepper-label">Colegio</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#information-part">
                                    <button type="button" class="step-trigger" role="tab"
                                        aria-controls="information-part" id="information-part-trigger">
                                        <span class="bs-stepper-circle">3</span>
                                        <span class="bs-stepper-label">Foto</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <!-- your steps content here -->
                                <form action="{{ route('postulante.agregar') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div id="logins-part" class="content" role="tabpanel"
                                        aria-labelledby="logins-part-trigger">
                                        <div class="form-group">
                                            <div class="container">
                                                <div class="row">
                                                    <input type="hidden" id="dni" name="dni"
                                                        value="{{ $userIddni }}">
                                                    <div class="col-sm-2 mb-2">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-user"></i>
                                                                Nacionalidad
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <input type="text" list="lista-nacionalidad"
                                                                class="form-control" id="nacionalidad"
                                                                name="nacionalidad" autocomplete="off" required>

                                                            <datalist id="lista-nacionalidad">
                                                                <option value="Peruana">
                                                                <option value="Colombiana">
                                                                <option value="Venezolana">
                                                            </datalist>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3 mb-2">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-layer-group"></i> Tipo Documento
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="tipodocumento" name="tipodocumento"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">DNI</option>
                                                                <option value="2">CARNET DE EXTRANJERÍA</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2 mb-2">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-venus-mars"></i> Sexo
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="genero_postu" name="genero_postu"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione su genero</option>
                                                                <option value="1">MASCÚLINO</option>
                                                                <option value="2">FEMENINO</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1 mb-3">
                                                        <label for="edad" class="form-label"
                                                            style="color: #91560d"><i class="fas fa-sort-numeric-up"></i>
                                                            Edad:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="number" class="form-control" id="edad"
                                                            name="edad" value="" autocomplete="off"
                                                            min="1"
                                                            oninput="this.value = this.value.slice(0, this.maxLength);"
                                                            maxlength="3" required>
                                                    </div>

                                                    <div class="col-sm-4 mb-3">
                                                        <label for="edad" class="form-label"
                                                            style="color: #91560d"><i class="fas fa-users"></i>
                                                            Identidad Etnica
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <select id="identidadetnica" name="identidadetnica"
                                                            class="form-control select2 select2-danger"
                                                            data-dropdown-css-class="select2-danger" required>
                                                            <option>Seleccione</option>
                                                            @foreach ($idenEtnicas as $idenEtnica)
                                                                <option value="{{ $idenEtnica->id }}">
                                                                    {{ $idenEtnica->name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4 mb-3">
                                                        <label for="apellido_paterno" class="form-label"
                                                            style="color: #91560d"><i class="fas fa-sort-alpha-up"></i>
                                                            Apellido Paterno:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="apellido_paterno"
                                                            name="apellido_paterno" value="" autocomplete="off"
                                                            required>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <label style="color: #91560d" for="apellido_materno"
                                                            class="form-label"><i class="fas fa-sort-alpha-up"></i>
                                                            Apellido Materno:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="apellido_materno"
                                                            name="apellido_materno" value="" autocomplete="off"
                                                            required>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <label style="color: #91560d" for="nombres"
                                                            class="form-label"><i class="fas fa-sort-alpha-up"></i>
                                                            Nombres:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="nombres"
                                                            name="nombres" value="" autocomplete="off" required>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3 mb-2">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fab fa-usps"></i>
                                                                Estado Civil
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="est_civil" name="est_civil"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                @foreach ($est_civil as $est_civi)
                                                                    <option value="{{ $est_civi->id }}">
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
                                                            <select id="con_hijos" name="con_hijos"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 mb-3 hidden" id="additionalField4">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                ¿Cuantos hijos?
                                                            </label>
                                                            <input type="number" min="1"
                                                                oninput="this.value = this.value.slice(0, this.maxLength);"
                                                                maxlength="3" class="form-control" id="edad"
                                                                name="cant_hijos" value="" autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-plus"></i>
                                                                Beneficiario REBRED <span style="color: red">*</span>
                                                            </label>
                                                            <select id="rebred" name="rebred"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 mb-3 hidden" id="additionalField3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                N° Resolucion REBRED
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="num_reso_rebred" name="num_reso_rebred"
                                                                value="" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-2 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-medal"></i>
                                                                Con BECA <span style="color: red">*</span>
                                                            </label>
                                                            <select id="con_beca" name="con_beca"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3 mb-3 hidden" id="additionalField5">
                                                        <label for="moda_beca" class="form-label"
                                                            style="color: #91560d"><i class="fas fa-users"></i>
                                                            Modalidad beca
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <select id="moda_beca" name="moda_beca"
                                                            class="form-control select2 select2-danger"
                                                            data-dropdown-css-class="select2-danger">
                                                            <option>Seleccione</option>
                                                            @foreach ($moda_beca as $moda_bec)
                                                                <option value="{{ $moda_bec->id }}">
                                                                    {{ $moda_bec->modalidad_beca }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-sm-3 mb-3 hidden" id="additionalField6">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                N° Resolucion BECA
                                                            </label>
                                                            <input type="text" class="form-control" id="num_reso_beca"
                                                                name="num_reso_beca" value="" autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-building"></i>
                                                                Con trabajo <span style="color: red">*</span>
                                                            </label>
                                                            <select id="con_trabajo" name="con_trabajo"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 mb-3 hidden" id="additionalField7">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-tablets"></i>
                                                                Tipo trabajo <span style="color: red">*</span>
                                                            </label>
                                                            <select id="tipo_trabajo" name="tipo_trabajo"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione</option>
                                                                <option value="1">FORMAL</option>
                                                                <option value="2">INFORMAL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-user-graduate"></i>
                                                                Con estudios Previos <span style="color: red">*</span>
                                                            </label>
                                                            <select id="con_estudios" name="con_estudios"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4 mb-3 hidden" id="additionalField8">
                                                        <label for="estu_previos" class="form-label"
                                                            style="color: #91560d"><i class="fas fa-users"></i>
                                                            Estudios Previos
                                                        </label>
                                                        <select id="estu_previos" name="estu_previos"
                                                            class="form-control select2 select2-danger"
                                                            data-dropdown-css-class="select2-danger">
                                                            <option>Seleccione</option>
                                                            @foreach ($estu_previos as $estu_previo)
                                                                <option value="{{ $estu_previo->id }}">
                                                                    {{ $estu_previo->nom_estuprevios }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- <div class="col-sm-4 mb-3 hidden" id="additionalField9">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                N° Resolucion APROBACION METAS
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="num_reso_metas" name="num_reso_metas" value=""
                                                                autocomplete="off">
                                                        </div>
                                                    </div> --}}
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-user-graduate"></i>
                                                                Con Proyecto de investigación <span
                                                                    style="color: red">*</span>
                                                            </label>
                                                            <select id="con_proyecto" name="con_proyecto"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;" required>
                                                                <option>Seleccione</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4 mb-3 hidden" id="additionalFieldpi">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                Nombre proyecto Investigación
                                                            </label>
                                                            <input type="text" class="form-control" id="nom_proyecto"
                                                                name="nom_proyecto" value="" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12 card pl-4 pr-4 pt-4 shadow-sm mb-4"
                                                        style="background: linear-gradient(135deg, #ffb773, #f7deb0);">
                                                        <h5 class="mb-3"><i class="fas fa-map-marked-alt"></i> Lugar de
                                                            nacimiento</h5>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <label style="color: #683d08" for="fecha_nacimiento"
                                                                    class="form-label"><i class="fas fa-calendar-alt"></i>
                                                                    Fecha
                                                                    de Nacimiento:
                                                                    <span style="color: red">*</span>
                                                                </label>
                                                                <input type="date" class="form-control"
                                                                    id="fecha_nacimiento" name="fecha_nacimiento"
                                                                    value="" autocomplete="off" required>
                                                            </div>

                                                            <div class="col-sm-9">

                                                                <livewire:select-component />
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
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione su lengua materna</option>
                                                                @foreach ($lenguasmater as $item)
                                                                    <option value="{{ $item->id_lengua }}">
                                                                        {{ $item->nombre }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 mb-3">
                                                        <label style="color: #91560d" for="lengua_secundaria"
                                                            class="form-label"><i class="fas fa-language"></i> Lengua
                                                            Secundaria:</label>
                                                        <input type="text" class="form-control" id="lengua_secundaria"
                                                            name="lengua_secundaria" value="" autocomplete="off"
                                                            placeholder="Si no tienes dejar en blanco">
                                                    </div>

                                                    <div class="col-sm-3 mb-3">
                                                        <label style="color: #91560d" for="celular"
                                                            class="form-label"><i class="fas fa-mobile-alt"></i> N°
                                                            Celular:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="celular"
                                                            name="celular" maxlength="9" value=""
                                                            autocomplete="off" required>
                                                    </div>
                                                    <div class="col-sm-3 mb-3">
                                                        <label style="color: #91560d" for="celular"
                                                            class="form-label"><i class="fas fa-at"></i>
                                                            Correo electronico:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            id="correo_electronico" name="correo_electronico"
                                                            value="" autocomplete="off" required>
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
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>¿Tienes Alguna Discapacidad?</option>
                                                                <option value="1">SI</option>
                                                                <option value="0">NO</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mb-3 hidden" id="additionalField">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-table"></i>
                                                                Detalles de Discapacidad
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="tipo_discapacidad" name="tipo_discapacidad"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione su discapacidad</option>
                                                                @foreach ($discapacidad as $item)
                                                                    <option value="{{ $item->id_discapacidad }}">
                                                                        {{ $item->nombre_discapacidad }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 mb-3 hidden" id="additionalField2">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i
                                                                    class="fas fa-sort-numeric-up-alt"></i>
                                                                Numero conadis
                                                                <span style="color: red">* (OBLIGATORIO)</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="num_conadis"
                                                                name="num_conadis" value="" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12 card pl-4 pr-4 pt-4 shadow-sm mb-4"
                                                        style="background: linear-gradient(135deg, #b8ebfa, #6dc7df);">
                                                        <h5 class="mb-3"><i class="fas fa-house-user"></i> Domicilio
                                                            actual</h5>
                                                        <div class="row">
                                                            <div class="col-sm-3 mb-3">
                                                                <label style="color: #91560d" for="direccion"
                                                                    class="form-label"><i
                                                                        class="fas fa-map-marker-alt"></i> <i
                                                                        class="fas fa-home"></i> Direccion de Domicilio
                                                                    Actual:
                                                                    <span style="color: red">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="direccion"
                                                                    name="direccion" value="" autocomplete="off"
                                                                    required>
                                                            </div>
                                                            <div class="col-sm-9 mb-3">
                                                                <livewire:selectdomi />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-12 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d; font-size: 25px;"><i
                                                                    class="fas fa-eye"></i>
                                                                Seleccione el
                                                                programa de estudio (Carrera profesional) al cual va a
                                                                postular
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="opcion_carrera" name="opcion_carrera"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione su lengua materna</option>
                                                                @foreach ($programa_estu as $p_item)
                                                                    <option value="{{ $p_item->idcarreras }}">
                                                                        {{ $p_item->nombre_de_carrera }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="stepper.next()">SIGUIENTE
                                            <i class="fas fa-arrow-alt-circle-right"></i></button>
                                    </div>

                                    <div id="colegio-part" class="content" role="tabpanel"
                                        aria-labelledby="colegio-part-trigger">
                                        <div class="form-group">
                                            <div class="container">
                                                <div class="row">

                                                    <div class="col-sm-12 mb-3">
                                                        <label style="color: #91560d" for="lugar_colegio"
                                                            class="form-label"><i class="fas fa-chalkboard-teacher"></i>
                                                            Lugar del Colegio:
                                                            <span style="color: red">*</span>
                                                        </label>

                                                    </div>
                                                    <livewire:selectcole />


                                                    <div class="col-sm-6 mb-3">
                                                        <label style="color: #91560d" for="anio_promocion"
                                                            class="form-label"><i class="fas fa-sort-numeric-up"></i> Año
                                                            de Promoción:
                                                            <span style="color: red">*</span>
                                                        </label>
                                                        <select class="form-control" id="anio_promocion"
                                                            name="anio_promocion" required>
                                                            <option value="">Seleccione un año</option>
                                                            @for ($year = date('Y'); $year >= date('Y') - 30; $year--)
                                                                <option value="{{ $year }}">{{ $year }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>



                                                    <div class="col-sm-6 mb-3">
                                                        <div class="form-group">
                                                            <label style="color: #91560d"><i class="fas fa-list"></i> Tipo
                                                                de colegio
                                                                <span style="color: red">*</span>
                                                            </label>
                                                            <select id="tipo_colegio" name="tipo_colegio"
                                                                class="form-control select2 select2-danger"
                                                                data-dropdown-css-class="select2-danger"
                                                                style="width: 100%;">
                                                                <option>Seleccione el tipo de colegio</option>
                                                                @foreach ($tipo_cole as $item)
                                                                    <option value="{{ $item->idtipo_colegio }}">
                                                                        {{ $item->tipo_de_colegio }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="stepper.previous()"><i
                                                class="fas fa-arrow-alt-circle-left"></i> ATRAS</button>
                                        <button type="button" class="btn btn-primary" onclick="stepper.next()">SIGUIENTE
                                            <i class="fas fa-arrow-alt-circle-right"></i></button>
                                    </div>

                                    <div id="information-part" class="content" role="tabpanel"
                                        aria-labelledby="information-part-trigger">
                                        <div class="form-group">
                                            <div class="mb-1">
                                                <label style="color: #91560d" for="" class="form-label"><i
                                                        class="fas fa-portrait"></i> Imagen:</label>
                                                <input type="file" class="form-control" id="imagen" name="imagen"
                                                    onchange="previewImage(event, '#imgPreview')">
                                                <center><img id="imgPreview" width="414px" height="571px"></center>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="stepper.previous()"><i
                                                class="fas fa-arrow-alt-circle-left"></i> ATRAS</button>
                                        <button type="submit" class="btn btn-dark"> <i class="fas fa-save"></i>
                                            REGISTRAR </button>
                                    </div>

                            </div>
                        </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">

                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    @endif


@stop

@section('js')
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })

        document.getElementById('edad').addEventListener('input', function() {
            if (this.value < 1) {
                this.value = '';
            }
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
        document.getElementById('discapa_postu').addEventListener('change', function() {
            const additionalField = document.getElementById('additionalField');
            const tipoDiscapacidad = document.getElementById('tipo_discapacidad');

            const additionalField2 = document.getElementById('additionalField2');
            const num_conadis = document.getElementById('num_conadis');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField.classList.remove('hidden');
                tipoDiscapacidad.removeAttribute('disabled'); // Habilitar selección
                additionalField2.classList.remove('hidden');
                num_conadis.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField.classList.add('hidden');
                tipoDiscapacidad.value = ""; // Limpiar el valor
                tipoDiscapacidad.setAttribute('disabled', 'disabled'); // Deshabilitar selección

                additionalField2.classList.add('hidden');
                num_conadis.value = ""; // Limpiar el valor
                num_conadis.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('rebred').addEventListener('change', function() {
            const additionalField3 = document.getElementById('additionalField3');
            const num_reso_rebred = document.getElementById('num_reso_rebred');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField3.classList.remove('hidden');
                num_reso_rebred.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField3.classList.add('hidden');
                num_reso_rebred.value = ""; // Limpiar el valor
                num_reso_rebred.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('con_hijos').addEventListener('change', function() {
            const additionalField4 = document.getElementById('additionalField4');
            const cant_hijos = document.getElementById('cant_hijos');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField4.classList.remove('hidden');
                cant_hijos.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField4.classList.add('hidden');
                cant_hijos.value = ""; // Limpiar el valor
                cant_hijos.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('con_beca').addEventListener('change', function() {
            const additionalField5 = document.getElementById('additionalField5');
            const moda_beca = document.getElementById('moda_beca');

            const additionalField6 = document.getElementById('additionalField6');
            const num_reso_beca = document.getElementById('num_reso_beca');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField5.classList.remove('hidden');
                moda_beca.removeAttribute('disabled'); // Habilitar selección
                additionalField6.classList.remove('hidden');
                num_reso_beca.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField5.classList.add('hidden');
                moda_beca.value = ""; // Limpiar el valor
                moda_beca.setAttribute('disabled', 'disabled'); // Deshabilitar selección

                additionalField6.classList.add('hidden');
                num_reso_beca.value = ""; // Limpiar el valor
                num_reso_beca.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('con_trabajo').addEventListener('change', function() {
            const additionalField7 = document.getElementById('additionalField7');
            const tipo_trabajo = document.getElementById('tipo_trabajo');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField7.classList.remove('hidden');
                tipo_trabajo.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField7.classList.add('hidden');
                tipo_trabajo.value = ""; // Limpiar el valor
                tipo_trabajo.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('con_estudios').addEventListener('change', function() {
            const additionalField8 = document.getElementById('additionalField8');
            const estu_previos = document.getElementById('estu_previos');

            const additionalField9 = document.getElementById('additionalField9');
            const num_reso_metas = document.getElementById('num_reso_metas');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalField8.classList.remove('hidden');
                estu_previos.removeAttribute('disabled'); // Habilitar selección
                additionalField9.classList.remove('hidden');
                num_reso_metas.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalField8.classList.add('hidden');
                estu_previos.value = ""; // Limpiar el valor
                estu_previos.setAttribute('disabled', 'disabled'); // Deshabilitar selección

                additionalField9.classList.add('hidden');
                num_reso_metas.value = ""; // Limpiar el valor
                num_reso_metas.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });

        document.getElementById('con_proyecto').addEventListener('change', function() {
            const additionalFieldpi = document.getElementById('additionalFieldpi');
            const nom_proyecto = document.getElementById('nom_proyecto');

            if (this.value === "1") { // Si selecciona "Sí"
                additionalFieldpi.classList.remove('hidden');
                nom_proyecto.removeAttribute('disabled'); // Habilitar selección
            } else { // Si selecciona "No"
                additionalFieldpi.classList.add('hidden');
                nom_proyecto.value = ""; // Limpiar el valor
                nom_proyecto.setAttribute('disabled', 'disabled'); // Deshabilitar selección
            }
        });
    </script>


    @livewireScripts
@stop
