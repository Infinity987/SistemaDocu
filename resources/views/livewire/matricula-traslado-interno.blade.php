<div>
    <div class="p-6 bg-[#fffaf3] rounded shadow-md">
        <h2 class="text-xl font-bold mb-6 text-[#533827]">📘 Matrícula por Traslado Interno</h2>

        {{-- 🔍 Búsqueda --}}
        <div class="mb-6 flex items-center gap-4">
            <input type="text" wire:model.defer="dni" placeholder="DNI del alumno" class="border border-[#d8bfa3] p-2 rounded w-64 bg-[#fff3e0] text-[#533827]">
            <button wire:click="buscarAlumno" class="btn-institucional px-4 py-2 rounded shadow">Buscar</button>
        </div>

        {{-- 🧾 Datos del alumno --}}
        @if ($alumno)
            <div class="mb-6 bg-[#fff3e0] p-4 rounded border border-[#d8bfa3]">
                <p><strong>👤 Alumno:</strong> {{ $alumno->nombres_postulante }} {{ $alumno->apellidos_pater_postulante }} {{ $alumno->apellidos_mater_postulante }}</p>
                <p><strong>📚 Malla actual:</strong> {{ $nombreMallaAnterior }}</p>
                <p><strong>📅 Ciclo actual:</strong> {{ $nombreCicloActual }}</p>
            </div>

            {{-- ⚠️ Mensaje si ya hizo traslado interno --}}
            @if (! $mostrarSelectorMalla)
                ⚠️ Este alumno ya realizó traslado interno en un semestre anterior. No puede volver a seleccionar malla.
            @else
                ✅ Este alumno aún no ha hecho traslado interno. Puedes seleccionar la nueva malla.
            @endif

            {{-- 🔄 Selección de nueva malla --}}
            @if ($mostrarSelectorMalla)
                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-[#533827]">🎓 Nueva malla:</label>
                    <select wire:model="mallaNueva" class="border p-2 rounded w-full bg-[#fff3e0] text-[#533827] border-[#d8bfa3]">
                        <option value="">-- Selecciona una malla --</option>
                        @foreach ($mallasDisponibles as $malla)
                            <option value="{{ $malla->idmalla_curricular }}">{{ $malla->nombre_malla }}</option>
                        @endforeach
                    </select>
                    <button wire:click="convalidar" class="btn-institucional mt-3 px-4 py-2 rounded shadow">Convalidar</button>
                </div>
            @endif
        @endif

        {{-- ✅ Cursos convalidables FG, FP, FEL agrupados por ciclo --}}
        @if ($cursosConvalidablesPorCiclo)
            <div class="mb-6">
                <h3 class="font-semibold text-[#533827] mb-2">✅ Cursos convalidables por ciclo (FG, FP, FEL)</h3>
                @foreach ($cursosConvalidablesPorCiclo as $ciclo => $cursos)
                    <div class="mb-2">
                        <button wire:click="$toggle('cicloExpandido.{{ $ciclo }}')" class="btn-claro px-3 py-1 rounded">
                            Ciclo {{ $ciclo }} ({{ count($cursos) }} cursos)
                        </button>
                        @if (!empty($cicloExpandido[$ciclo]))
                            <table class="tabla-cursos-modal mt-2">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Créditos</th>
                                        <th>Formación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cursos as $curso)
                                        <tr>
                                            <td>{{ $curso->nombre_curso }}</td>
                                            <td>{{ $curso->credito }}</td>
                                            <td>{{ $curso->Formacion }}</td>
                                            <td>
                                                @if ($aprobados->contains($curso->nombre_curso))
                                                    ✅ Aprobado
                                                @elseif ($desaprobados->contains($curso->nombre_curso))
                                                    ❌ Desaprobado
                                                @else
                                                    ⏳ Pendiente
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 📚 Cursos FE pendientes agrupados por ciclo --}}
      @if ($cursosPendientesPorCiclo)
    <div class="mb-6">
        <h3 class="font-semibold text-[#533827] mb-2">📚 Cursos pendientes por ciclo (FG, FP, FEL, FE)</h3>
        <p class="text-[#8b5e3c] italic mb-2">⚠️ Estos cursos serán matriculados automáticamente con nota 0.</p>
        @foreach ($cursosPendientesPorCiclo as $ciclo => $cursos)
            <div class="mb-2">
                <button wire:click="$toggle('cicloExpandido.{{ $ciclo }}')" class="btn-claro px-3 py-1 rounded">
                    Ciclo {{ $ciclo }} ({{ count($cursos) }} cursos)
                </button>
                @if (!empty($cicloExpandido[$ciclo]))
                    <table class="tabla-cursos-modal mt-2">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Créditos</th>
                                <th>Formación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cursos as $curso)
                                <tr>
                                    <td>{{ $curso->nombre_curso }}</td>
                                    <td>{{ $curso->credito }}</td>
                                    <td>{{ $curso->Formacion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>
@endif
  {{-- 🚀 Mensaje del ciclo siguiente --}}
        @if ($cicloActual)
            <p class="mt-4 text-[#533827] font-semibold">
                🚀 El alumno se matriculará automáticamente en el ciclo {{ $cicloActual + 1 }}
            </p>
        @endif
@if ($cursosDelCicloSiguiente)


<div class="mb-4">
    <label class="block font-semibold text-[#533827] mb-2">🕒 Turno para ciclo siguiente:</label>
    <select wire:model="turno" class="border p-2 rounded bg-[#fff3e0] text-[#533827] border-[#d8bfa3]">
        <option value="1">Mañana</option>
        <option value="2">Tarde</option>
    </select>
</div>

<div class="mb-4">
    <label class="block font-semibold text-[#533827] mb-2">🎫 Código de boleta para ciclo siguiente:</label>
    <input type="text" wire:model.defer="codigoBoleta" placeholder="Ej. BOLETA2025-001" class="border p-2 rounded bg-[#fff3e0] text-[#533827] border-[#d8bfa3] w-full">
</div>

    <div class="mb-6">
        <h3 class="font-semibold text-[#533827] mb-2">🚀 Cursos del ciclo {{ $cicloActual + 1 }} (próxima matrícula)</h3>
        <table class="tabla-cursos-modal mt-2">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Créditos</th>
                    <th>Formación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cursosDelCicloSiguiente as $curso)
                    <tr>
                        <td>{{ $curso->nombre_curso }}</td>
                        <td>{{ $curso->credito }}</td>
                        <td>{{ $curso->Formacion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

        {{-- 🚀 Registrar matrícula --}}
        @if ($alumno)
            <div class="flex gap-4 mt-6">
                <button wire:click="registrarMatriculaTraslado" class="btn-institucional px-4 py-2 rounded shadow">Registrar matrícula</button>
            </div>
        @endif

        {{-- 🧾 Mensajes --}}
        @if (session()->has('success'))
            <div class="mt-6 text-green-700 font-semibold">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="mt-6 text-red-700 font-semibold">{{ session('error') }}</div>
        @endif
    </div>
</div>