<div class="container mt-2">
    <div class="row">
        <div class="col-sm-4 mb-3">
            <div class="form-group">
                <label for="proceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
                <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" id="proceso"
                    class="form-control">
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
                        id="modalidad" name="modalidad" class="form-control">
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
            <div class="col-12 mb-3">
                <div class="form-group">
                    @if ($hayResultados)
                        <div class="table-responsive">
                            <table class="table" id="tablainscritos" wire:key="tabla-{{ $selectedModalidad }}"
                                wire:ignore>
                                <a href="{{ route('inscripcion.exportar', [$selectedProceso, $selectedModalidad]) }}"
                                    target="_blank" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </a>
                                <thead>
                                    <tr>
                                        <th>Nombre de la modalidad</th>
                                        <th>Dni -</th>
                                        <th>Apellidos y Nombres</th>
                                        <th>Carrera</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($carreras as $carrera)
                                        <tr>
                                            <td>{{ $carrera->nombre_modalidad }}</td>
                                            <td>{{ strval($carrera->idpostulante) }}</td>
                                            <td>{{ $carrera->apellidos_pater_postulante }}
                                                {{ $carrera->apellidos_mater_postulante }}
                                                {{ $carrera->nombres_postulante }}</td>
                                            <td>{{ $carrera->nombre_de_carrera }}</td>
                                            <td>
                                                <div style="display: flex; gap: 10px; align-items: center;">
                                                    <form action="{{ route('pdf.fichainscritos') }}" method="POST"
                                                        enctype="multipart/form-data" target="_blank">
                                                        @csrf
                                                        <button title="Ficha de Inscripción" class="btn btn-sm btn-info"
                                                            type="submit" id="idpostu" name="idpostu"
                                                            value="{{ $carrera->idinscripcion }}">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('pdf.fichainscritosconstancia') }}"
                                                        method="POST" enctype="multipart/form-data" target="_blank">
                                                        @csrf
                                                        <button title="Constancia de inscripción"
                                                            class="btn btn-sm btn-info" type="submit" id="idpostu"
                                                            name="idpostu" value="{{ $carrera->idinscripcion }}">
                                                            <i class="fas fa-portrait"></i>
                                                        </button>
                                                    </form>

                                                    <button title="Cambiar de carrera" type="button"
                                                        class="btn btn-sm btn-warning" data-toggle="modal"
                                                        data-target="#formModal1{{ $carrera->idpostulante }}">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>

                                                    {{-- btn eliminar inscripcion --}}
                                                    <button title="Eliminar inscripción" type="button"
                                                        class="btn btn-sm btn-danger" data-toggle="modal"
                                                        data-target="#formModal1d{{ $carrera->idpostulante }}">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                </div>


                                                <div class="modal fade" id="formModal1d{{ $carrera->idpostulante }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background: linear-gradient(135deg, #8d0c01, #ec2a2a); ">
                                                                <h5 class="modal-title text-white"
                                                                    id="exampleModalLabel"><i class="fas fa-trash"></i>
                                                                    Eliminar inscripcion</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body"
                                                                style="background: linear-gradient(135deg, #fff8f7, #ffe4e4); ">
                                                                <form action="{{ route('inscripcion.eliminar') }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="id_inscrip"
                                                                        value="{{ $carrera->idinscripcion }}">
                                                                    <div class="form-group text-center">
                                                                        <label
                                                                            style="
                                                                                font-size: 2rem;
                                                                                font-weight: bold;
                                                                                color: #b30000;
                                                                                background: linear-gradient(to right, #ffe5e5, #ffffff);
                                                                                padding: 15px 20px;
                                                                                border-radius: 10px;
                                                                                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                                                                                display: inline-block;
                                                                            ">
                                                                            <i class="fas fa-exclamation-triangle"
                                                                                style="color: #ff0000; margin-right: 10px;"></i>
                                                                            ¿Está seguro de eliminar la INSCRIPCIÓN
                                                                            DE:<br>
                                                                            {{ $carrera->apellidos_pater_postulante }}
                                                                            {{ $carrera->apellidos_mater_postulante }}
                                                                            {{ $carrera->nombres_postulante }}?
                                                                        </label>
                                                                    </div>

                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-success"
                                                                            data-dismiss="modal"><i
                                                                                class="fas fa-window-close"></i>
                                                                            Cancelar</button>
                                                                        <button type="submit"
                                                                            class="btn btn-danger"><i
                                                                                class="fas fa-save"></i>
                                                                            Eliminar</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="formModal1{{ $carrera->idpostulante }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"><i
                                                                        class="fas fa-edit"></i> <i
                                                                        class="far fa-list-alt"></i>
                                                                    Cambiar de carrera</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('inscripcion.cambiar') }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="id_inscrip"
                                                                        value="{{ $carrera->idinscripcion }}">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="">{{ $carrera->apellidos_pater_postulante }}
                                                                            {{ $carrera->apellidos_mater_postulante }}
                                                                            {{ $carrera->nombres_postulante }}</label>

                                                                    </div>
                                                                    <div class="col-sm-12 mb-3">
                                                                        @livewire('cambiar-carrera', ['idInscripcion' => $carrera->idinscripcion])
                                                                    </div>


                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal"><i
                                                                                class="fas fa-window-close"></i>
                                                                            Cerrar</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary"><i
                                                                                class="fas fa-save"></i>
                                                                            Inscribir</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> No se encontraron registros para esta
                            modalidad.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
</div>
