<div class="container">
    <div class="row">
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="departamento">Departamento:</label>
                <select wire:model="selectedDepartamento" wire:change="handleDepartamentoChange($event.target.value)" id="departamento" class="form-control" required>
                    <option value="">Seleccione un departamento</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->Departamento }}">{{ $departamento->Departamento }}</option>
                    @endforeach
                </select>
             
                {{-- <p>Departamento seleccionado: {{ $selectedDepartamento }}</p> --}}
            </div>
        </div>

        @if (!is_null($selectedDepartamento))
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <select wire:model="selectedProvincia" wire:change="handleProvinciaChange($event.target.value)" id="provincia" class="form-control" required>
                        <option value="">Seleccione una provincia</option>
                        @foreach($provincias as $provincia)
                            <option value="{{ $provincia->Provincia }}">{{ $provincia->Provincia }}</option>
                        @endforeach
                    </select>
                           
                    {{-- <p>Provincia seleccionada: {{ $selectedProvincia }}</p> --}}
                </div>
            </div>
        @endif

        @if (!is_null($selectedProvincia))
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="distrito">Distrito:</label>
                    <select id="distrito_nacimiento" name="distrito_nacimiento" wire:model="selectedDistrito" wire:change="handleDistritoChange($event.target.value)"  class="form-control" required>
                        <option value="">Seleccione un distrito</option>
                        @foreach($distritos as $distrito)
                            <option value="{{ $distrito->Ubigeo }}">{{ $distrito->Distrito }}</option>
                        @endforeach
                    </select>
                   
                    {{-- <p>Distrito seleccionado: {{ $selectedDistrito }}</p> --}}
                </div>
            </div>
        @endif
    </div>
</div>

