<div >
    <div class="row mb-10">
        <div class="col">
            <label>Carrera</label>
            <select wire:model="selectedCarrera" wire:change="handleCarreraChange($event.target.value)" class="form-control">
                <option value="">Seleccione</option>
                @foreach($carrera as $c)
                    <option value="{{ $c->idcarreras }}">{{ $c->nombre_de_carrera }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>Malla</label>
            <select wire:model="selectedMalla" wire:change="handleMallaChange($event.target.value)" class="form-control">
                <option value="">Seleccione</option>
                @foreach($malla as $m)
                    <option value="{{ $m->idmalla_curricular }}">{{ $m->nombre_malla_curricular }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>Semestre</label>
            <select wire:model="selectedSemestre" class="form-control">
                <option value="">Seleccione</option>
                @foreach($semestre as $s)
                    <option value="{{ $s->idsemestre_academico }}">{{ $s->año }} - {{ $s->periodo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>&nbsp;</label>
            <button wire:click="buscarLicencias" class="btn btn-primary form-control">
                Buscar
            </button>
        </div>
    </div>

    @if(session()->has('mensaje'))
        <div class="alert alert-info">{{ session('mensaje') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Resolución</th>
                <th>Motivo</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Semestre Fin (Texto)</th>
                <th>Cant. Semestres</th>
                <th>Acciones</th>
                <th>Reincorporación</th>

             
            </tr>
        </thead>
        <tbody>
            @forelse($licencias as $l)
                <tr>
                    <td>{{ $l->apellidos }} {{ $l->nombres }}</td>
                    <td  style="width:50px">{{ $l->resolucion_licencia }}</td>
                    <td>{{ $l->motivo_licencia }}</td>
                    <td>{{ $l->anio_inicio }} - {{ $l->periodo_inicio }}</td>
                    <td>{{ $l->anio_fin }} - {{ $l->periodo_fin }}</td>
                    <td>{{ $l->Nombre_semestre_fin }}</td>
                    <td>{{ $l->cantidad_semestres }}</td>
                   <td>
    <button wire:click="abrirModalEditar('{{ $l->idlicencia }}')" class="btn btn-warning">Editar</button>
    <button wire:click="generarPDF({{ $l->idlicencia }})" class="btn btn-outline-secondary">
    <i class="bi bi-file-earmark-pdf"></i> PDF
</button>
    <button wire:click="eliminarLicencia({{ $l->idlicencia }})" class="btn btn-danger">Eliminar</button>
    <button wire:click="abrirModalReincorporacion({{ $l->idlicencia }})" class="btn btn-primary">
    Reincorporar
</button>
</td>
<td>
    @if($l->reincorporado)
        <span class="badge bg-success">Registrada</span>
    @else
        <span class="badge bg-secondary">Pendiente</span>
    @endif
</td>



                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay licencias registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>


<!-- Modal Bootstrap -->
<div wire:ignore.self class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarLabel">
                    <i class="bi bi-pencil-square me-2"></i> Editar Licencia
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="$set('modalEditar', false)"></button>
            </div>
         <div class="modal-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Resolución</label>
            <input type="text" class="form-control" wire:model="resolucionLicencia">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Motivo</label>
            <input type="text" class="form-control" wire:model="motivoLicencia">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Semestres de Licencia</label>
            <input type="number" min="1" class="form-control" wire:model="cantidadSemestres">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold d-block">&nbsp;</label>
            <button type="button" class="btn btn-info w-100" wire:click="calcularSemestreFin">
                <i class="fas fa-calculator"></i> Calcular semestre de reincorporación
            </button>
        </div>

        @if($semestreFinLicencia)
        <div class="col-12">
            <label class="form-label fw-bold">Semestre de reincorporación</label>
            <div class="alert alert-secondary">
                {{ $semestreFinLicencia->año }} - {{ $semestreFinLicencia->periodo }}
            </div>
        </div>
        @endif
    </div>
</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('modalEditar', false)">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" wire:click="actualizarLicencia">
                    <i class="bi bi-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reincorporación -->
<div wire:ignore.self class="modal fade" id="modalReincorporacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar Reincorporación</h5>
                <button type="button" class="btn-close btn-close-white" wire:click="$set('modalReincorporacion', false)"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-bold">Resolución</label>
                <input type="text" class="form-control" wire:model="resolucionReincorporacion">

                <div class="mt-3">
                    <label class="form-label fw-bold">Semestre actual</label>
                    <div class="alert alert-secondary">
    {{ $semestreActualNombre }}
</div>
                    <div class="alert alert-secondary">
                        ID: {{ $semestreActualId }}
                    </div>
                </div>

                @if(session()->has('mensaje'))
    <div class="alert alert-warning mt-2">
        {{ session('mensaje') }}
    </div>
@endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('modalReincorporacion', false)">Cancelar</button>
                <button type="button" class="btn btn-success" wire:click="guardarReincorporacion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('abrir-modal-reincorporacion', () => {
            let modal = new bootstrap.Modal(document.getElementById('modalReincorporacion'));
            modal.show();
        });
    });
</script>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('abrir-modal-editar', () => {
            let modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
        });
    });
</script>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('cerrar-modal-reincorporacion', () => {
            let modal = bootstrap.Modal.getInstance(document.getElementById('modalReincorporacion'));
            if (modal) modal.hide();
        });
    });
</script>

</div>

