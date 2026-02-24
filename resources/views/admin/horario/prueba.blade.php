<div class="d-flex gap-3">
    <form class="form-eliminar" id="form-eliminar" action="{{ route('deleteHorario.elimi') }}" method="post">
        @csrf
        <input type="hidden" name="idmalla" value="{{ $idmalla }}">
        <input type="hidden" name="semestre_acad" value="{{ $semestre_acad }}">
        <input type="hidden" name="selectCiclo" value="{{ $selectCiclo }}">
        <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">
        <input type="hidden" name="turno" value="{{ $turno }}">
        <input type="hidden" name="aula" value="{{ $aula }}">
        <input type="hidden" name="idseccion" value="{{ $idseccion }}">

        <button type="submit" title="Eliminar" class="btn btn-sm btn-danger mr-1"><i class="fas fa-trash"></i> </button>
    </form>

    <form action="{{ route('agreindex.index') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="selectCarrera" value="{{ $selectCarrera }}">
        <input type="hidden" name="selectAnioMallaCu" value="{{ $selectAnioMallaCu }}">
        <input type="hidden" name="idmalla" value="{{ $idmalla }}">
        <input type="hidden" name="semestre_acad" value="{{ $semestre_acad }}">
        <input type="hidden" name="selectCiclo" value="{{ $selectCiclo }}">
        <input type="hidden" name="nomCarrera" value="{{ $nomCarrera }}">
        <input type="hidden" name="nomSemestre" value="{{ $nomSemestre }}">
        <input type="hidden" name="nombreTipoDocenCurso" value="{{ $nombreTipoDocenCurso }}">
        <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">
        <input type="hidden" name="nomAño" value="{{ $nomAño }}">
        <input type="hidden" name="nomciclo" value="{{ $nomciclo }}">
        <input type="hidden" name="activaHorario" value="{{ $activaHorario }}">

        <input type="hidden" name="turno" value="{{ $turno }}">
        <input type="hidden" name="aula" value="{{ $aula }}">

        <input type="hidden" name="tipoo" value="{{ $tipoo }}">

        <input type="hidden" name="editar" value="1">
        <button type="submit" title="Editar" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> </button>
    </form>
</div>
