<div>
    <div class="container">
        <div class="row">
            <div class="col-sm-4 mb-3">
                <div class="form-group">
                    <label for="proceso">PROCESO:</label>
                    <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)"
                        id="proceso" class="form-control">
                        <option value="">Seleccione un proceso</option>
                        @foreach ($procesos as $proceso)
                            <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if (!is_null($selectedProceso))
                <div class="col-sm-4 mb-3">
                    <div class="form-group">
                        <label for="proceso">MODALIDAD:</label>
                        <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)"
                            id="modalidad" class="form-control">
                            <option value="">Seleccione una modalidad</option>
                            @foreach ($modalidad as $modalidades)
                                <option value="{{ $modalidades->idmodalidad }}">{{ $modalidades->nombre_modalidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            @if (!is_null($selectedModalidad))
                <div class="col-sm-4 mb-3">
                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                        <i class="fas fa-trash-alt"></i> Eliminar vacantes de esta modalidad
                    </button>
                </div>

                <div class="col-sm-4 mb-3">
    <button type="button" class="btn btn-success" wire:click="exportarExcel">
        <i class="fas fa-file-excel"></i> Exportar Vacantes a Excel
    </button>
</div>
            @endif

            {{-- partes serpara --}}
            @if (!is_null($selectedModalidad))
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-project-diagram"></i> MODALIDAD DEL PROCESO</th>
                                        <th><i class="far fa-list-alt" style="width: 400px"></i> CARRERAS</th>
                                        <th><i class="fas fa-sign-in-alt"></i> VACANTES</th>
                                        <th><i class="fas fa-edit"></i> EDITAR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($carreras as $carrera)
                                        <tr>
                                            <td width="160px"><input type="text" class="form-control"
                                                    id="nombre_proceso" name="nombre_proceso"
                                                    value="{{ $carrera->nombre_modalidad }}" readonly></td>
                                            <td width="230px"><input type="text" class="form-control" id="carreras"
                                                    name="carreras" value="{{ $carrera->nombre_de_carrera }}" readonly>
                                            </td>
                                            <td width="5px"><input type="text" class="form-control"
                                                    id="numerovacantes" name="numerovacantes"
                                                    value="{{ $carrera->cantidad_vacantes }}" readonly></td>

                                            <td width="5px">
                                                <a type="button" class="btn btn-warning btn-sm m-1 verModel"
                                                    data-nombreproce="{{ $carrera->nombre_modalidad }}"
                                                    data-nombrecarre="{{ $carrera->nombre_de_carrera }}"
                                                    data-cantiva="{{ $carrera->cantidad_vacantes }}"
                                                    data-idvaca="{{ $carrera->idvacantes }}" data-bs-toggle="modal"
                                                    data-bs-target="#verDetalle">
                                                    <i class="fas fa-clipboard"></i> </a>
                                            </td>
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


    <script>
        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará todas las vacantes de la modalidad seleccionada.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarVacantesConfirmado');
                }
            });
        }
    </script>


</div>
