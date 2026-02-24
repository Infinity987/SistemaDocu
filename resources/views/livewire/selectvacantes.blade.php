<div class="container">
    <div class="row">
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="idproceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
                <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" id="idproceso" name="idproceso" class="form-control">
                    <option value="">Seleccione un proceso</option>
                    @foreach($procesos as $proceso)
                        <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if (!is_null($selectedProceso))
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="proceso">MODALIDAD:</label>
                <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)" id="modalidad" name="modalidad" class="form-control">
                    <option value="">Seleccione una modalidad</option>
                    @foreach($modalidad as $modalidades)
                        <option value="{{ $modalidades->idmodalidad }}">{{ $modalidades->nombre_modalidad }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        @if (!is_null($selectedModalidad))
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="carrera"><i class="fas fa-user-tie"></i> CARRERA:</label>
                    <select wire:model="selectedCarrera" wire:change="handleCarreraChange($event.target.value)" id="carrera" name="carrera" class="form-control">
                        <option value="">Seleccione una carrera</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->idvacantes }}">{{ $carrera->nombre_de_carrera }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>
</div>
