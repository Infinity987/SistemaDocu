<div class="container">
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
                    <CENter>
                        <div class="bg-lightblue disabled color-palette">
                            <h2>TABLA DE CONSTANCIAS</h2>
                        </div>
                    </CENter>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre de modalidad</th>
                                    <th>Dni postulante</th>
                                    <th>Datos</th>
                                    <th>Carrera</th>
                                    <th>ESTADO</th>
                                    <th>CONSTANCIA DE INGRESO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carreras as $index => $carrera)
                                    <tr>
                                        <td>
                                            <textarea class="form-control" id="nombre_proceso" name="nombre_proceso" readonly>{{ $carrera->nombre_modalidad }}</textarea>
                                        </td>
                                        <input type="hidden" id="idincripcion" name="idincripcion"
                                            value="{{ $carrera->idinscripcion }}">
                                        <input type="hidden" id="idproceso" name="idproceso"
                                            value="{{ $carrera->idprocesos }}">
                                        <td width="150px">
                                            <input type="number" class="form-control" id="idpostulante"
                                                name="idpostulante" value="{{ $carrera->idpostulante }}" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control" id="datos" name="datos" readonly>{{ $carrera->apellidos_pater_postulante }} {{ $carrera->apellidos_mater_postulante }} {{ $carrera->nombres_postulante }}</textarea>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="carreras" name="carreras"
                                                value="{{ $carrera->nombre_de_carrera }}" readonly>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control" id="estados" name="estado"
                                                value="{{ $carrera->estado_ingreso }}" readonly>
                                        </td>
                                        <td>
                                            <form action="{{ route('pdf.fichaconstancia') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <button class="btn btn-info" type="submit" id="idresultado"
                                                    name="idresultado" value="{{ $carrera->idresultados }}"><i
                                                        class="fas fa-file-pdf"></i>&nbsp;Generar constancia</button>
                                            </form>
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
