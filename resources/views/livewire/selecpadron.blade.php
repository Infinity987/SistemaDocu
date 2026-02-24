<div class="container mt-4">
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      <strong><i class="fas fa-project-diagram"></i> Selección de Proceso y Modalidad</strong>
    </div>
    <div class="card-body">

      <!-- Agrupación de configuración inicial -->
      <fieldset class="border p-3 mb-4">
        <legend class="w-auto px-2 text-primary"><i class="fas fa-cogs"></i> Configuración Inicial</legend>
        <div class="row">
          <!-- Proceso -->
          <div class="col-sm-4 mb-3">
            <div class="form-group">
              <label for="idproceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
              <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" id="idproceso" name="idproceso" class="form-control">
                <option value="">Seleccione un proceso</option>
                @foreach($procesos as $proceso)
                  <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Modalidad -->
          @if (!is_null($selectedProceso))
          <div class="col-sm-4 mb-3">
            <div class="form-group">
              <label for="modalidad"><i class="fas fa-route"></i> MODALIDAD:</label>
              <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)" id="modalidad" name="modalidad" class="form-control">
                <option value="">Seleccione una modalidad</option>
                @foreach($modalidad as $modalidades)
                  <option value="{{ $modalidades->idmodalidad }}">{{ $modalidades->nombre_modalidad }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @endif

          <!-- Total de postulantes -->
          @if (!is_null($selectedModalidad))
          <div class="col-sm-4 mb-3">
            <div class="form-group">
              <label for="numerototalpostu"><i class="fas fa-user-check"></i> CANTIDAD DE POSTULANTES:</label>
              @foreach($carreras as $carrera)
                <input type="text" class="form-control bg-light" id="numerototalpostu" name="numerototalpostu" value="{{ $carrera->total }}" readonly>
              @endforeach
            </div>
          </div>
          @endif
        </div>
      </fieldset>

      <!-- División de alumnos -->
      <fieldset class="border p-3 mb-4">
        <legend class="w-auto px-2 text-success"><i class="fas fa-users-cog"></i> Distribución de Alumnos</legend>
        <div class="row">
          <!-- Alumnos por aula -->
          <div class="col-sm-6 mb-3">
            <div class="form-group">
              <label for="numerodealumnosadividir"><i class="fas fa-users-cog"></i> CUANTOS ALUMNOS POR AULA:</label>
              <input type="text" class="form-control form-control-lg" id="numerodealumnosadividir" name="numerodealumnosadividir" wire:model="numerodealumnosadividir" placeholder="Ej. 25">
              @if ($numerodeaula && $carreras[0]->total % $numerodealumnosadividir != 0)
                <div class="alert alert-warning mt-2 p-2">
                  <i class="fas fa-exclamation-triangle"></i> La división no es exacta. El último grupo tendrá menos alumnos.
                </div>
              @endif
            </div>
          </div>

          <!-- Botón de cálculo -->
          <div class="col-sm-6 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-success btn-block" wire:click="calcularDistribucion" data-toggle="tooltip" title="Calcula la cantidad de aulas necesarias">
              <i class="fas fa-calculator"></i> Calcular distribución
            </button>
          </div>

          <!-- Aulas a dividir -->
          <div class="col-sm-6 mb-3">
            <div class="form-group">
              <label for="numerodeaula"><i class="fas fa-chalkboard-teacher"></i> AULAS A DIVIDIR:</label>
              <input type="text" class="form-control bg-light" id="numerodeaula" name="numerodeaula" wire:model="numerodeaula" readonly>
            </div>
          </div>
        </div>
      </fieldset>

      <!-- Tabla de distribución -->
      @if (count($distribucion) > 0)
      <div class="mt-4">
        <h5 class="text-info"><i class="fas fa-table"></i> Distribución por Aula</h5>
        <p class="text-muted">Total de aulas: <strong>{{ count($distribucion) }}</strong></p>
        <table class="table table-bordered table-striped table-hover">
          <thead class="thead-light">
            <tr>
              <th>Aula</th>
              <th>Cantidad de alumnos</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($distribucion as $grupo)
              <tr>
                <td>Aula {{ $grupo['aula'] }}</td>
                <td>{{ $grupo['cantidad'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

    </div>
  </div>
</div>