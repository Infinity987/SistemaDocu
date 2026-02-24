<div>
    <form action="{{ route('agreindex.index') }}" method="post">
        @csrf
        <div class="">
            <!-- /.card-header -->
            <div class="card-body p-1 pt-2">
                <div class="container">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="carrera"><i class="fas fa-code-branch"></i> Carrera profesional</label>
                            <select wire:model="selectCarrera" name="selectCarrera"
                                wire:change="traerAniosMallaPorCarrera($event.target.value)" id="selectCarrera"
                                class="form-control">
                                <option value="0">Selecciona carrera</option>
                                @foreach ($carreras as $carrera)
                                    <option value="{{ $carrera->idcarreras }}">{{ $carrera->nombre_de_carrera }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (!is_null($selectCarrera))
                            <div class="form-group col-md-2">
                                <label for="selectAnioMallaCu"><i class="fas fa-table"></i> Año malla curricular</label>
                                <select wire:model='selectAnioMallaCu' name="selectAnioMallaCu"
                                    wire:change='traerSemesAca($event.target.value)' id="selectAnioMallaCu"
                                    class="form-control">
                                    <option value="0">Selecciona año</option>
                                    @foreach ($años as $año)
                                        <option value="{{ $año->idmalla_curricular }}">{{ $año->año_de_inicio }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="idmalla" value="{{ $idmalla }}">
                                <input type="hidden" name="nomCarrera" value="{{ $nomCarrera->nombre_de_carrera }}">
                            </div>
                        @endif

                        @if (!is_null($selectAnioMallaCu))
                            <div class="form-group col-md-2">
                                <label for="semestre_acad"><i class="fab fa-stumbleupon"></i> Semestre académico</label>
                                <select wire:model='selecdSemesAca' wire:change='traerCiclos($event.target.value)'
                                    id="semestre_acad" name="semestre_acad" class="form-control">
                                    <option value="0">Selecciona periodo</option>
                                    @foreach ($semestroAcade as $semestroAcad)
                                        <option value="{{ $semestroAcad->idsemestre_academico }}">
                                            {{ $semestroAcad->año }} -
                                            {{ $semestroAcad->periodo }}</option>
                                    @endforeach
                                    <input type="hidden" name="nomAño" value="{{ $nomAño->año_de_inicio }}">
                                </select>
                                @error('selecdSemesAca')
                                    <div class="text-danger error-selecdSemesAca">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (!is_null($selecdSemesAca))
                            <div class="form-group col-md-2">
                                <label for="selectCiclo"><i class="fas fa-sync-alt"></i> Ciclos</label>
                                <select wire:model='selectCiclo' name="selectCiclo"
                                    wire:change='traerTipo($event.target.value)' id="selectCiclo" class="form-control">
                                    <option value = "0">Selecciona ciclo</option>
                                    @foreach ($ciclos as $ciclo)
                                        <option value="{{ $ciclo->idciclos }}">{{ $ciclo->nombre_ciclo }}</option>
                                    @endforeach
                                    <input type="hidden" name="nomSemestre"
                                        value="{{ $nomSemestre->año . ' - ' . $nomSemestre->periodo }}">

                                </select>
                            </div>
                        @endif

                        @if (!is_null($selectCiclo))
                            <div class="form-group col-md-2">
                                <label for="selectTipo"><i class="fas fa-tags"></i> tipo</label>
                                <select wire:model='selectTipo' wire:change='traerboton($event.target.value)'
                                    id="selectTipo" class="form-control">
                                    <option value = "0">Selecciona ciclo</option>
                                    @foreach ($selecTipo as $tip)
                                        <option value="{{ $tip->idtipo_matricula }}">{{ $tip->nombre_tipo_matricula }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if (!is_null($selectTipo))
                            <input type="hidden" name="nombreTipoDocenCurso" value="{{ $nombreTipoDocenCurso }}">
                            <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">

                            <div class="form-group col-md-1">
                                <input type="hidden" name="nomciclo" value="{{ $nomciclo->nombre_ciclo }}">
                                <input type="hidden" name="tipoo" value="{{ $tipo }}">
                                <label for="">_</label>
                                <button type="submit" class="btn btn-success form-control"><i
                                        class="fas fa-plus-square"></i></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
