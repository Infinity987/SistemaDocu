<div>
    <div class="container mt-4">
    <h3 class="mb-4">📋 Gestión de Estado Académico</h3>

    {{-- Selectores de proceso y modalidad --}}

    @if ($procesoActivo)
    <div class="alert alert-info">
        <strong>Proceso activo:</strong> {{ $procesoActivo->nombre_proceso }}
    </div>
@endif

    <div class="row">
       @if (!$procesoActivo)
    <div class="col-md-4 mb-3">
        <label for="proceso">Proceso:</label>
        <select wire:model="selectedProceso" class="form-control">
            <option value="">Seleccione un proceso</option>
            @foreach ($procesos as $proceso)
                <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
            @endforeach
        </select>
    </div>
@endif
        </div>

        @if (!is_null($selectedProceso))
            <div class="col-md-4 mb-3">
                <label for="modalidad">Modalidad:</label>
                <select wire:model="selectedModalidad" class="form-control">
                    <option value="">Seleccione una modalidad</option>
                    @foreach ($modalidades as $mod)
                        <option value="{{ $mod->idmodalidad }}">{{ $mod->nombre_modalidad }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

   <button wire:click="cargarInscritos" class="btn btn-primary">Cargar inscritos</button>

    {{-- Tabla de inscritos --}}
    @if (!empty($inscritos))
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>dni</th>
                        <th>Nombre</th>
                        <th>Carrera</th>
                        <th>Estado de ingreso</th>
                        <th>Malla asignada</th>
                        <th>Rol académico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inscritos as $i)
                        <tr>
                            <td>{{$i->idpostulante}}</td>
                            <td>{{ $i->apellidos_pater_postulante }} {{ $i->apellidos_mater_postulante }} {{ $i->nombres_postulante }}</td>
                            <td>{{ $i->nombre_de_carrera }}</td>
                            <td>
                                @if ($i->estado_ingreso === 'Alcanzó vacante')
                                    <span class="badge badge-success">Ingresó</span>
                                @else
                                    <span class="badge badge-secondary">No ingresó</span>
                                @endif
                            </td>
                            <td>
                                @if ($i->id_malla)
                                    <span class="text-success">Asignada</span>
                                @else
                                    <span class="text-muted">Sin malla</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tieneRolAlumno = DB::table('model_has_roles')
                                        ->where('model_id', $i->idpostulante)
                                        ->where('role_id', 4)
                                        ->exists();
                                @endphp
                                @if ($tieneRolAlumno)
                                    <span class="text-success">Alumno</span>
                                @else
                                    <span class="text-muted">Postulante</span>
                                @endif
                            </td>
                            <td>
                                {{-- Botón para promover a alumno --}}
                                @if ($i->estado_ingreso !== 'Alcanzó vacante')
                                    <button wire:click="promoverAlumno({{ $i->idinscripcion }})" class="btn btn-sm btn-success">
                                        Promover a alumno
                                    </button>
                                @endif

                                {{-- Botón para retirar malla y rol --}}
                                @if ($i->id_malla)
                                    <button wire:click="retirarAlumno({{ $i->idpostulante }})" class="btn btn-sm btn-danger">
                                        Retirar malla y rol
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

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
