<div>
    <div>
    <h4>📚 Docentes con cursos de Subsanación</h4>

    <select wire:model.defer="docenteSeleccionado" class="form-select mb-2">
    <option value="">Seleccione un docente…</option>
    @foreach($docentes as $docente)
        <option value="{{ $docente->iddocente }}">{{ $docente->nombre }}</option>
    @endforeach
</select>

<button wire:click="cargarAlumnosDelDocente" class="btn btn-primary mb-3">
    🔍 Ver alumnos del docente seleccionado
</button>

    @if($alumnosDelDocente)

      <button onclick="abrirModalPdf()" class="btn btn-warning">
    🔧 GENERAR EXCEL
</button>

        <table class="table table-bordered">

         
            <thead class="table-light">
                <tr>
                    <th>DNI</th>
                    <th>Alumno</th>
                    <th>Curso</th>
                    <th>Ciclo</th>
                    <th>Carrera</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnosDelDocente as $a)
                    <tr>
                        <td>{{ $a->idpostulante }}</td>
                        <td>{{ $a->apellidos_pater_postulante }} {{ $a->apellidos_mater_postulante }}, {{ $a->nombres_postulante }}</td>
                        <td>{{ $a->nombre_curso }}</td>
                        <td>{{ $a->ciclo_matricula }}</td>
                        <td>{{ $a->nombre_de_carrera }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div wire:ignore.self class="modal fade" id="modalGenerarPdf" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📄 Generar PDF de Subsanación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
      <form method="POST" action="{{ route('pdf.subsanacionmemorando') }}" target="_blank">
    @csrf
   <input type="hidden" name="docente" value="{{ $this->docenteSeleccionadoNombre }}">
    <input type="hidden" name="periodo" value="{{ $this->periodoCompleto }}">
    <input type="hidden" name="idDocente" value="{{ $docenteSeleccionado }}">


          <div class="mb-3">
            <label class="form-label">Título del Memorando</label>
            <input type="text" class="form-control" name="memorando" placeholder="Ej. MEMORANDUM Nº 32-2023/ESA/ESPP 'GBM'-CP">
          </div>

          <div class="mb-3">
            <label class="form-label">Referencia</label>
            <input type="text" class="form-control" name="referencia" placeholder="Ej. Resolución Directoral Nº 311-2023/DGESPP 'GBM'-CP">
          </div>

          <div class="mb-3">
            <label class="form-label">Docente Responsable</label>
            <select class="form-select" wire:model.defer="docenteSeleccionado">
              <option value="">Seleccione un docente…</option>
              @foreach($docentes as $docente)
                <option value="{{ $docente->iddocente }}">{{ $docente->nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">
              🖨️ Generar PDF
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</div>
<script>
    window.addEventListener('mostrar-modal-pdf', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalGenerarPdf'));
        modal.show();
    });
</script>