<div class="container">
    <div class="row">
        <!-- Select de Departamento -->
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="departamento">Departamento:</label>
                <select wire:model="selectedDepartamento" wire:change="handleDepartamentoChange($event.target.value)" id="departamento" class="form-control" required>
                    <option value="">Seleccione un departamento</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->Departamento }}">{{ $departamento->Departamento }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Select de Provincia -->
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
                </div>
            </div>
        @endif

        <!-- Select de Distrito -->
        @if (!is_null($selectedProvincia))
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="distrito">Distrito:</label>
                    <select id="distrito" name="distrito" wire:model="selectedDistrito" wire:change="handleDistritoChange($event.target.value)" class="form-control" required>
                        <option value="">Seleccione un distrito</option>
                        @foreach($distritos as $distrito)                                
                            <option value="{{ $distrito->Distrito }}">{{ $distrito->Distrito }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <!-- Select de Colegio -->
        @if (!is_null($selectedDistrito))
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="colegio">COLEGIO:</label>
                    <select id="colegio" name="colegio" wire:model="selectedColegio" wire:change="handleColegioChange($event.target.value)" class="form-control" required>
                        <option value="">Seleccione un colegio</option>
                        @foreach($colegio as $colegios)
                            <option value="{{ $colegios->Codigo_Modular }}">{{ $colegios->Nombre_ie }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <!-- Mostrar Información del Colegio Seleccionado -->
        @if($colegioSeleccionado)
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="codigo_modular">Código Modular:</label>
                <input type="text" id="codigo_modular" name="codigo_modular" value="{{ $colegioSeleccionado->Codigo_Modular }}" class="form-control" readonly>
            </div>
        </div>
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion_colegio" name="direccion_colegio" value="{{ $colegioSeleccionado->Direccion }}" class="form-control" readonly>
            </div>
        </div>
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                
                <input type="hidden" id="distrito_cole" name="distrito_cole" value="{{ $colegioSeleccionado->Ubigeo }}" >
                <input type="hidden" id="nombre_colegio" name="nombre_colegio" value="{{ $colegioSeleccionado->Nombre_ie }}" >
            </div>
        </div>
    @endif
    

    </div>
</div>
