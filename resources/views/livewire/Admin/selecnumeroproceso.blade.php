<div class="container">
  <div class="row">
    <!-- Proceso -->
    <div class="col-sm-4 mb-3">
      <div class="form-group">
        <label for="proceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
        <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" id="proceso" class="form-control">
          <option value="">Seleccione un proceso</option>
          @foreach ($procesos as $proceso)
            <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Modalidad -->
    @if (!is_null($selectedProceso))
      <div class="col-sm-4 mb-3">
        <div class="form-group">
          <label for="modalidad">MODALIDAD:</label>
          <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)" id="modalidad" class="form-control">
            <option value="">Seleccione una modalidad</option>
            @foreach ($modalidad as $mod)
              <option value="{{ $mod->idmodalidad }}">{{ $mod->nombre_modalidad }}</option>
            @endforeach
          </select>
        </div>
      </div>
    @endif

    <!-- Carreras -->
    @if (!is_null($selectedModalidad))
      <div class="col-sm-4 mb-3">
        <div class="form-group">
          <label for="carrera">CARRERA:</label>
          <select wire:model="selectedCarreraId" wire:change="$refresh" id="carrera" class="form-control">
            <option value="">Seleccione una carrera</option>
            @foreach ($carreras as $carrera)
              <option value="{{ $carrera->idvacantes }}">{{ $carrera->nombre_de_carrera }} ({{ $carrera->Numero_de_Inscritos }})</option>
            @endforeach
          </select>
        </div>
      </div>
    @endif



    <!-- Tabla de postulantes -->
    @if ($selectedCarreraId)
      <div class="col-12 mt-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <strong>Postulantes de la carrera seleccionada</strong>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>DNI</th>
                  <th>Apellidos</th>
                  <th>Nombres</th>
                  <th>Edad</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($postulantes as $index => $postulante)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $postulante->idpostulante}}</td>
                    <td>{{ $postulante->apellidos_pater_postulante }} {{ $postulante->apellidos_mater_postulante }}</td>
                    <td>{{ $postulante->nombres_postulante }}</td>
                    <td>{{ $postulante->edad_postulante }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            @if ($postulantes->count())
  <div class="mt-3">
    <button wire:click="exportarExcel" class="btn btn-success">
      <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>
  </div>
@endif
          </div>
          <div class="mt-2">
            {{ $postulantes->links() }}
          </div>
        </div>
      </div>
    @endif
  </div>
</div>