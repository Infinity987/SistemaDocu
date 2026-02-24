<div>
    <div class="input-group">
        <input type="number" class="form-control col-md-2 mr-2" placeholder="Ingrese DNI" wire:model="dni" />
        <button class="btn btn-primary" wire:click="buscar">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>

    @if (!is_null($matriculas))
        <div class="shadow-lg"
            style="text-align:center; margin:15px; padding:10px; border:1px solid #4b1f02; border-radius:8px; background: linear-gradient(90deg, #693501, #d49d5e); ">
            <h4 style="margin:0; color:#ffffff; font-weight:bold;">
                <i class="fas fa-user" style="color:#ffffff;"></i>
                {{ $alumno->apellidos_pater_postulante }} {{ $alumno->apellidos_mater_postulante }}
            </h4>
            <p style="margin:5px 0 0; color:#e3e3e3; font-size:16px;">
                {{ $alumno->nombres_postulante }}
            </p>
        </div>

        @if ($matriculas->count() > 0)
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle"></i>
                Se encontraron <strong>{{ $matriculas->count() }}</strong> matrículas.
            </div>


            <span class="font-weight-bold">Semestres Académicos:</span>
            <div class="row mt-3">
                {{-- @dump($matriculas) --}}
                @foreach ($matriculas as $item)
                    {{-- @dump( $item->id_alumno ) --}}
                    <div class="col-md-3 col-sm-6 mb-2">
                        <button class="btn btn-danger btn-block"
                            wire:click="irPdf('{{ $item->id_alumno }}', {{ $item->idmatricula }})">
                            <i class="fas fa-file-pdf"></i>
                            {{ $item->año }} - {{ $item->periodo }} ||
                            @php
                                if ($item->idtipo_matricula == 1) {
                                    $nomm = 'REGULAR';
                                } elseif ($item->idtipo_matricula == 2) {
                                    $nomm = 'SUBSANACIÓN';
                                } elseif ($item->idtipo_matricula == 3) {
                                    $nomm = 'REINCORPORACIÓN';
                                } elseif ($item->idtipo_matricula == 4) {
                                    $nomm = 'TRASLADO INTERNO';
                                } elseif ($item->idtipo_matricula == 5) {
                                    $nomm = 'TRASLADO EXTERNO';
                                } elseif ($item->idtipo_matricula == 6) {
                                    $nomm = 'CONVALIDACIÓN Y UBICACIÓN';
                                }
                            @endphp
                            {{ $nomm }} || Ciclo: {{ $item->nombre_ciclo }}
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                No se encontraron matrículas para el DNI ingresado.
            </div>
        @endif
    @endif
</div>
