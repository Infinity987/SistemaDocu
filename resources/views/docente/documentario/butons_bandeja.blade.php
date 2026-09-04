@if ($estado === 1)
    <div class="d-grid gap-2 d-md-flex justify-content-md-center" aria-label="Basic mixed styles example">
        <form
            action="{{ route('docente.bandejaRecepcionar', ['idtipo_estado' => 2, 'iddocument' => $iddocument, 'movimient' => $movimient, 'iddependencias_emior' => $iddependencias_emior]) }}"
            method="get">
            <button type="submit" class="btn btn-block bg-gradient-success btn-sm" data-toggle="modal"
                data-target="#editardocu">
                <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
@elseif ($estado === 2)
    <div class="d-grid gap-2 d-md-flex justify-content-md-center" aria-label="Opciones de documento">
        <div class="btn-group">
            <button type="button" class="btn btn-sm bg-gradient-success dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-cogs"></i> Acciones
            </button>
            <ul class="dropdown-menu">
                <li>
                    <form action="{{ route('documentario.enviardocumentos.solucionar', $movimient) }}" method="get">
                        <button class="dropdown-item" type="submit"><i class="fas fa-tools"></i> Dar solución</button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('docente.responderDocumento-docente', $iddocument) }}"
                        method="get">
                        <button class="dropdown-item" type="submit">
                            <i class="fas fa-reply"></i> Responder documento
                        </button>
                    </form>

                </li>
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
