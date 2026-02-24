<div>
<div class="container mt-2">
    <div class="row">
         <div class="col-sm-4 mb-3">
      <div class="form-group">
        <label for="proceso"><i class="fas fa-project-diagram"></i> AÑO:</label>
        <select wire:model="selectedAño" wire:change="handleAñoChange($event.target.value)" id="año"
          class="form-control">
          <option value="">Seleccione un año</option>
          @foreach ($años as $año)
            <option value="{{ $año->año_de_inicio }}">{{ $año->año_de_inicio}}</option>
          @endforeach
        </select>
      </div>
    </div>


@if (!is_null($selectedAño))
      <div class="col-sm-4 mb-3">
        <div class="form-group">
          <label for="proceso">carrera:</label>
          <select wire:model="selectedCarreras" wire:change="handleCarreraChange($event.target.value)" id="carrera"
            name="carrera" class="form-control">
            <option value="">Seleccione una carrera</option>
            @foreach ($carreras as $carrera)
              <option value="{{ $carrera->idmalla_curricular }}">{{ $carrera->nombre_de_carrera }}</option>
            @endforeach
          </select>
        </div>
      </div>
    @endif

    @if (!is_null($selectedCarreras))
  <div class="col-12 mb-3">
    <div class="form-group">

      <table class="table" id="tablaplanestu" wire:key="tabla-{{ $selectedCarreras }}">
        <thead>
          <tr>
            <th>Curso</th>
            <th>Horas</th>
            <th>Créditos</th>
            <th>editar</th>
            <th>precurso</th>
            <th>competencias</th>
            <th>Formacion</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($plan_estudios as $ciclo => $cursos)
            <tr class="table-primary">
              <td colspan="7"><strong>Ciclo {{ $ciclo }}</strong></td>
            </tr>
            @foreach ($cursos as $curso)
              <tr>
                <td>{{ $curso->nombre_curso }}</td>
                <td>{{ $curso->horas }}</td>
                <td>{{ $curso->credito }}</td>
                 <td width="5px">
                                    <a type="button" class="btn btn-warning btn-sm m-1 verModel"
                                     data-nombrecurso="{{ $curso->nombre_curso }}"
                                         data-horacurso="{{ $curso->horas }}"
                                          data-creditocurso="{{ $curso->credito }}"
                                           data-idcurso="{{ $curso->idcursos }}"
                                           data-bs-toggle="modal"
                                           data-bs-target="#verDetalle">
                                           <i class="fas fa-edit"></i> </a>
                                </td>
                                <td width="5px">
    <a type="button"
       class="btn btn-info btn-sm"
       wire:click="seleccionarCurso({{ $curso->idcursos }})"
       onclick="abrirModalPrecurso()">
       <i class="fas fa-link"></i> Precurso
    </a>
    @if (in_array($curso->idcursos, $cursosConPrecurso))
        <span class="badge bg-success ms-1">Asignado</span>
    @endif
</td>
<td width="5px">
  <a type="button"
     class="btn btn-success btn-sm px-3 py-2 text-start position-relative w-100"
     wire:click="asignarCompetencias({{ $curso->idcursos }})"
     onclick="abrirModalCompetencias()"
     style="line-height: 1.2;">
      <div class="d-flex flex-column align-items-start">
        <span class="fw-semibold"><i class="fas fa-tasks me-1"></i> Competencias</span>

        @if (in_array($curso->idcursos, $cursosConCompetencias))
          <span class="mt-1 px-2 py-0 bg-danger text-white rounded-pill small shadow-sm" style="font-size: 0.65rem;">
            ✔ Asignadas
          </span>
        @endif
      </div>
  </a>
</td>
<td width="5px">
 <button type="button"
        class="btn btn-outline-secondary btn-sm"
        wire:click="asignarFormacion({{ $curso->idcursos }})"
        onclick="abrirModalFormacion()">
  <i class="fas fa-book-open" title="Asignar formación"></i>
</button>
@if (in_array($curso->idcursos, $cursosConFormacion))
  <span class="badge bg-warning ms-1 text-dark" style="font-size: 0.65rem;">
    ✔ Formación asignada
  </span>
@endif
</td>

                                                        

              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
@endif

    </div>
</div>

<div wire:ignore.self class="modal fade" id="modalFormacion" tabindex="-1" aria-labelledby="modalFormacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark text-light">
        <h5 class="modal-title"><i class="fas fa-book-open"></i> Asignar formación al curso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label for="formacion">Tipo de formación:</label>
        <select wire:model="formacionSeleccionada" class="form-control">
          <option value="">Seleccione una opción</option>
          <option value="FG">Formación General</option>
          <option value="FE">Formación Específica</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" wire:click="guardarFormacion">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal en la Blade principal -->
<div wire:ignore.self class="modal fade" id="modalPrecurso" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
       <div class="modal-header">
          <h5 class="modal-title" id="verDetalleLabel"><i class="fas fa-sign-in-alt"></i> Detalles del curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      
    @if (!empty($plan_estudios))
 <div class="modal-body">
  <label><strong>Seleccione cursos como prerrequisitos:</strong></label>
  <div class="table-responsive mt-2">
    <table class="table table-bordered table-sm">
      <thead>
        <tr>
          <th>Ciclo</th>
          <th>Curso</th>
          <th>Seleccionar</th>
        </tr>
      </thead>
      <tbody>
       @foreach ($plan_estudios as $ciclo => $cursos)
    @php
        // Filtra cursos del ciclo actual que sean anteriores
        $cursosFiltrados = collect($cursos)->filter(fn($curso) => $curso->idciclos < $cicloCursoSeleccionado);
    @endphp

    @if ($cursosFiltrados->isNotEmpty())
        <tr class="table-primary">
            <td colspan="3"><strong>Ciclo {{ $ciclo }}</strong></td>
        </tr>

        @foreach ($cursosFiltrados as $curso)
            @php
                $yaAsignado = in_array($curso->idcursos, $precursoSeleccionados);
            @endphp
            <tr>
                <td>{{ $ciclo }}</td>
                <td>
                    <span class="{{ $yaAsignado ? 'fw-bold' : '' }}">
                        {{ $curso->nombre_curso }}
                    </span>
                </td>
                <td>
                    <input type="checkbox"
                           wire:model="precursoSeleccionados"
                           value="{{ $curso->idcursos }}"
                           {{ $yaAsignado ? 'checked' : '' }}>
                </td>
            </tr>
        @endforeach
    @endif
@endforeach


      </tbody>
    </table>
    @if ($plan_estudios->flatMap(fn($c) => $c)->where('idciclos', '<', $cicloCursoSeleccionado)->isEmpty())
    <div class="alert alert-warning mt-2">
        No hay cursos anteriores que puedan ser prerrequisitos para este curso.
    </div>
@endif

  </div>

  <button wire:click="guardarPrecurso({{ $cursoIdSeleccionado }})"
          class="btn btn-primary mt-3">
    Guardar prerrequisitos
  </button>
</div>
@else
  <p class="text-muted">Seleccione una carrera para mostrar cursos disponibles.</p>
@endif
    </div>
  </div>
</div>

<!-- Modal para asignar competencias -->
<div wire:ignore.self class="modal fade" id="modalCompetencias" tabindex="-1" aria-labelledby="modalCompetenciasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
       <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-tasks"></i> Asignar competencias al curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @if (!empty($competenciasDisponibles))
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>Competencia</th>
                  <th>Descripción</th>
                  <th>Seleccionar</th>
                </tr>
              </thead>
             <tbody>
  @foreach ($competenciasDisponibles as $dominio => $competencias)
    <tr class="table-primary">
      <td colspan="3"><strong>{{ $dominio }}</strong></td>
    </tr>
    @foreach ($competencias as $compe)
      <tr>
        <td>{{ $compe->competencia }}</td>
        <td>{{ $compe->descripcion }}</td>
        <td>
          <input type="checkbox" wire:model="competenciasSeleccionadas" value="{{ $compe->idcompetencias }}">
        </td>
      </tr>
    @endforeach
  @endforeach
</tbody>
            </table>
          </div>
          <button class="btn btn-primary mt-2" wire:click="guardarCompetencias">Guardar Competencias</button>
        </div>
        @else
          <div class="modal-body">
            <p class="text-muted">No hay competencias disponibles para esta malla.</p>
          </div>
        @endif
    </div>
  </div>
</div>


</div>