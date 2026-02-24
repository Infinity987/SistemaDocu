<div>
<div class="container mt-3">

    {{-- Estado del semestre --}}
    @if($semestreActivo && $semestreActivo->estado_matricula == 1 && ! $matriculaCerrada)
        <div class="alert alert-success">
            🗓️ <strong>Semestre:</strong> {{ $semestreActivo->año }} {{ $semestreActivo->periodo }}<br>
            📅 <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($semestreActivo->fecha_ini_matricula)->format('d/m/Y') }}<br>
            📅 <strong>Fin:</strong> {{ \Carbon\Carbon::parse($semestreActivo->fecha_fin_matricula)->format('d/m/Y') }}<br>
            ✅ La matrícula está abierta.
        </div>

        @if($diasRestantes > 5)
            <div class="alert alert-success">⏳ Quedan <strong>{{ $diasRestantes }}</strong> días para matricularse.</div>
        @elseif($diasRestantes <= 5 && $diasRestantes > 0)
            <div class="alert alert-warning">⚠️ Faltan <strong>{{ $diasRestantes }}</strong> días para matricularse.</div>
        @elseif($diasRestantes === 0)
            <div class="alert alert-warning">⚠️ Hoy es el último día para matricularse.</div>
        @endif
    @else
        <div class="alert alert-danger">⛔ El proceso de matrícula ya está cerrado.</div>
    @endif

    {{-- Búsqueda de alumno --}}
    <div class="mb-4">
        <h5 class="text-primary fw-bold"><i class="bi bi-search"></i> Buscar alumno</h5>
        <input
            type="text"
            wire:model.live.debounce.500ms="search"
            class="form-control"
            placeholder="Buscar por DNI o apellidos"
        />
    </div>

   @if(empty($search))
    <div class="alert alert-info">Empieza a escribir para buscar un alumno.</div>
@elseif(count($alumnos) === 1)
    @php $alumno = $alumnos[0]; @endphp

        {{-- Estado de licencia y reincorporación --}}
        @if($licenciaActiva && $semestreFinLicencia && !$reincorporacion)
            <div class="alert alert-info">
                📝 Este alumno tiene una <strong>licencia activa</strong> hasta el semestre 
                <strong>{{ $licencia->Nombre_semestre_fin }}</strong>.<br>
                📄 <strong>Resolución:</strong> {{ $licencia->resolucion_licencia ?? 'No registrada' }}<br>
                🗒️ <strong>Motivo:</strong> {{ $licencia->motivo_licencia ?? 'No registrado' }}<br>
                ⚠️ Solo puede matricularse en cursos de <strong>subsanación</strong> durante este periodo.
            </div>
        @endif

        @if($licenciaActiva && $reincorporacion)
            <div class="alert alert-success">
                ✅ Este alumno tenía una licencia activa, pero ya se reincorporó en el semestre 
                <strong>{{ $reincorporacion->semestre_reincorporacion }}</strong>.<br>
                📄 Resolución de reincorporación: {{ $reincorporacion->resolucion_reincorporacion ?? 'No registrada' }}
            </div>
        @endif

        @if(!$licenciaActiva && !$reincorporacion && $licencia)
            <div class="alert alert-warning">
                ⚠️ Este alumno terminó su licencia en el semestre 
                <strong>{{ $licencia->Nombre_semestre_fin }}</strong>, pero aún no ha sido reincorporado.
                Debe registrar una reincorporación para poder matricularse.
            </div>
        @endif

        {{-- Advertencia por aprobación baja --}}
        @if($aprobacionBaja && !$licenciaActiva && $tipoCiclo !== 3)
            <div class="alert alert-warning">
                ⚠️ Aprobaste con menos del 75% de tus créditos en el ciclo anterior. Debes matricularte en tu ciclo regular para poder solicitar una licencia y no perder tu rango de estudiante.
            </div>
        @endif

        {{-- Cursos sin docente --}}
        @if(!$licenciaActiva && $tipoCiclo !== 3 && $cursosRegularesSinDocente && !$tieneMatriculaRegular && count($cursosRegularesSinDocente))
            <div class="alert alert-danger">
                ⚠️ Los siguientes cursos regulares no tienen docente asignado. No podrás matricularte hasta que se asignen:
                <ul class="mt-2 mb-0">
                    @foreach($cursosRegularesSinDocente as $curso)
                        <li><strong>{{ $curso->nombre_curso }}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($cursosSubsanacion && $cursosSubsanacion->count())
            <div class="alert alert-primary mt-3">
                ⚠️ Este alumno tiene cursos pendientes de subsanación.
                <ul class="mt-2 mb-0">
                    @foreach ($cursosSubsanacion as $curso)
                        <li>
                            <strong>{{ $curso->nombre_curso }}</strong>
                            <span class="text-muted"> — Ciclo {{ $curso->ciclo_matricula }}, {{ $curso->credito }} créditos</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($cursosDesaprobadosSinDocente && count($cursosDesaprobadosSinDocente))
            <div class="alert alert-danger">
                ⚠️ Los siguientes cursos desaprobados no tienen docente asignado en este semestre. No podrás matricular al estudiante hasta que se asignen:
                <ul class="mt-2 mb-0">
                    @foreach($cursosDesaprobadosSinDocente as $curso)
                        <li><strong>{{ $curso->nombre_curso }}</strong> — Ciclo {{ $curso->ciclo_matricula }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Datos del alumno --}}
        <table class="table table-bordered mb-3">
            <thead>
                <tr>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>DNI</th>
                    <th>Carrera</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $alumno->apellidos_pater_postulante }} {{ $alumno->apellidos_mater_postulante }}</td>
                    <td>{{ $alumno->nombres_postulante }}</td>
                    <td>{{ $alumno->idpostulante }}</td>
                    <td>{{ $alumno->nombre_de_carrera ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

                {{-- Turno --}}
        <div class="mb-3">
            <label for="turno" class="form-label">🕒 Selecciona el turno:</label>
            <select wire:model="turno" id="turno" class="form-select w-auto @error('turno') is-invalid @enderror" required>
                <option value="">Seleccione el turno ...</option>
                <option value="1">Mañana</option>
                <option value="2">Tarde</option>
            </select>
            @error('turno')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Boleta --}}
        <div class="mb-3">
            <label for="codigo_boleta" class="form-label">Código de boleta</label>
            <input type="text" wire:model.defer="codigoBoleta" id="codigo_boleta" class="form-control">
        </div>

        {{-- Convalidación y ubicación --}}
        @if($tipoCiclo !== 3)
            <div class="form-check">
                <input type="checkbox" wire:model="esConvalidacionUbicacion" wire:key="matriculaEspecialCheckbox" class="form-check-input" id="matriculaEspecial">
                <label class="form-check-label" for="matriculaEspecial">
                    Esta matrícula es por convalidación y ubicación
                </label>
            </div>

            @if($esConvalidacionUbicacion)
                <div class="alert alert-warning mt-2">
                    ⚠️ Usted está seleccionando <strong>MATRÍCULA POR CONVALIDACIÓN Y UBICACIÓN</strong>.
                </div>
            @endif
        @endif

        {{-- Visualización forzada de matrícula regular --}}
        @if($aprobacionBaja)
            <div class="mb-3 mt-3">
                <button wire:click="toggleMostrarMatricula" class="btn btn-outline-primary">
                    {{ $mostrarMatriculaForzada ? '🔒 Ocultar matrícula regular' : '🔓 Mostrar matrícula regular (excepción)' }}
                </button>

                @if($mostrarMatriculaForzada)
                    <div class="alert alert-info mt-2">
                        ⚠️ Estás forzando la visualización de la matrícula regular manualmente.
                    </div>
                @endif
            </div>
        @endif

      
                {{-- Cursos regulares --}}
        @if(!$aprobacionBaja || $mostrarMatriculaForzada)
            @if(!$licenciaActiva && $tipoCiclo === 3)
                <div class="alert alert-info">
                    📘 Este semestre es intensivo (tipo ciclo). Solo se permiten cursos de subsanación.
                </div>
            @elseif(!$licenciaActiva && $tieneMatriculaRegular)
                <div class="alert alert-warning">
                    ⚠️ Este alumno ya tiene matrícula regular registrada en este semestre. Verificar matrícula.
                </div>
            @elseif(!$licenciaActiva && $cursosRegulares && count($cursosRegulares))
                <div class="alert alert-info mt-3">
                    📘 Cursos disponibles para ciclo: <strong>{{ $nombreCicloSugerido }}</strong>
                </div>

                <div class="form-check mb-2">
                    <input type="checkbox" wire:click="toggleSeleccionarTodos" class="form-check-input" id="seleccionarTodos">
                    <label class="form-check-label" for="seleccionarTodos">
                        Seleccionar todos los cursos
                    </label>
                </div>

                <table class="table table-sm table-striped mb-3">
                    <thead>
                        <tr>
                            <th>✔</th>
                            <th>Curso</th>
                            <th>Créditos</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cursosRegulares as $c)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model="cursosSeleccionadosRegulares"
                                        value="{{ $c->idcursos }}"
                                        class="form-check-input"
                                    />
                                </td>
                                <td>{{ $c->nombre_curso }}</td>
                                <td>{{ $c->credito }}</td>
                                <td>{{ $c->horas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        {{-- Cursos por reincorporación --}}
        @if($reincorporacion && count($cursosPendientesPorLicencia))
            <div class="alert alert-info">
                📘 Cursos pendientes por licencia:
            </div>

            <table class="table table-sm table-bordered mb-3">
                <thead>
                    <tr>
                        <th>✔</th>
                        <th>Curso</th>
                        <th>Créditos</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cursosPendientesPorLicencia as $c)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    wire:model="cursosSeleccionadosRegulares"
                                    value="{{ $c->idcursos }}"
                                    class="form-check-input"
                                />
                            </td>
                            <td>{{ $c->nombre_curso }}</td>
                            <td>{{ $c->credito }}</td>
                            <td>{{ $c->horas }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Cursos de subsanación --}}
        @if($cursosSubsanacion && count($cursosSubsanacion))
            <div class="alert alert-secondary mb-3">
                🛠️ Cursos de subsanación
                @if($tipoCiclo == 3)
                    (máx. 16 créditos)
                @else
                    (máx. 8 créditos)
                @endif
            </div>

            <table class="table table-sm table-bordered mb-4">
                <thead>
                    <tr>
                        <th>✔</th>
                        <th>Curso</th>
                        <th>Ciclo ant.</th>
                        <th>Cred.</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cursosSubsanacion->sortByDesc('ciclo_matricula')->unique('idcursos') as $c)
                        @if(!in_array($c->idcursos, $cursosYaMatriculados))
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model="cursosSeleccionadosSubsanacion"
                                        value="{{ $c->idcursos }}"
                                        class="form-check-input"
                                    />
                                </td>
                                <td>{{ $c->nombre_curso }}</td>
                                <td>{{ $c->ciclo_matricula }}</td>
                                <td>{{ $c->credito }}</td>
                                <td>{{ $c->horas }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            @if($aprobacionBaja)
    <div class="mb-3">
        <button wire:click="$toggle('ignorarLimiteCreditos')" class="btn btn-outline-warning">
            {{ $ignorarLimiteCreditos ? '🔒 Activar límite de créditos' : '🔓 Quitar límite de créditos para subsanación' }}
        </button>

        @if($ignorarLimiteCreditos)
            <div class="alert alert-info mt-2">
                ⚠️ El límite de créditos ha sido desactivado manualmente para este ciclo, La matricula uqe se realizara se asignara a los docentes de cursos regular.
            </div>
        @endif
    </div>
@endif

            @php
    $totalSubsanacion = collect($cursosSubsanacion)
        ->whereIn('idcursos', $cursosSeleccionadosSubsanacion ?? [])
        ->sum('credito');

    $limite = $tipoCiclo === 3 ? 16 : 8;
@endphp

@if($totalSubsanacion > $limite && ! $ignorarLimiteCreditos)
    <div class="alert alert-danger">
        ❌ Has seleccionado <strong>{{ $totalSubsanacion }}</strong> créditos de subsanación, lo cual excede el límite permitido de <strong>{{ $limite }}</strong> créditos.
        <br>Debes reducir la selección o activar la excepción manual si está autorizado.
    </div>
@endif
        @endif
                {{-- Botón de acción --}}
        @php
            $hayRegulares = count($cursosSeleccionadosRegulares ?? []) > 0;
            $haySubsan = count($cursosSeleccionadosSubsanacion ?? []) > 0;
            $botonDisabled = $bloqueadoPorLicencia && ! $modoReincorporacion;
        @endphp

        <div class="text-end">
            <button
                class="btn btn-primary"
                wire:click="confirmarMatricula"
                @if($botonDisabled) disabled @endif
            >
                Registrar Matrícula
                @if($tipoCiclo == 3) (solo subsanación) @endif
            </button>
        </div>
    @elseif($search)
        <div class="alert alert-warning">
            No hay resultados para "<strong>{{ $search }}</strong>"
        </div>
    @endif

    {{-- Modal Diagnóstico --}}
    <div wire:ignore.self class="modal fade" id="modalDiagnostico" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📘 Historial académico por ciclo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @foreach ($historialPorCiclo as $ciclo => $cursos)
                        <h6 class="mt-3">🌀 Ciclo {{ $ciclo }}</h6>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Créditos</th>
                                    <th>Nota</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cursos as $curso)
                                    <tr>
                                        <td>{{ $curso['nombre'] }}</td>
                                        <td>{{ $curso['credito'] }}</td>
                                        <td>{{ $curso['nota'] ?? '--' }}</td>
                                        <td>
                                            @if ($curso['estado'] === 'Aprobado')
                                                <span class="badge bg-success">Aprobado</span>
                                            @elseif ($curso['estado'] === 'Desaprobado')
                                                <span class="badge bg-danger">Desaprobado</span>
                                            @else
                                                <span class="badge bg-secondary">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert listener JS --}}
    <script>
        function registrarListenerSwal() {
            window.Livewire.on('swal:confirmarMatricula', function (data) {
                const payload = Array.isArray(data) ? data[0] : data;

                const turno = payload.turno ?? '—';
                const nombre_turno = payload.nombre_turno ?? '—';
                const regulares = Array.isArray(payload.regulares) ? payload.regulares : [];
                const subsanacion = Array.isArray(payload.subsanacion) ? payload.subsanacion : [];

                let html = `<p><strong>🕒 Turno:</strong> ${nombre_turno}</p>`;
                let totalCreditos = 0;

                if (regulares.length) {
                    html += '<strong>📘 Cursos Regulares:</strong><ul>';
                    regulares.forEach(c => {
                        html += `<li>${c.nombre_curso} (${c.credito} créditos)</li>`;
                        totalCreditos += parseInt(c.credito);
                    });
                    html += '</ul>';
                }

                if (subsanacion.length) {
                    html += '<strong>🛠️ Cursos de Subsanación:</strong><ul>';
                    subsanacion.forEach(c => {
                        html += `<li>${c.nombre_curso} (${c.credito} créditos)</li>`;
                        totalCreditos += parseInt(c.credito);
                    });
                    html += '</ul>';
                }

                html += `<p><strong>🎓 Total de créditos:</strong> ${totalCreditos}</p>`;

                Swal.fire({
                    title: 'Confirmar Matrícula',
                    html: html || '<em>No se han seleccionado cursos.</em>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Registrar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.find(livewireComponentId).call('registrarMatricula');
                    }
                });
            });

            window.Livewire.on('abrirModalDiagnostico', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalDiagnostico'));
                modal.show();
            });

            window.Livewire.on('matriculaExitosa', () => {
                Swal.fire({
                    title: '✅ Matrícula registrada',
                    text: 'La matrícula se ha registrado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                });

                Livewire.find(livewireComponentId).call('resetVistaMatricula');
            });
        }

        document.addEventListener('DOMContentLoaded', registrarListenerSwal);
        document.addEventListener('livewire:load', registrarListenerSwal);
    </script>

    <script>
        const livewireComponentId = @json($this->getId());
    </script>

</div>
</div>