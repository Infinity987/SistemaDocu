<div>

@if($semestreActivo)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="alert alert-info mb-0">
        📅 Semestre activo: <strong>{{ $semestreActivo->año }}</strong> - <strong>{{ $semestreActivo->periodo }}</strong>
    </div>

     <button type="button" class="btn btn-info fw-bold shadow-sm" data-toggle="modal" data-target="#modalReporteGlobal" title="Reporte general">
            <i class="fas fa-chart-bar me-1"></i> Nomina de matricula
        </button>
    <button wire:click="exportarExcel" class="btn btn-success">
        📊 Exportar Excel General
    </button>

    
</div>
@endif
    <div class="form-group">
    <label for="carrera">Selecciona la carrera:</label>
    <select wire:change="handleCarreraChange($event.target.value)" class="form-control">
        <option value="">-- Seleccionar --</option>
        @foreach($carrera as $c)
            <option value="{{ $c->idcarreras }}">{{ $c->nombre_de_carrera }}</option>
        @endforeach
    </select>
</div>

@if($mallas && count($mallas))
    <div class="form-group mt-2">
        <label for="malla">Selecciona la malla curricular:</label>
        <select wire:model="id_malla" class="form-control">
            <option value="">-- Seleccionar --</option>
            @foreach($mallas as $malla)
                <option value="{{ $malla->id }}">{{ $malla->nombre }}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="mt-3">
    <button wire:click="cargarResumen" class="btn btn-warning">
        🔄 Ver resumen de matrícula general
    </button>
</div>

{{-- 🎯 Filtros antes del botón --}}
<div class="row mt-3">
    <div class="col-md-4">
        <label for="filtroCiclo">Filtrar por ciclo:</label>
        <select wire:model="filtroCiclo" class="form-control">
            <option value="">-- Todos --</option>
            @foreach($ciclosDisponibles as $ciclo)
                <option value="{{ $ciclo->idciclos }}">{{ $ciclo->nombre_ciclo }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="filtroTurno">Filtrar por turno:</label>
        <select wire:model="filtroTurno" class="form-control">
            <option value="">-- Todos --</option>
            @foreach($turnosDisponibles as $turno)
                <option value="{{ $turno->idturno }}">{{ $turno->nombre_turno }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="filtroTipoMatricula">Filtrar por tipo de matrícula:</label>
        <select wire:model="filtroTipoMatricula" class="form-control">
            <option value="">-- Todos --</option>
            @foreach($tiposMatriculaDisponibles as $tipo)
                <option value="{{ $tipo->idtipo_matricula }}">{{ $tipo->nombre_tipo_matricula }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- 🔘 Botón después de los filtros --}}
<div class="mt-3">
    <button wire:click="cargarResumen" class="btn btn-primary">
        🔄 Ver resumen de matrícula especifica
    </button>
</div>

    {{-- 📊 Resumen por ciclo/sección/turno --}}
    @if($resumen && count($resumen))
        <h5 class="mt-4">📊 Resumen por ciclo</h5>

      
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ciclo</th>
                    <th>Tipo de Matrícula</th>
                    <th>Sección</th>
                    <th>Turno</th>
                    <th>Alumnos Matriculados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen as $item)
                    <tr>
                        <td>{{ $item->nombre_ciclo }}</td>
                        <td>
                            @switch($item->idtipo_matricula)
                                @case(1) Regular @break
                                @case(2) Subsanación @break
                                @default Otro
                            @endswitch
                        </td>
                        <td>{{ $item->nombre_seccion }}</td>
                        <td>{{ $item->nombre_turno }}</td>
                        <td>{{ $item->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

 {{-- 📋 Tabla de alumnos --}}
@if($alumnosMatriculados && count($alumnosMatriculados))
    <h5 class="mt-4">🧑‍🎓 Alumnos matriculados</h5>
      <button type="button" class="btn btn-outline-danger fw-bold shadow-sm" wire:click="prepararCambioTurno">
    🔁 Cambiar turno por ciclo
</button>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>N°</th>
                <th>DNI</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Ciclo</th>
                <th>Sección</th>
                <th>Turno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnosMatriculados as $a)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $a->idpostulante }}</td>
                    <td>{{ $a->apellidos_pater_postulante }} {{ $a->apellidos_mater_postulante }}</td>
                    <td>{{ $a->nombres_postulante }}</td>
                    <td>{{ $a->ciclo_matricula }}</td>
                    <td>{{ $a->nombre_seccion }}</td>
                    <td>{{ $a->nombre_turno }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" wire:click="abrirModal('{{ $a->idpostulante }}')"
>
                            📘 Ver cursos
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($id_malla)
    <div class="alert alert-warning mt-3">
        No hay alumnos matriculados que coincidan con los filtros aplicados.
    </div>
@endif

{{-- 📘 Modal de cursos por alumno --}}
<div wire:ignore.self class="modal fade" id="modalCursos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
    📘 Cursos matriculados <br>
    <small class="text-muted">👤 {{ $nombreAlumnoModal }}</small>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @php
                    $cursos = $cursosAgrupados[$alumnoSeleccionado] ?? collect();
                @endphp

                @if($cursos->count())
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Créditos</th>
                                <th>Horas</th>
                                <th>Ciclo</th>
                                <th>Sección</th>
                                <th>Turno</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cursos as $c)
                                <tr>
                                    <td>{{ $c->nombre_curso }}</td>
                                    <td>{{ $c->credito }}</td>
                                    <td>{{ $c->horas }}</td>
                                    <td>{{ $c->ciclo_matricula }}</td>
                                     <td>{{ $a->nombre_seccion }}</td>
                    <td>{{ $a->nombre_turno }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">Este alumno no tiene cursos registrados.</div>
                @endif
                <hr>

               

<div class="mt-3">
    <label for="codigoBoletaEditar" class="form-label">✏️ Código de boleta</label>
    <input type="text" wire:model.defer="codigoBoletaEditar" class="form-control" placeholder="Ingrese nuevo código de boleta">
</div>

@if($codigoBoletaEditar)
    <div class="alert alert-secondary mt-2">
        Código actual: <strong>{{ $codigoBoletaEditar }}</strong>
    </div>
@endif

<div class="mt-3">
    <label for="turnoEditar" class="form-label">🔁 Cambiar turno</label>
    <select wire:model="turnoEditar" class="form-control">
        <option value="">-- Seleccionar turno --</option>
        @foreach($todosLosTurnos as $t)
            <option value="{{ $t->idturno }}">{{ $t->nombre_turno }}</option>
        @endforeach
    </select>
</div>

<div class="mt-3 text-end">
    <button wire:click="actualizarBoleta" class="btn btn-success">
        💾 Guardar boleta
    </button>
</div>
            </div>
        </div>
    </div>
</div>



    {{-- 📌 Totales por tipo de matrícula --}}
    @if($resumen && count($resumen))
   
@elseif($id_malla)
    <!-- ⚠️ Mostrar mensaje solo si no hay datos -->
 
@endif


    <!-- Modal de Reporte de acta por matricula -->
            <div class="modal fade" id="modalReporteGlobal" tabindex="-1" role="dialog" aria-hidden="true"
             wire:ignore>
               
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg rounded">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">📄 Generar Nomina de matricula</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body bg-light">
                            <form action="{{ route('matricula_proceso.semestrepdf') }}" method="POST"
                                enctype="multipart/form-data" target="_blank">
                                @csrf

                                <div class="p-3 bg-white border rounded">
                                    <livewire:nominadematriculareporte />

                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-pdf"></i> Generar PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer bg-secondary text-white justify-content-between">
                            <small class="text-white">Verifica todos los campos antes de generar el Acta de matricula.</small>
                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div wire:ignore.self class="modal fade" id="modalCambioTurnoGlobal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">🔁 Cambio masivo de turno por ciclo</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body bg-light">
                <div class="p-3 bg-white border rounded">
                    <div class="form-group mb-3">
                        <label for="cicloCambioTurno">Ciclo:</label>
                        <select wire:model="cicloCambioTurno" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach($ciclosDisponibles as $c)
                                <option value="{{ $c->idciclos }}">{{ $c->nombre_ciclo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
    <label for="tipoMatriculaCambio">Tipo de matrícula:</label>
    <select wire:model="tipoMatriculaCambio" class="form-control">
        <option value="">-- Todos --</option>
        @foreach($tiposMatriculaDisponibles as $tipo)
            <option value="{{ $tipo->idtipo_matricula }}">{{ $tipo->nombre_tipo_matricula }}</option>
        @endforeach
    </select>
</div>

                    <div class="form-group mb-3">
                        <label for="turnoActual">Turno actual:</label>
                        <select wire:model="turnoActual" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach($turnosDisponibles as $t)
                                <option value="{{ $t->idturno }}">{{ $t->nombre_turno }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="turnoNuevo">Nuevo turno:</label>
                       <select wire:model="turnoNuevo" class="form-control">
    <option value="">-- Seleccionar --</option>
    @foreach($todosLosTurnos as $t)
        <option value="{{ $t->idturno }}">{{ $t->nombre_turno }}</option>
    @endforeach
</select>
                    </div>

                    @if (session()->has('error'))
                        <div class="alert alert-warning">
                            {{ session('error') }}
                        </div>
                    @elseif (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="text-right mt-4">
                        <button wire:click="cambiarTurnoMasivo" class="btn btn-danger">
                            💾 Aplicar cambio
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-secondary text-white justify-content-between">
                <small class="text-white">Este cambio afectará a todos los alumnos del ciclo seleccionado.</small>
                <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

            


<script>
    window.addEventListener('mostrar-modal-cursos', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalCursos'));
        modal.show();
    });
</script>

<script>
    window.addEventListener('abrir-modal-cambio-turno', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalCambioTurnoGlobal'));
        modal.show();
    });
</script>

<script>
    window.addEventListener('cerrar-modal-cambio-turno', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCambioTurnoMasivo'));
        modal.hide();
    });
</script>





</div>