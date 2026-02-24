<div>
<div class="row">
    <!-- Semestre Selector -->
    <div class="col-sm-4 mb-3">
        <label for="semestre"><i class="fas fa-calendar-alt"></i> Semestre:</label>
        <select wire:model="selectedSemestre" class="form-control">
            <option value="">Seleccione un semestre</option>
            @if($semestreActivo)
                <option value="{{ $semestreActivo->idsemestre_academico }}">
                    {{ $semestreActivo->año }}-{{ $semestreActivo->periodo }}
                </option>
            @endif
        </select>
    </div>

    <!-- Ciclo Selector -->
    <div class="col-sm-4 mb-3">
        <label for="semestre"><i class="fas fa-calendar-alt"></i> Ciclo:</label>
        <select wire:model="selectedCiclo" class="form-control">
            <option value="">Seleccione un ciclo</option>
            @foreach($ciclo as $c)
                <option value="{{ $c->idciclos }}">{{ $c->nombre_ciclo}}</option>
            @endforeach
        </select>
    </div>

    <!-- Campo de Búsqueda -->
    <div class="col-sm-4 mb-3">
        <label for="search"><i class="fas fa-search"></i> Buscar alumno:</label>
        <input type="text"
               wire:model.live.debounce.500ms="search"
               class="form-control"
               placeholder="Buscar por DNI o apellidos" />
    </div>

    <!-- Resultados -->
    @if(count($alumno) > 0)
        <div class="col-sm-12 mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Apellidos</th>
                        <th>Nombres</th>
                        <th>ID Postulante</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumno as $a)
                        <tr>
                            <td>{{ $a->apellidos_pater_postulante }} {{ $a->apellidos_mater_postulante }}</td>
                            <td>{{ $a->nombres_postulante }}</td>
                            <td>{{ $a->idpostulante }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" wire:click="seleccionarAlumno({{ $a->idpostulante }})">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($selectedSemestre && strlen($search) > 0)
        <div class="col-sm-12 mt-3">
            <p class="text-muted">No se encontraron coincidencias...</p>
        </div>
    @endif

    <!-- Datos de matrícula -->
    @if($matricula)
        <div class="mt-3">
            <h5>Datos de matrícula:</h5>
            <ul>
                <li>ID alumno: {{ $matricula->id_alumno }}</li>
                <li>Fecha: {{ $matricula->fecha_matricula }}</li>
                <li>Ciclo: {{ $matricula->ciclo_matricula }}</li>
            </ul>
        </div>
    @endif

    <!-- Cursos matriculados -->
    @if($cursosAlumno)
        <div class="mt-3">
            <h5>Cursos matriculados:</h5>
            <ul>
                @foreach($cursosAlumno as $curso)
                    <li>{{ $curso->nombre_curso }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario de licencia -->
    @if($matricula)
        <div class="col-sm-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-file-signature text-primary"></i> Registrar Licencia Académica
                    </h5>

                    <div class="form-group mb-3">
                        <label for="resolucionLicencia" class="font-weight-bold">
                            <i class="fas fa-file-alt"></i> Resolución:
                        </label>
                        <input type="text" class="form-control border-primary"
                               wire:model="resolucionLicencia"
                               placeholder="Ej: Resolución N° 123-2025-D">
                    </div>

                    <div class="form-group mb-3">
                        <label for="motivoLicencia">Motivo de licencia</label>
                        <input type="text" wire:model.defer="motivoLicencia" class="form-control" placeholder="Escriba el motivo">
                        @error('motivoLicencia')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="cantidadSemestres" class="font-weight-bold">
                            <i class="fas fa-hourglass-half"></i> Semestres de licencia:
                        </label>
                        <input type="number" min="1" class="form-control border-primary"
                               wire:model="cantidadSemestres"
                               placeholder="Ej: 2">
                    </div>

                    <div class="form-group mb-3">
                        <button class="btn btn-info" wire:click="calcularSemestreFin">
                            <i class="fas fa-calculator"></i> Calcular semestre de reincorporación
                        </button>
                    </div>

                    <!-- Resultados del cálculo -->
                   @if($semestreFinLicencia && $semestreReincorporacion)
    <div class="card border-info mb-3">
        <div class="card-header bg-info text-white">
            <i class="fas fa-info-circle"></i> Resumen de licencia proyectada
        </div>
        <div class="card-body">
            <p>
                <strong>Inicio de licencia:</strong>
                {{ $semestreActivo->año }} - {{ $this->obtenerPeriodoDesdeTipoCiclo($semestreActivo->tipo_ciclo ?? 1) }}
            </p>
            <p>
                <strong>Fin de licencia:</strong>
                {{ $semestreFinLicencia->año }} - {{ $semestreFinLicencia->periodo }}
            </p>
            <p>
                <strong>Reincorporación estimada:</strong>
                {{ $semestreReincorporacion->año }} - {{ $semestreReincorporacion->periodo }}
            </p>
        </div>
    </div>
@endif

                    <button class="btn btn-outline-success"
                            wire:click="guardarLicencia">
                        <i class="fas fa-save"></i> Guardar Licencia
                    </button>

                    @if(session()->has('mensaje'))
                        <div class="alert alert-info mt-3">
                            {{ session('mensaje') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

</div>