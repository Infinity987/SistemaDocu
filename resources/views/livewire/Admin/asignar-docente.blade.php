<div>
    {{-- <form action="{{ route('admin.asigCursosDoce') }}" method="post"> --}}
    <form wire:submit.prevent = "confirmarGuardado">
        @csrf

        <!-- /.card-header -->
        <div class="card-body">
            <div class="container">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="carrera"><i class="fas fa-code-branch"></i> Carrera profesional</label>
                        <select wire:model="selectCarrera" wire:change="traerAniosMallaPorCarrera($event.target.value)"
                            id="selectCarrera" class="form-control">
                            <option value="0">Selecciona carrera</option>
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->codigo_de_carrera }}">{{ $carrera->nombre_de_carrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if (!is_null($selectCarrera))
                        <div class="form-group col-md-2">
                            <label for="selectAnioMallaCu"><i class="fas fa-table"></i> Año malla curricular</label>
                            <select wire:model='selectAnioMallaCu' wire:change='traerSemesAca($event.target.value)'
                                id="selectAnioMallaCu" class="form-control">
                                <option value="0">Selecciona año</option>
                                @foreach ($años as $año)
                                    <option value="{{ $año->idmalla_curricular }}">{{ $año->año_de_inicio }}
                                    </option>
                                @endforeach
                            </select>
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
                            </select>
                            @error('selecdSemesAca')
                                <div class="text-danger error-selecdSemesAca">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if (!is_null($selecdSemesAca))
                        <div class="form-group col-md-2">
                            <label for="selectCiclo"><i class="fas fa-sync-alt"></i> Ciclos</label>
                            <select wire:model='selectCiclo' wire:change='traerTipo($event.target.value)'
                                id="selectCiclo" class="form-control">
                                <option value = "0">Selecciona ciclo</option>
                                @foreach ($ciclos as $ciclo)
                                    <option value="{{ $ciclo->idciclos }}">{{ $ciclo->nombre_ciclo }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (!is_null($selectCiclo))
                        <div class="form-group col-md-2">
                            <label for="selectTipo"><i class="fas fa-tags"></i> tipo</label>
                            <select wire:model='selectTipo' wire:change='traerCursos($event.target.value)'
                                id="selectTipo" class="form-control">
                                <option value = "0">Selecciona ciclo</option>
                                @foreach ($selecTipo as $tip)
                                    <option value="{{ $tip->idtipo_matricula }}">{{ $tip->nombre_tipo_matricula }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        @if (!is_null($selectTipo))
            <div class="form-group col-md-12">
                <div class="callout callout-info card card-info pt-0 pl-0 pr-0">
                    <div class="card-header pr-1 pt-2 pb-1">
                        <label><i class="fas fa-bezier-curve"></i> <i class="fas fa-book-reader"></i> Cursos del ciclo
                            @if ($nombreTipoDocenCurso)
                                <span class="btn btn-sm btn-warning">{{ $nombreTipoDocenCurso }}</span>
                            @endif
                        </label>
                    </div>
                    <input type="hidden" name="tipodocente_curso">

                    <div class="card-body pb-0">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12">
                                    {{-- @dump($cursoos) --}}
                                    @foreach ($cursoos as $curso)
                                        <div class="form-group row align-items-center mb-2"
                                            wire:key="curso-{{ $selectCiclo }}-{{ $curso->idcursos }}-{{ (int) $actualizar }}">
                                            <label class="col-sm-7 col-form-label"><i class="fas fa-book"></i> -
                                                {{ $curso->nombre_curso }}
                                            </label>
                                            <div class="col-sm-5">
                                                @if ($curso->dc_iddoce)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control"
                                                            value="{{ $curso->nombre }}" disabled>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="Livewire.dispatch('confirmarEliminacion', { iddocentecurso: {{ $curso->iddocentecurso }} })">
                                                            <i class="fas fa-trash-alt"></i> Quitar
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="col-sm-12" wire:ignore>
                                                        <select wire:model="asignaciones.{{ $curso->idcursos }}"
                                                            class="form-control select2-docente"
                                                            id="docente-select-{{ $curso->idcursos }}"
                                                            data-curso-id="{{ $curso->idcursos }}"
                                                            data-selected="{{ $curso->dc_iddoce ?? '' }}"
                                                            data-selected-text="{{ $curso->nombre ?? '' }}">
                                                            <option value="">Seleccionar docente</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @error('selectCursos')
                                        <div class="text-danger error-selectCursos">{{ $message }}</div>
                                    @enderror


                                </div>
                                <div class="col-sm-12 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"><i
                                            class="fas fa-save"></i> Asignar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </form>
</div>
