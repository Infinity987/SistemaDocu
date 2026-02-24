<div>
    {{-- Selector de Malla Curricular --}}
    <label class="form-label fw-bold mb-2 text-primary">
        <i class="fas fa-book"></i> Seleccione Malla Curricular:
    </label>
    <select wire:model="mallaId" wire:change="loadCompetencias" class="form-select mb-4">
        <option value="">--Seleccione--</option>
        @foreach($mallas as $malla)
            <option value="{{ $malla->idmalla_curricular }}">
                {{ $malla->nombre_malla_curricular }}
            </option>
        @endforeach
    </select>

    {{-- Contenedor de Dominios y Competencias --}}
    @if(count($dominios) > 0)
        <div class="row">
            @foreach($dominios as $grupo)
                <div class="col-md-6">
                    <div class="card border-primary shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="fas fa-layer-group me-2"></i>{{ $grupo['dominio']->Nombre_dominio }}
                            </strong>
                       <button onclick="confirmarEliminarDominio({{ $grupo['dominio']->iddominio_competencia }})"
        class="btn btn-outline-light btn-sm">
    <i class="fas fa-trash-alt"></i> Eliminar Dominio
</button>



                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach($grupo['competencias'] as $comp)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-bullseye text-muted me-2"></i>
                                        <span>
                                            <strong>{{ $comp->competencia }}</strong> <br>
                                            <small class="text-muted">{{ $comp->descripcion }}</small>
                                        </span>
                                    </div>
                                <button onclick="confirmarEliminarCompetencia({{ $comp->idcompetencias }})"
        class="btn btn-outline-warning btn-sm">
    <i class="fas fa-times-circle"></i> Eliminar
</button>



                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No hay dominios con competencias disponibles para la malla seleccionada.
        </div>
    @endif
    <div class="alert alert-info">
    Valor actual de mallaId: {{ $mallaId }}
</div>

<script>
    function confirmarEliminarCompetencia(id) {
        Swal.fire({
            title: '¿Eliminar esta competencia?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('eliminarCompetenciaDirecto', id);
            }
        });
    }

    function confirmarEliminarDominio(id) {
        Swal.fire({
            title: '¿Eliminar este dominio y sus competencias?',
            text: "Se eliminarán todas las competencias asociadas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('eliminarDominioDirecto', id);
            }
        });
    }
</script>

</div>
