<div class="d-grid gap-2 d-md-flex justify-content-md-center" aria-label="Basic mixed styles example">
    <form action="{{ route('docente.showEmitidoDoce', $iddocumentos) }}" method="get">
        @if ($id_estado == 1)
            <button type="submit" class="btn btn-block bg-gradient-danger btn-sm" data-toggle="modal"
                data-target="#editardocu">
                <i class="fas fa-edit"></i>
            </button>
        @else
            <button type="submit" class="btn btn-block bg-gradient-success btn-sm" data-toggle="modal"
                data-target="#editardocu">
                <i class="fas fa-eye"></i>
            </button>
        @endif
    </form>
</div>
