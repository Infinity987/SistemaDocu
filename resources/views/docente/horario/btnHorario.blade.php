<form action="{{ route('docente.verHorario') }}" method="POST" style="display:inline;">
    @csrf
    <input type="hidden" name="iddocente_curso" value="{{ $iddocente_curso }}">
    <input type="hidden" name="estado" value="{{ $estado }}">
    <input type="hidden" name="idciclos" value="{{ $idciclos }}">

    <input type="hidden" name="nombre_de_carrera" value="{{ $nombre_de_carrera }}">
    <input type="hidden" name="nombre_ciclo" value="{{ $nombre_ciclo }}">
    <input type="hidden" name="nombre_turno" value="{{ $nombre_turno }}">
    <input type="hidden" name="codigo_aula" value="{{ $codigo_aula }}">
    <input type="hidden" name="aula_nombre" value="{{ $aula_nombre }}">
    <input type="hidden" name="año" value="{{ $año }}">
    <input type="hidden" name="periodo" value="{{ $periodo }}">

    <input type="hidden" name="año_de_inicio" value="{{ $año_de_inicio }}">
    <input type="hidden" name="nom_seccion" value="{{ $nom_seccion }}">
    <input type="hidden" name="idmalla_curricular" value="{{ $idmalla_curricular }}">

    <input type="hidden" name="tipodocente_curso" value="{{ $tipodocente_curso }}">

    <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver</button>
</form>
