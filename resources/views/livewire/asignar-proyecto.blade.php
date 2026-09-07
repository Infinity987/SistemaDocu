<div>
    {{-- Alerta de Error Global --}}
    @if (session()->has('error_global'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> {{ session('error_global') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($vistaActual === 'bandeja')
        <!-- =========================================================
             VISTA 1: BANDEJA PRINCIPAL DE PROYECTOS
             ========================================================= -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">Bandeja de Proyectos de Investigación</h5>
                <button type="button" wire:click="abrirModalNuevoProyecto" class="btn btn-success btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Registrar Nuevo Proyecto</span>
                </button>
            </div>
            <div class="card-body">
                {{-- Buscador Principal --}}
                <div class="mb-3">
                    <input type="text" wire:model.live="searchBandeja" class="form-control" placeholder="Buscar por título de investigación...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 35%;">Título de Investigación</th>
                                <th style="width: 15%;">Tipo de Trámite</th>
                                <th style="width: 20%;">Carrera Profesional</th>
                                <th style="width: 15%;">Integrantes</th>
                                <th style="width: 10%; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proyectos as $proy)
                                <tr>
                                    <td>{{ $proy->id_proyecto }}</td>
                                    <td class="font-weight-bold text-dark">{{ $proy->titulo_investigacion }}</td>
                                    <td>
                                        <span class="badge {{ $proy->tipo_tramite == 2 ? 'badge-success' : 'badge-info' }}" style="font-size: 0.9rem; padding: 6px 12px;">
                                            {{ $proy->nombre_tramite_texto }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-dark d-block font-weight-bold" style="font-size: 0.85rem;">
                                            {{ $proy->nombre_carrera }}
                                        </small>
                                    </td>
                                    <td>
                                        @if(count($proy->integrantes_lista) > 0)
                                            <ul class="pl-3 mb-0" style="font-size: 0.85rem;">
                                                @foreach($proy->integrantes_lista as $nombreInt)
                                                    <li>{{ $nombreInt }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-danger font-italic" style="font-size: 0.85rem;">Sin integrantes agregados</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <button wire:click="gestionarProyecto({{ $proy->id_proyecto }})" class="btn btn-primary btn-circle btn-sm" title="Gestionar Alumnos y Autoridades">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No se encontraron proyectos registrados o que coincidan con la búsqueda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación Bootstrap --}}
                <div class="mt-3">
                    {{ $proyectos->links() }}
                </div>
            </div>
        </div>

        <!-- =========================================================
             MODAL: REGISTRAR NUEVO PROYECTO
             ========================================================= -->
        <div wire:ignore.self class="modal fade" id="modalNuevoProyecto" tabindex="-1" role="dialog" aria-labelledby="modalNuevoProyectoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold" id="modalNuevoProyectoLabel">
                            <i class="fas fa-folder-plus mr-2"></i>Registrar Proyecto de Investigación
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="guardarNuevoProyecto">
                        <div class="modal-body bg-light">
                            <div class="row">
                                {{-- Tipo de trámite --}}
                                <div class="col-md-6 form-group mb-3">
                                    <label class="font-weight-bold text-dark">1. Tipo de Trámite:</label>
                                    <select wire:model="tipo_tramite" class="form-control @error('tipo_tramite') is-invalid @enderror">
                                        <option value="">-- SELECCIONE EL TRÁMITE --</option>
                                        <option value="1">TRÁMITE DE BACHILLER</option>
                                        <option value="2">TRÁMITE DE TÍTULO PROFESIONAL</option>
                                    </select>
                                    @error('tipo_tramite') <span class="text-danger font-weight-bold mt-1 d-block" style="font-size: 0.9rem;">{{ $message }}</span> @enderror
                                </div>

                                {{-- Selección Obligatoria de Carrera --}}
                                <div class="col-md-6 form-group mb-3">
                                    <label class="font-weight-bold text-dark">2. Carrera / Escuela Profesional:</label>
                                    <select wire:model="id_carrera" class="form-control @error('id_carrera') is-invalid @enderror">
                                        <option value="">-- SELECCIONE LA CARRERA --</option>
                                        @foreach ($carrerasDisponibles as $carr)
                                            <option value="{{ $carr->idcarreras }}">{{ $carr->nombre_de_carrera }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_carrera') <span class="text-danger font-weight-bold mt-1 d-block" style="font-size: 0.9rem;">{{ $message }}</span> @enderror
                                </div>

                                {{-- Título de Investigación --}}
                                <div class="col-12 form-group mb-2">
                                    <label class="font-weight-bold text-dark">3. Título de Investigación (Completo):</label>
                                    <textarea wire:model="titulo_investigacion" class="form-control @error('titulo_investigacion') is-invalid @enderror" rows="4" placeholder="Escriba aquí el título completo del proyecto de investigación..."></textarea>
                                    @error('titulo_investigacion') <span class="text-danger font-weight-bold mt-1 d-block" style="font-size: 0.9rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-white d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success px-4 font-weight-bold">
                                <i class="fas fa-save mr-2"></i>Guardar Proyecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
        <!-- =========================================================
             VISTA 2: GESTIÓN INTEGRAL DEL PROYECTO SELECCIONADO
             ========================================================= -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between bg-dark text-white">
                <button wire:click="volverALaBandeja" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Volver a la Bandeja
                </button>
                <h5 class="m-0 font-weight-bold text-white">Ficha Técnica & Gestión de Proyecto</h5>
                <span class="badge badge-light">ID: {{ $proyectoIdSeleccionado }}</span>
            </div>
            
            <div class="card-body">
                {{-- Encabezado de Datos del Proyecto --}}
                <div class="p-3 mb-4" style="background-color: #f8f9fa; border-left: 5px solid #4e73df; border-radius: 4px;">
                    <span class="badge badge-danger mb-2 text-uppercase" style="font-size: 1rem; padding: 6px 12px;">
                        {{ $proyectoSeleccionado->nombre_tramite_texto }}
                    </span>
                    <h4 class="font-weight-bold text-gray-900 mb-1">{{ $proyectoSeleccionado->titulo_investigacion }}</h4>
                    <p class="text-muted mb-0 font-italic" style="font-size: 0.95rem;">
                        <strong>Estado de flujo actual:</strong> 
                        @if((int)$proyectoSeleccionado->estado == 1) REGISTRADO @else PROCESADO @endif
                    </p>
                </div>

                <!-- ==========================================
                     SECCIÓN A: GESTIÓN DE ALUMNOS (INTEGRANTES)
                     ========================================== -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h4 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-user-graduate mr-2"></i>1. Integrantes del Proyecto (Alumnos)
                        </h4>
                    </div>

                    {{-- Buscador de Alumnos --}}
                    <div class="col-xl-5 col-lg-6 mb-4">
                        <div class="card border-left-primary shadow-sm h-100 py-2">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-user-plus mr-1"></i> Añadir Integrante
                                </h6>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">Escribe el DNI del Alumno:</label>
                                    <div class="input-group">
                                        <input type="text" wire:model.live="searchDni" class="form-control form-control-lg text-center" placeholder="DNI de 8 dígitos" maxlength="8">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                    @if ($searchError)
                                        <small class="text-danger font-weight-bold mt-1 d-block">{{ $searchError }}</small>
                                    @endif
                                </div>

                                {{-- Tarjeta de Confirmación de Alumno --}}
                                @if ($alumnoEncontrado)
                                    <div class="card border-success bg-light mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <i class="fas fa-user-graduate fa-2x text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="font-weight-bold text-success mb-0">{{ $alumnoEncontrado['nombre_completo'] }}</h6>
                                                    <small class="text-dark d-block"><strong>DNI:</strong> {{ $alumnoEncontrado['dni'] }}</small>
                                                    <small class="text-muted d-block"><strong>Carrera:</strong> {{ $alumnoEncontrado['carrera'] }}</small>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-right">
                                                <button wire:click="agregarAlumnoAlProyecto" class="btn btn-success btn-block font-weight-bold">
                                                    <i class="fas fa-plus mr-1"></i> Confirmar y Agregar Alumno
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (session()->has('error_alumno'))
                                    <div class="alert alert-danger mt-2 font-weight-bold">{{ session('error_alumno') }}</div>
                                @endif
                                @if (session()->has('success_alumno'))
                                    <div class="alert alert-success mt-2 font-weight-bold">{{ session('success_alumno') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Lista de Alumnos Integrantes --}}
                    <div class="col-xl-7 col-lg-6 mb-4">
                        <div class="card border-left-info shadow-sm h-100 py-2">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-info mb-3">
                                    <i class="fas fa-users mr-1"></i> Alumnos Registrados
                                </h6>

                                @if (count($alumnosProyectoActual) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm table-hover align-middle">
                                            <thead class="bg-info text-white">
                                                <tr>
                                                    <th>Nombre Completo</th>
                                                    <th class="text-center" style="width: 25%;">DNI</th>
                                                    <th class="text-center" style="width: 20%;">Remover</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($alumnosProyectoActual as $ap)
                                                    <tr>
                                                        <td class="font-weight-bold text-dark p-2">{{ $ap->nombre_completo }}</td>
                                                        <td class="text-center p-2">{{ $ap->id_alumno }}</td>
                                                        <td class="text-center p-2">
                                                            <button wire:click="quitarAlumno({{ $ap->id_alumno_proyecto }})" class="btn btn-danger btn-sm btn-circle" title="Quitar de este proyecto">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-user-slash fa-2x mb-2 text-gray-400"></i>
                                        <p class="mb-0 font-italic">Este proyecto aún no tiene asignado ningún alumno.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     🚀 SECCIÓN B: GESTIÓN DE DOCENTES (AUTORIDADES)
                     ========================================== -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-success font-weight-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>2. Autoridades del Proyecto (Asesores & Jurados)
                        </h4>
                    </div>

                    {{-- Buscador de Docentes --}}
                    <div class="col-xl-5 col-lg-6 mb-4">
                        <div class="card border-left-success shadow-sm h-100 py-2">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success mb-3">
                                    <i class="fas fa-search-plus mr-1"></i> Asignar Asesor o Jurado
                                </h6>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">Escribe el DNI del Docente:</label>
                                    <div class="input-group">
                                        <input type="text" wire:model.live="searchDocenteDni" class="form-control form-control-lg text-center" placeholder="DNI del Docente" maxlength="8">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-success text-white"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                    @if ($searchDocenteError)
                                        <small class="text-danger font-weight-bold mt-1 d-block">{{ $searchDocenteError }}</small>
                                    @endif
                                </div>

                                {{-- Confirmación de Docente --}}
                                @if ($docenteEncontrado)
                                    <div class="card border-success bg-light mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="mr-3">
                                                    <i class="fas fa-user-tie fa-2x text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="font-weight-bold text-dark mb-0">{{ $docenteEncontrado['nombre'] }}</h6>
                                                    <small class="text-dark d-block"><strong>DNI / ID:</strong> {{ $docenteEncontrado['dni'] }}</small>
                                                    <small class="text-muted d-block"><strong>Correo:</strong> {{ $docenteEncontrado['correo'] }}</small>
                                                </div>
                                            </div>

                                            {{-- Seleccionar Rol del Docente --}}
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark mb-1">Selecciona su Rol en el Proyecto:</label>
                                                <select wire:model="id_rol_proyecto" class="form-control @error('id_rol_proyecto') is-invalid @enderror">
                                                    <option value="">-- ELEGIR ROL --</option>
                                                    <option value="1">ASESOR DE PROYECTO</option>
                                                    <option value="2">JURADO EVALUADOR</option>
                                                </select>
                                                @error('id_rol_proyecto') <span class="text-danger font-weight-bold d-block mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <button wire:click="agregarDocenteAlProyecto" class="btn btn-success btn-block font-weight-bold">
                                                <i class="fas fa-check-circle mr-1"></i> Asignar al Proyecto
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if (session()->has('error_docente'))
                                    <div class="alert alert-danger mt-2 font-weight-bold">{{ session('error_docente') }}</div>
                                @endif
                                @if (session()->has('success_docente'))
                                    <div class="alert alert-success mt-2 font-weight-bold">{{ session('success_docente') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Listado de Autoridades Asignadas --}}
                    <div class="col-xl-7 col-lg-6 mb-4">
                        <div class="card border-left-warning shadow-sm h-100 py-2">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-warning mb-3">
                                    <i class="fas fa-shield-alt mr-1"></i> Plana de Autoridades Registradas
                                </h6>

                                @if (count($autoridadesProyectoActual) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm table-hover align-middle" style="font-size: 0.9rem;">
                                            <thead class="bg-warning text-dark">
                                                <tr>
                                                    <th>Docente / Profesional</th>
                                                    <th class="text-center">Rol</th>
                                                    <th class="text-center">Asignación</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($autoridadesProyectoActual as $aut)
                                                    <tr class="{{ $aut->estado_asignado == 0 ? 'table-secondary text-muted' : '' }}">
                                                        <td class="p-2">
                                                            <div class="font-weight-bold">{{ $aut->nombre_completo }}</div>
                                                            <small class="text-muted d-block">Contacto: {{ $aut->celular }}</small>
                                                        </td>
                                                        <td class="text-center p-2">
                                                            <span class="badge {{ $aut->id_rol_proyecto == 1 ? 'badge-primary' : 'badge-dark' }}">
                                                                {{ $aut->id_rol_proyecto == 1 ? 'ASESOR' : 'JURADO' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center p-2">
                                                            <small class="d-block"><strong>Ini:</strong> {{ $aut->fecha_asignacion }}</small>
                                                            @if($aut->fecha_fin)
                                                                <small class="text-danger d-block"><strong>Fin:</strong> {{ $aut->fecha_fin }}</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center p-2">
                                                            @if ($aut->estado_asignado == 1)
                                                                <span class="badge badge-success">ACTIVO</span>
                                                            @else
                                                                <span class="badge badge-danger" title="Motivo: {{ $aut->motivo_cambio }}">INACTIVO</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center p-2">
                                                            @if ($aut->estado_asignado == 1)
                                                                <button onclick="confirmarBaja({{ $aut->id_autoridades_proyecto }})" class="btn btn-danger btn-circle btn-sm" title="Dar de baja o remover">
                                                                    <i class="fas fa-user-slash"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-secondary btn-circle btn-sm" disabled title="Ya dado de baja">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-user-shield fa-2x mb-2 text-gray-400"></i>
                                        <p class="mb-0 font-italic">Este proyecto no cuenta con asesores ni jurados registrados.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($vistaActual === 'gestionar' && $proyectoSeleccionado)
    <div class="mt-4 card p-3 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5>Generar Documento Oficial</h5>
                <p class="text-muted mb-0">Genera la resolución directoral correlativa con plantilla Word basada en este proyecto.</p>
            </div>
            
            <!-- Botón de Generación y Descarga -->
            <a href="{{ route('documentario.resoluciones.generar', $proyectoSeleccionado->id_proyecto) }}" 
               target="_blank" 
               class="btn btn-success btn-lg">
                <i class="fas fa-file-word"></i> Generar Resolución Word
            </a>
        </div>
    </div>
@endif

            </div>
        </div>
    @endif
</div>

{{-- Scripts e interactividad con Bootstrap Modals & SweetAlert --}}
@script
<script>
    $wire.on('abrir-modal-nuevo-proyecto', () => {
        $('#modalNuevoProyecto').modal('show');
    });

    $wire.on('cerrar-modal-nuevo-proyecto', () => {
        $('#modalNuevoProyecto').modal('hide');
    });

    // Cuadro de confirmación nativo para dar de baja autoridades de forma rápida y limpia
    window.confirmarBaja = function(id) {
        let motivo = prompt("Ingrese el motivo del cambio o baja de la autoridad:", "Cambio de plana docente / Reemplazo");
        if (motivo !== null && motivo.trim() !== "") {
            $wire.call('darDeBajaAutoridad', id, motivo);
        }
    }
</script>
@endscript