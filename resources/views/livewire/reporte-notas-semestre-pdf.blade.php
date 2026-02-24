<div>
   <div class="container-fluid px-4">
    <div class="row g-3">
        {{-- Carrera --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="idcarrera" class="font-weight-bold text-primary">
                    <i class="fas fa-project-diagram"></i> Carrera:
                </label>
                <select wire:model="selectedCarrera" wire:change="handleCarreraChange($event.target.value)" id="idcarrera" name="idcarrera" class="form-control border border-primary">
                    <option value="">Seleccione una carrera</option>
                    @foreach($carrera as $carreraselect)
                        <option value="{{ $carreraselect->idcarreras }}">{{ $carreraselect->nombre_de_carrera }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Malla --}}
        @if (!is_null($selectedCarrera))
        <div class="col-md-4">
            <div class="form-group">
                <label for="id_malla" class="font-weight-bold text-primary">
                    <i class="fas fa-layer-group"></i> Malla Curricular:
                </label>
                <select wire:model="selectedMalla" wire:change="handleMallaChange($event.target.value)" id="id_malla" name="id_malla" class="form-control border border-primary">
                    <option value="">Seleccione una malla</option>
                    @foreach($malla as $selectmalla)
                        <option value="{{ $selectmalla->idmalla_curricular }}">{{ $selectmalla->nombre_malla_curricular }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Semestre --}}
        @if (!is_null($selectedMalla))
        <div class="col-md-4">
            <div class="form-group">
                <label for="id_semestre" class="font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt"></i> Semestre Académico:
                </label>
                <select wire:model="selectedSemestre" wire:change="handleSemestreChange($event.target.value)" id="id_semestre" name="id_semestre" class="form-control border border-primary">
                    <option value="">Seleccione un semestre</option>
                    @foreach($semestre as $selectsemestre)
                        <option value="{{ $selectsemestre->idsemestre_academico }}">{{ $selectsemestre->año }}-{{ $selectsemestre->periodo }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Ciclo --}}
        @if (!is_null($selectedSemestre))
        <div class="col-md-4">
            <div class="form-group">
                <label for="id_ciclo" class="font-weight-bold text-primary">
                    <i class="fas fa-sync-alt"></i> Ciclo:
                </label>
                <select wire:model="selectedCiclo" wire:change="handleCicloChange($event.target.value)" id="id_ciclo" name="id_ciclo" class="form-control border border-primary">
                    <option value="">Seleccione un ciclo</option>
                    @foreach($ciclo as $selectciclo)
                        <option value="{{ $selectciclo->idciclos }}">{{ $selectciclo->nombre_ciclo }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Turno --}}
        @if (!is_null($selectedCiclo))
        <div class="col-md-4">
            <div class="form-group">
                <label for="id_turno" class="font-weight-bold text-primary">
                    <i class="fas fa-clock"></i> Turno:
                </label>
                <select wire:model="selectedTurno" wire:change="handleTurnoChange($event.target.value)" id="id_turno" name="id_turno" class="form-control border border-primary">
                    <option value="">Seleccione un turno</option>
                    @foreach($turno as $selectturno)
                        <option value="{{ $selectturno->idturno }}">{{ $selectturno->nombre_turno }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Tipo de matrícula --}}
        @if (!is_null($selectedTurno))
        <div class="col-md-4">
            <div class="form-group">
                <label for="id_tipo_matricula" class="font-weight-bold text-primary">
                    <i class="fas fa-user-check"></i> Tipo de Matrícula:
                </label>
                <select wire:model="selectedTipomatricula" wire:change="handleMatriculaChange($event.target.value)" id="id_tipo_matricula" name="id_tipo_matricula" class="form-control border border-primary">
                    <option value="">Seleccione un tipo de matrícula</option>
                    @foreach($tipomatricula as $selecttipomatricula)
                        <option value="{{ $selecttipomatricula->idtipo_matricula }}">{{ $selecttipomatricula->nombre_tipo_matricula }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
