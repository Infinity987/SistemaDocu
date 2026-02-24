<div>
    {{-- 🔍 Búsqueda por DNI --}}
    <input type="text" wire:model="dni" placeholder="Ingrese DNI del alumno" class="form-control mb-2">
    <button wire:click="buscar" class="btn btn-primary">Buscar historial</button>

    {{-- 🧾 Mensajes --}}
    @if (session('error'))
        <div class="alert alert-danger mt-2">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    {{-- 👤 Datos del alumno --}}
    @if ($alumno)
        <h5 class="mt-3">Alumno: {{ $alumno->nombres_postulante }} {{ $alumno->apellidos_pater_postulante }} {{ $alumno->apellidos_mater_postulante }}</h5>
        <p>Ciclo actual: {{ $cicloActual }}</p>

        @if (is_null($cicloActual))
    <div class="alert alert-info">
        ℹ️ Este alumno no tiene matrícula registrada. Se mostrarán todos los cursos disponibles según su malla curricular para iniciar el historial académico.
    </div>
@endif

        {{-- 🎯 Selección de ciclo, semestre y tipo --}}
     <div class="card mb-3">
    <div class="card-header bg-light fw-bold">
        🎯 Parámetros de matrícula
    </div>
    <div class="card-body row g-3">
        <div class="col-md-3">
            <label class="form-label">🌀 Ciclo</label>
            <select wire:model="cicloSeleccionado" class="form-select">
                <option value="">-- Seleccione ciclo --</option>
               @php
    $maxCiclo = $cicloActual ?? 10; // Mostrar hasta ciclo 10 si no hay matrícula
@endphp

@for ($i = 1; $i <= $maxCiclo; $i++)
    <option value="{{ $i }}">Ciclo {{ $i }}</option>
@endfor
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">🕒 Turno</label>
            <select wire:model="turnoSeleccionado" class="form-select">
                <option value="">-- Seleccione turno --</option>
                <option value="1">Mañana</option>
                <option value="2">Tarde</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">📅 Semestre académico</label>
            <select wire:model="semestreSeleccionado" class="form-select">
                <option value="">-- Seleccione semestre --</option>
                @foreach ($semestresDisponibles as $sem)
                    <option value="{{ $sem['id'] }}">{{ $sem['nombre'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">📘 Tipo de matrícula</label>
            <select wire:model="tipoMatriculaSeleccionado" class="form-select">
                <option value="">--</option>
                <option value="1">Regular</option>
                <option value="2">Subsanación</option>
            </select>
        </div>

        <div class="col-md-3">
    <label class="form-label">🔖 Código de boleta (opcional)</label>
    <input type="text" wire:model.defer="codigoBoleta" class="form-control" placeholder="Ej: BOL-2025-001">
    @if ($codigoBoleta === '')
        <small class="text-muted">⚠️ Si se deja vacío, se almacenará como <strong>null</strong>.</small>
    @endif
</div>
    </div>
</div>


<div class="text-end mb-3">
    <button wire:click="cargarCursosDelCiclo" class="btn btn-info">
        🔍 Mostrar cursos del ciclo seleccionado
    </button>
</div>

@if (!empty($resumenCiclo) && isset($resumenCiclo['total']))
    <div class="alert alert-secondary">
        <strong>Resumen del ciclo {{ $cicloSeleccionado }}:</strong><br>
        Total de cursos: {{ $resumenCiclo['total'] }} |
        Ya registrados: {{ $resumenCiclo['registrados'] }} |
        Subsanables: {{ $resumenCiclo['subsanables'] }} |
        Disponibles para registrar: {{ $resumenCiclo['disponibles'] }}
    </div>
@endif

        {{-- 📚 Cursos del ciclo seleccionado --}}
        @if ($cicloSeleccionado && count($cursosFiltrados))
            <div class="alert alert-info">
                Mostrando cursos del ciclo <strong>{{ $cicloSeleccionado }}</strong>
            </div>

            

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Curso</th>
                        <th>Créditos</th>
                        <th>Nota</th>
                        <th>Estado</th>
                        <th>estado 2</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cursosFiltrados as $index => $curso)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $curso->nombre_curso }}</td>
                            <td>{{ $curso->credito }}</td>
                           <td>
    @if ($curso->bloqueado)
        <input type="number" class="form-control form-control-sm" value="{{ $curso->nota_registrada }}" disabled>
    @else
        <input type="number" min="0" max="20"
            wire:model="notasPorCiclo.{{ $curso->idcursos }}.nota"
            class="form-control form-control-sm">
    @endif
</td>
<td>
    @if ($curso->bloqueado)
        <span class="badge bg-secondary">{{ $curso->estado_registrado == 1 ? 'Aprobado' : 'Desaprobado' }}</span>
    @else
       <select wire:model="notasPorCiclo.{{ $curso->idcursos }}.estado" class="form-select form-select-sm">
    <option value="">--</option>
    <option value="1">✅ Aprobado</option>
    <option value="0">❌ Desaprobado</option>
     <option value="2">📚 Llevando(para agregar licencia)</option>
</select>
    @endif
</td>
<td>
    @if ($curso->bloqueado)
        <span class="text-muted">✔️ Ya registrado en este semestre</span>
    @elseif ($tipoMatriculaSeleccionado == 2 && $curso->estado_registrado == 0)
        <span class="text-warning">⚠️ Subsanación permitida</span>
    @else
        <span class="text-info">🆕 Disponible para registro</span>
    @endif
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- 💾 Botón para guardar todos los cursos seleccionados --}}
            <div class="text-end">
                <button wire:click="guardarNotasPorCiclo" class="btn btn-success">
                    💾 Guardar notas del ciclo {{ $cicloSeleccionado }}
                </button>
            </div>
        @elseif ($cicloSeleccionado)
            <div class="alert alert-warning">No hay cursos para el ciclo seleccionado.</div>
        @endif
    @endif
</div>