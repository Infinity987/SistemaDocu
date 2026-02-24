<div>
    <div class="row">
        {{-- Carrera --}}
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="idcarrera"><i class="fas fa-project-diagram"></i> Carrera:</label>
                <select wire:model="selectedCarrera" wire:change="handleCarreraChange($event.target.value)" id="idcarrera" name="idcarrera" class="form-control">
                    <option value="">Seleccione una carrera</option>
                    @foreach($carrera as $carreraselect)
                        <option value="{{ $carreraselect->idcarreras }}">{{ $carreraselect->nombre_de_carrera }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Malla --}}
        @if (!is_null($selectedCarrera))
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="id_malla">Malla:</label>
                <select wire:model="selectedMalla" wire:change="handleMallaChange($event.target.value)" id="id_malla" name="id_malla" class="form-control">
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
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="id_semestre">Semestre:</label>
                <select wire:model="selectedSemestre" wire:change="handleSemestreChange($event.target.value)" id="id_semestre" name="id_semestre" class="form-control">
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
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="id_ciclo">Ciclo:</label>
                <select wire:model="selectedCiclo" wire:change="handleCicloChange($event.target.value)" id="id_ciclo" name="id_ciclo" class="form-control">
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
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="id_turno">Turno:</label>
                <select wire:model="selectedTurno" wire:change="handleTurnoChange($event.target.value)" id="id_turno" name="id_turno" class="form-control">
                    <option value="">Seleccione un turno</option>
                    @foreach($turno as $selectturno)
                        <option value="{{ $selectturno->idturno }}">{{ $selectturno->nombre_turno }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

           @if (!is_null($selectedTurno))
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="proceso">tipo matricula:</label>
                <select wire:model="selectedTipomatricula" wire:change="handleMatriculaChange($event.target.value)" id="id_tipo_matricula" name="id_tipo_matricula" class="form-control">
                    <option value="">Seleccione un tipo de matricula</option>
                    @foreach($tipomatricula as $selecttipomatricula)
                        <option value="{{ $selecttipomatricula->idtipo_matricula }}">{{ $selecttipomatricula->nombre_tipo_matricula}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
    </div>
</div>