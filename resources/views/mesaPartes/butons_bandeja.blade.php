@if ($estado === 1)
    <div class="d-grid gap-2 d-md-flex justify-content-md-center" aria-label="Basic mixed styles example">
        <form
            action="{{ route('documentario.bandeja.recibir', ['idtipo_estado' => 2, 'iddocument' => $iddocument, 'movimient' => $movimient, 'iddependencias_emior' => $iddependencias_emior]) }}"
            method="get">
            <button type="submit" class="btn btn-block bg-gradient-success btn-sm" data-toggle="modal"
                data-target="#editardocu">
                <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
@elseif ($estado === 2)
    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
        <div class="btn-group">
            <button type="button" class="btn btn-sm bg-gradient-success dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cogs"></i> Acciones
            </button>
            <ul class="dropdown-menu">
                
                @if ($id_depen == 24)
                    {{-- SOLO PARA MESA DE PARTES --}}
                    <li>
                        <form action="{{ route('documentario.enviardocumentos.derivarDirector', $iddocument) }}" method="POST">
                            @csrf
                            <button class="dropdown-item" type="submit">
                                <i class="fas fa-share-square"></i> Derivar al Director
                            </button>
                        </form>
                    </li>
                @else
                    {{-- PARA TODAS LAS DEMÁS OFICINAS --}}
                    <li>
                        <form action="{{ route('documentario.enviardocumentos.solucionar', $iddocument) }}" method="get">
                            <button class="dropdown-item" type="submit">
                                <i class="fas fa-tools"></i> Dar solución
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('documentario.enviardocumentos.responderDocumento', $iddocument) }}" method="get">
                            <button class="dropdown-item" type="submit">
                                <i class="fas fa-reply"></i> Responder documento
                            </button>
                        </form>
                    </li>
                @endif

            </ul>
        </div>
    </div>
    @elseif ($estado === 3)
    <div class="d-grid gap-2 d-md-flex justify-content-md-center" aria-label="Basic mixed styles example">
        <form action="{{ route('documentario.mesapar.showEmitido', $iddocument) }}" method="get">
            <button type="submit" class="btn btn-block bg-gradient-success btn-sm" data-toggle="modal"
                data-target="#editardocu">
                <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
@endif
