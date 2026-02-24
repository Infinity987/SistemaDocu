<div class="d-flex gap-3">
    {{-- <form class="form-eliminar" id="form-eliminar" > --}}
        {{-- @csrf --}}
        {{-- action="{{ route('eliminarSemes') }}" method="post" --}}
        <button id="btnEliminar" title="Eliminar" class="btn btn-sm btn-danger mr-1 btn-eliminar" data-idsemestre_academico="{{ $idsemestre_academico }}" data-url="{{ route('eliminarSemes', $idsemestre_academico) }}"><i class="fas fa-trash"></i></button>
    {{-- </form> --}}

    {{-- <form action="" method="POST" style="display:inline;"> --}}
        {{-- @csrf --}}
        {{-- <input type="hidden" name="selectCarrera" value="{{ $selectCarrera }}"> --}}

        <button type="submit" title="Editar" class="btn btn-sm btn-info btn-editar" data-idsemestre_academico="{{ $idsemestre_academico }}" data-url="{{ route('verEditarSemes', $idsemestre_academico) }}"><i class="fas fa-edit"></i></button>
    {{-- </form> --}}
</div>
