
<div>
  {{-- Buscador por DNI --}}
  <div class="mb-3 d-flex">
    <input
      type="text"
      wire:model="dni"
      class="form-control me-2"
      placeholder="Escribe el DNI del alumno..."
    />
    <button wire:click="fetchMatriculas" class="btn btn-primary">
      Buscar
    </button>
  </div>

  @if($alumnoCarrera)
  <div class="alert alert-secondary">
    🎓 <strong>Carrera:</strong> {{ $alumnoCarrera }}
  </div>
@endif

  @php
    $matriculasPorSemestre = collect($matriculas)
      ->groupBy('idsemestre_academico');
  @endphp

  {{-- Listado de Matrículas --}}
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      Matrículas Registradas
    </div>
    <div class="card-body p-0">
      @if($matriculasPorSemestre->isEmpty())
        <div class="p-3">
          <div class="alert alert-info">
            @if(! $dni)
              Ingresa un DNI para buscar…
            @else
              No se encontraron matrículas para el DNI {{ $dni }}.
            @endif
          </div>
        </div>
      @else
        @foreach($matriculasPorSemestre as $semId => $group)
      

          @php
            $first     = $group->first();
            $anio      = $first->año     ?? '—';
            $periodo   = $first->periodo ?? '—';
            $tipoLabel = $first->idtipo_matricula === 1
              ? 'Regular'
              : ($first->tipo_ciclo === 3 ? 'Recuperación' : 'Subsanacion');
          @endphp
            <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
  <h5 class="mb-0">
    {{ $anio }} - {{ $periodo }}
    <small class="text-muted">({{ $tipoLabel }})</small>
  </h5>

</div>

        

          <table class="table table-striped table-hover mb-4">
            <thead class="table-dark text-center">
              <tr>

   
    <th>DNI</th>
<th>Apellidos</th>
<th>Nombres</th>
    <th>Fecha</th>
    <th>Ciclo</th>
    <th>Tipo</th>
    <th>Créditos</th>
    <th>Semestre</th>
    <th>Acciones</th>
    <th>Acciones2</th>
  </tr>

            </thead>
            <tbody class="align-middle text-center">
              @foreach($group as $m)
                @php
                  $tipoFila = $m->idtipo_matricula === 1
                    ? 'Regular'
                    : ($m->tipo_ciclo === 3 ? 'Recuperación' : 'Subsanacion');
                  $badgeCls = $m->idtipo_matricula === 1 ? 'success' : 'warning';
                @endphp
                <tr>
                  
                  <td>{{ $dni }}</td>
<td>{{ $alumnoApellidos }}</td>
<td>{{ $alumnoNombres }}</td>
                  <td>{{ \Carbon\Carbon::parse($m->fecha_matricula)->format('Y-m-d') }}</td>
                <td>{{ $m->nombre_ciclo ?? $m->ciclo_matricula }}</td>
                  <td>
                    <span class="badge bg-{{ $badgeCls }} text-white">
                      {{ $tipoFila }}
                    </span>
                  </td>
                  <td>{{ $m->total_credito }}</td>
                  <td>{{ $m->año }} - {{ $m->periodo }}</td>
                  <td>
                  
                  <button wire:click="Vercursos({{ $m->idmatricula }})" class="btn btn-sm btn-claro">
  <i class="fas fa-eye"></i> Ver Cursos
</button>
<form method="POST" action="{{ route('ruta.pdf', ['idSemestre' => $semId]) }}" target="_blank">
  @csrf
  <input type="hidden" name="dni" value="{{ $dni }}">
  <button type="submit" class="btn btn-sm btn-institucional">
    <i class="fas fa-file-pdf"></i> Ver PDF consolidado
  </button>
</form>

@php
    $tieneSubsanacion = $group->contains(fn($m) => $m->idtipo_matricula === 2);
@endphp

@if($tieneSubsanacion)
    <form method="POST" action="{{ route('ruta.subsanacionpdf', ['idSemestre' => $semId]) }}" target="_blank" class="mt-2">
        @csrf
        <input type="hidden" name="dni" value="{{ $dni }}">
        <button type="submit" class="btn btn-sm btn-warning">
            <i class="fas fa-file-pdf"></i> Ver PDF de Subsanación
        </button>
    </form>
@endif


                  </td>
                  <td>
                    <button wire:click="solicitarEliminacion({{ $m->idmatricula }})" class="btn btn-sm btn-danger">
    <i class="fas fa-trash-alt"></i>
</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endforeach
      @endif
    </div>
  </div>

 
  {{-- Modal: Ver Cursos de la Matrícula --}}
@if($coursesModalOpen)
  <div class="modal fade show d-block" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
  📘 Cursos de la Matrícula #{{ $currentMatriculaId }}
</h5>
          <button
            type="button"
            class="btn-close"
            wire:click="closeModal"
          ></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
  <div class="card mb-3 shadow-sm">
  <div class="card-body py-2 px-3" style="background-color: #e9af5e;">
    <p class="mb-1">
      🧑 <strong>Alumno:</strong> {{ $alumnoApellidos }} {{ $alumnoNombres }}
    </p>
    <p class="mb-0">
      🌀 <strong>Ciclo:</strong> {{ $cicloActualNombre }}
    </p>
  </div>
</div>
</div>
          @if($selectedCourses->isEmpty())
            <p>No hay cursos registrados en esta matrícula.</p>
          @else
            <table class="tabla-cursos-modal">
              <thead class="table-light text-center">
                <tr>
                  <th>Curso</th>
                  <th>Créditos</th>
                  <th>Nota 1</th>
                  <th>Nota 2</th>
                  <th>Nota 3</th>
                  <th>Nota Final</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody class="align-middle text-center">
                @foreach($selectedCourses as $curso)
                  <tr>
                    <td>{{ $curso->nombre_curso }}</td>
                    <td>{{ $curso->credito }}</td>
                 <td>{{ $calificaciones[$curso->idCalificaciones1] ?? '—' }}</td>
<td>{{ $calificaciones[$curso->idCalificaciones2] ?? '—' }}</td>
<td>{{ $calificaciones[$curso->idCalificaciones3] ?? '—' }}</td>
<td>{{ $curso->total ?? '—' }}</td>
                    <td>
  @switch($curso->estado_nota)
    @case(1)
      <span class="badge bg-success fw-bold rounded-pill">✅ Aprobado</span>
      @break

    @case(0)
      <span class="badge bg-danger">❌ Desaprobado</span>
      @break

    @case(2)
      <span class="badge bg-warning text-dark">📚 En curso</span>
      @break

    @case(3)
      <span class="badge bg-primary">📄 Guardado por licencia</span>
      @break

    @default
      <span class="badge bg-secondary">Estado desconocido</span>
  @endswitch
</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
        <div class="modal-footer">
          <button
            class="btn btn-secondary"
            wire:click="closeModal"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
@endif


@if($confirmarEliminacionModal)
<div class="modal fade show d-block" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">⚠️ Confirmar eliminación</h5>
                <button type="button" class="btn-close" wire:click="$set('confirmarEliminacionModal', false)"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta matrícula? Esta acción no se puede deshacer.</p>

                @if($datosMatriculaAEliminar)
                <div class="alert alert-warning mt-3">
                    <p class="mb-1">🧾 <strong>ID Matrícula:</strong> {{ $datosMatriculaAEliminar->idmatricula }}</p>
                    <p class="mb-1">📅 <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($datosMatriculaAEliminar->fecha_matricula)->format('Y-m-d') }}</p>
                    <p class="mb-1">🌀 <strong>Ciclo:</strong> {{ $datosMatriculaAEliminar->nombre_ciclo }}</p>
                    <p class="mb-1">📚 <strong>Tipo:</strong>
                        @switch($datosMatriculaAEliminar->idtipo_matricula)
                            @case(1) Regular @break
                            @case(2) Subsanación @break
                            @default Otro
                        @endswitch
                    </p>
                    <p class="mb-0">📆 <strong>Semestre:</strong> {{ $datosMatriculaAEliminar->año }} - {{ $datosMatriculaAEliminar->periodo }}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" wire:click="$set('confirmarEliminacionModal', false)">Cancelar</button>
                <button class="btn btn-danger" wire:click="eliminarMatricula">Eliminar definitivamente</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
</div>