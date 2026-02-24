<div>

    <ul class="nav nav-tabs mb-4" id="tabs">
    <li class="nav-item">
        <a class="nav-link active" id="tab-normal" data-toggle="tab" href="#contenido-normal">🧮 Proceso de Admisión</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-historico" data-toggle="tab" href="#contenido-historico">📄 Carga Histórica</a>
    </li>
</ul>

<div class="tab-content">
    {{-- TAB NORMAL --}}
    <div class="tab-pane fade show active" id="contenido-normal">
        <div class="container">
        <div class="row">
            {{-- PROCESO --}}
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="proceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
                    <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)"
                        id="proceso" class="form-control">
                        <option value="">Seleccione un proceso</option>
                        @foreach ($procesos as $proceso)
                            <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- MODALIDAD --}}
            @if (!is_null($selectedProceso))
                <div class="col-sm-4 mb-3">
                    <div class="form-group">
                        <label for="modalidad">MODALIDAD:</label>
                        <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)"
                            id="modalidad" name="modalidad" class="form-control">
                            <option value="">Seleccione una modalidad</option>
                            @foreach ($modalidad as $modalidades)
                                <option value="{{ $modalidades->idmodalidad }}">{{ $modalidades->nombre_modalidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>

        {{-- SECCIÓN TABLA Y ACCIONES --}}
        @if (!is_null($selectedModalidad))
            <div class="col-12 mb-3">
                <div class="form-group">
                    <center>
                        <div class="bg-lightblue disabled color-palette">
                            <h2>TABLA DE RESULTADOS</h2>
                        </div>
                    </center>

                    @php
                        $hayNota1 = $carreras->contains(function ($c) {
                            return !is_null($c->nota1);
                        });
                        $hayNota2 = $carreras->contains(function ($c) {
                            return !is_null($c->nota2);
                        });
                    @endphp

                    {{-- Upload segun fase --}}
                    @if (!$hayNota1)
                        <div class="mb-3">
                            <label for="archivoNotas">Subir archivo con DNI y NOTA 1</label>
                            <input type="file" id="archivoNotas" wire:model="archivoNotas" class="form-control"
                                accept=".csv,.txt">
                            @error('archivoNotas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="archivoNotasFase2">Subir archivo con DNI y NOTA 2</label>
                            <input type="file" id="archivoNotasFase2" wire:model="archivoNotasFase2"
                                class="form-control" accept=".csv,.txt">
                            @error('archivoNotasFase2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- Botones de acción con lógica de fases --}}
                    <div class="mt-3">

                        {{-- Botón 1: primera nota --}}
                        @if (!$hayNota1)
                            <button
                                onclick="confirmarAccion(@this, 'cargarPrimeraNota', '¿Seguro que deseas cargar la primera nota?')"
                                class="btn btn-primary">
                                <i class="fas fa-folder-plus"></i> Cargar primer resultado
                            </button>
                        @endif

                        {{-- Botón 2: segunda nota --}}
                        @if ($hayNota1 && !$hayNota2)
                            <button
                                onclick="confirmarAccion(@this, 'cargarSegundaYTerceraNota', '¿Seguro que deseas cargar la segunda y tercera nota?')"
                                class="btn btn-primary">
                                <i class="fas fa-folder-plus"></i> Cargar segundo y tercer resultados
                            </button>
                        @endif

                        {{-- Botón 3: generar ingresantes --}}
                        @if ($hayNota1 && $hayNota2)
                            <button
                                onclick="confirmarAccion(@this, 'generarIngresantes', '¿Generar lista de ingresantes?')"
                                class="btn btn-success">
                                <i class="fas fa-cogs"></i> Generar ingresantes
                            </button>
                        @endif

                    </div>


                    @if ($mostrarDesempate)
                        @foreach ($desempatesPorCarrera as $idVacante => $grupo)
                            <div class="bg-white shadow-md rounded-lg p-6 mb-6 border border-yellow-300">
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold text-yellow-700">
                                        ⚠️ Empate detectado en <span class="underline">{{ $grupo['carrera'] }}</span>
                                    </h2>
                                    <p class="text-sm text-gray-700">
                                        Se encontraron <strong>{{ count($grupo['empatados']) }}</strong> postulantes con
                                        la misma nota.<br>
                                        Vacantes disponibles: <strong>{{ $grupo['vacantesRestantes'] }}</strong>
                                    </p>
                                </div>

                                <div class="table-responsive">
                                    <table
                                        class="w-full text-sm text-left border border-gray-200 rounded overflow-hidden">
                                        <thead class="bg-yellow-100 text-yellow-800">
                                            <tr>
                                                <th class="px-4 py-2">Nombre</th>
                                                <th class="px-4 py-2">Nota Total</th>
                                                <th class="px-4 py-2 text-center">Seleccionar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grupo['empatados'] as $postulante)
                                                <tr class="border-t hover:bg-yellow-50">
                                                    <td class="px-4 py-2">
                                                        {{ $postulante->apellidos_pater_postulante }}
                                                        {{ $postulante->apellidos_mater_postulante }}
                                                        {{ $postulante->nombres_postulante }}
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        {{ number_format($postulante->nota_total, 2) }}
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        <input type="checkbox"
                                                            wire:model.defer="seleccionadosDesempate.{{ $idVacante }}.{{ $postulante->idinscripcion }}"
                                                            class="form-checkbox h-5 w-5 text-green-600">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>



                                <p class="mt-2 text-xs text-gray-500 text-right">
                                    Seleccionados: {{ count($seleccionadosDesempate[$idVacante] ?? []) }} /
                                    {{ $grupo['vacantesRestantes'] }}
                                </p>
                            </div>
                        @endforeach

                        <div class="flex justify-end mt-4">
                            <button wire:click="resolverDesempate"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-md transition">
                                ✅ Confirmar selección
                            </button>
                        </div>
                    @endif

                    {{-- Botón desaprobados --}}
                    <div class="mt-3">
                        <button type="button" class="btn btn-warning" wire:click="verDesaprobados">
                            Ver Desaprobados
                        </button>
                    </div>

                    {{-- Tabla de desaprobados editable --}}
                    @if ($mostrarDesaprobados)
                        <div class="mt-4">
                            <h3 class="text-lg font-bold mb-3">Lista de Desaprobados</h3>

                            <form wire:submit.prevent="guardarDesaprobados">
                                <div class="table-responsive">
                                    <table class="w-full border-collapse border border-gray-300">
                                        <thead>
                                            <tr class="bg-gray-200 text-left">
                                                <th class="border p-2">Nombre</th>
                                                <th class="border p-2">Carrera</th>
                                                <th class="border p-2">Mate</th>
                                                <th class="border p-2">Comu</th>
                                                <th class="border p-2">Demo</th>
                                                <th class="border p-2">Total</th>
                                                <th class="border p-2">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($desaprobados as $index => $desaprobado)
                                                <tr>
                                                    <td class="border p-2">
                                                        {{ $desaprobado->apellidos_pater_postulante }}
                                                        {{ $desaprobado->apellidos_mater_postulante }},
                                                        {{ $desaprobado->nombres_postulante }}

                                                    </td>
                                                    <td class="border p-2">{{ $desaprobado->nombre_de_carrera }}</td>


                                                    {{-- Editables --}}
                                                    <td class="border p-2">
                                                        <input type="number" class="form-control" step="0.01"
                                                            wire:model="desaprobados.{{ $index }}.nota1_mate"
                                                            value="{{ $desaprobado->nota1_mate }}">

                                                    </td>
                                                    <td class="border p-2">
                                                        <input type="number" class="form-control" step="0.01"
                                                            wire:model="desaprobados.{{ $index }}.nota1_comu"
                                                            value="{{ $desaprobado->nota1_comu }}">
                                                    </td>
                                                    <td class="border p-2">
                                                        <input type="number" class="form-control" step="0.01"
                                                            wire:model="desaprobados.{{ $index }}.nota1_demo"
                                                            value="{{ $desaprobado->nota1_demo }}">
                                                    </td>

                                                    {{-- Mostramos total y estado (se recalculan al guardar) --}}
                                                    <td class="border p-2">{{ $desaprobado->nota1 }}</td>
                                                    <td class="border p-2 text-red-600">
                                                        {{ $desaprobado->estado_apro_desa }}</td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center border p-2">No hay
                                                        desaprobados.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>


                                <div class="mt-3 text-right">
                                    <button type="button" wire:click="guardarDesaprobados" class="btn btn-success">
                                        Guardar cambios
                                    </button>
                                    <button type="button" wire:click="cerrarDesaprobados" class="btn btn-secondary">
                                        Cerrar
                                    </button>
                                </div>


                            </form>
                        </div>
                    @endif



                    {{-- Tabla principal --}}
                    <div class="table-responsive">
                        <table class="tabla-resultados w-100 mt-4">
                            <thead>
                                <tr>
                                    <th>Nombre de Modalidad</th>
                                    <th>DNI Postulante</th>
                                    <th>Datos</th>
                                    <th>Carrera</th>

                                    @if ($hayNota1 && $hayNota2)
                                        <th>Nota 1</th>
                                        <th>Nota 2</th>
                                        <th>Nota final base 20</th>
                                    @elseif(!$hayNota1)
                                        <th>Nota 1.1</th>
                                        <th>Nota 1.2</th>
                                        <th>Nota 1.3</th>
                                    @elseif($hayNota1)
                                        <th>Nota 1</th>
                                        <th>Nota 1 base 20</th>
                                        <th>Nota 2.1</th>
                                        <th>Nota 2.2</th>
                                        <th>Nota 2.3</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($carreras as $index => $carrera)
                                    <tr>
                                        <td>
                                            <input type="hidden" id="idincripcion_{{ $index }}"
                                                name="vacantes[{{ $index }}][idincripcion]"
                                                value="{{ $carrera->idinscripcion }}">
                                            <input type="hidden" id="idproceso" name="idproceso"
                                                value="{{ $carrera->idprocesos }}">
                                            <textarea class="form-control" id="nombre_proceso_{{ $index }}"
                                                name="vacantes[{{ $index }}][nombre_proceso]" readonly>{{ $carrera->nombre_modalidad }}</textarea>
                                        </td>

                                        <input type="hidden" id="idinscripcion_{{ $index }}"
                                            name="vacantes[{{ $index }}][idinscripcion]"
                                            value="{{ $carrera->idinscripcion }}">

                                        <td width="150px">
                                            <input type="number" class="form-control"
                                                id="idpostulante_{{ $index }}"
                                                name="vacantes[{{ $index }}][idpostulante]"
                                                value="{{ $carrera->idpostulante }}" readonly>
                                        </td>

                                        <td>
                                            <textarea class="form-control" id="datos_{{ $index }}" name="vacantes[{{ $index }}][datos]" readonly>
                                            {{ $carrera->apellidos_pater_postulante }} {{ $carrera->apellidos_mater_postulante }} {{ $carrera->nombres_postulante }}
                                                                </textarea>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control"
                                                id="carreras_{{ $index }}"
                                                name="vacantes[{{ $index }}][carreras]"
                                                value="{{ $carrera->nombre_de_carrera }}" readonly>
                                        </td>

                                        @if (is_null($carrera->nota1))
                                            {{-- Fase 1: permitir 1.1, 1.2, 1.3 --}}
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    id="nota1_1_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nota1_1]"
                                                    wire:model="carreras.{{ $index }}.nota1_1" step="0.01">
                                            </td>
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    id="nota1_2_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nota1_2]"
                                                    wire:model="carreras.{{ $index }}.nota1_2" step="0.01">
                                            </td>
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    id="nota1_3_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nota1_3]"
                                                    wire:model="carreras.{{ $index }}.nota1_3" step="0.01">
                                            </td>
                                        @elseif(is_null($carrera->nota2))
                                            {{-- Fase 2: mostrar nota1 y permitir 2.1, 2.2, 2.3 si corresponde --}}
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    id="nota1_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nota1]"
                                                    value="{{ $carrera->nota1 }}" step="0.01" readonly>
                                            </td>
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    id="nota1_redon_{{ $index }}"
                                                    name="vacantes[{{ $index }}][nota1_redon]"
                                                    value="{{ $carrera->nota1 / 2.5 }}" step="0.01" readonly>
                                            </td>

                                            <td width="90px">
                                                @if ($carrera->estado_apro_desa !== 'Desaprobó' || $segundaNotaHabilitada)
                                                    <input type="number" class="form-control"
                                                        id="nota2_1_{{ $index }}"
                                                        name="vacantes[{{ $index }}][nota2_1]"
                                                        wire:model="carreras.{{ $index }}.nota2_1"
                                                        step="0.01">
                                                @endif
                                            </td>
                                            <td width="90px">
                                                @if ($carrera->estado_apro_desa !== 'Desaprobó' || $segundaNotaHabilitada)
                                                    <input type="number" class="form-control"
                                                        id="nota2_2_{{ $index }}"
                                                        name="vacantes[{{ $index }}][nota2_2]"
                                                        wire:model="carreras.{{ $index }}.nota2_2"
                                                        step="0.01">
                                                @endif
                                            </td>
                                            <td width="90px">
                                                @if ($carrera->estado_apro_desa !== 'Desaprobó' || $segundaNotaHabilitada)
                                                    <input type="number" class="form-control"
                                                        id="nota2_3_{{ $index }}"
                                                        name="vacantes[{{ $index }}][nota2_3]"
                                                        wire:model="carreras.{{ $index }}.nota2_3"
                                                        step="0.01">
                                                @endif
                                            </td>
                                        @endif

                                        @if (!is_null($carrera->nota2))
                                            {{-- Ya connota2 calculada --}}
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    value="{{ $carrera->nota1 }}" step="0.01" readonly>
                                            </td>
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    value="{{ $carrera->nota2 }}" step="0.01" readonly>
                                            </td>
                                            <td width="100px">
                                                <input type="number" class="form-control"
                                                    value="{{ $carrera->nota_total }}" step="0.01" readonly>
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @endif
    </div>
    </div>

    {{-- TAB HISTÓRICO --}}
    <div class="tab-pane fade" id="contenido-historico">
        <div class="container mt-4">
    <h4 class="mb-3">📄 Generar Excel para carga histórica de resultados</h4>

    <div class="row">
        {{-- Selector de proceso --}}
        <div class="col-sm-4 mb-3">
            <label for="procesoHistorico">Proceso:</label>
            <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" class="form-control">
                <option value="">Seleccione un proceso</option>
                @foreach ($procesos as $proceso)
                    <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
                @endforeach
            </select>
        </div>

        {{-- Selector de modalidad --}}
        @if (!is_null($selectedProceso))
            <div class="col-sm-4 mb-3">
                <label for="modalidadHistorico">Modalidad:</label>
                <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)" class="form-control">
                    <option value="">Seleccione una modalidad</option>
                    @foreach ($modalidad as $mod)
                        <option value="{{ $mod->idmodalidad }}">{{ $mod->nombre_modalidad }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Botón para generar Excel --}}
    @if (!is_null($selectedModalidad))
        <div class="mt-3">
            <button wire:click="generarExcelHistorico" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Generar Excel con inscritos
            </button>
        </div>
    @endif

    <div class="mt-4">
    <label for="archivoHistorico">Subir Excel con datos históricos:</label>
    <input type="file" wire:model="archivoHistorico" class="form-control" accept=".xlsx,.csv">
    @error('archivoHistorico')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    <button wire:click="procesarArchivoHistorico" class="btn btn-primary mt-3">
        <i class="fas fa-upload"></i> Procesar archivo y registrar resultados
    </button>
</div>

    {{-- Mensaje explicativo --}}
    <div class="alert alert-info mt-4">
        <strong>Importante:</strong> Esta carga histórica no modifica la malla curricular ni el rol del postulante. Solo registra el historial de resultados en la tabla <code>resultados</code> y vincula los PDFs generados.
    </div>

    {{-- Mensajes de estado --}}
    @if (session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif
</div>
      
    </div>
</div>


    
    <script>
        function confirmarAccion(livewire, metodo, mensaje) {
            Swal.fire({
                title: 'Confirmar acción',
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    livewire.call(metodo);
                }
            });
        }
    </script>

</div>
