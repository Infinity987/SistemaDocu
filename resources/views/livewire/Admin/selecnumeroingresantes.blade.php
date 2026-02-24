<div class="container">
    <div class="row">
      <div class="col-sm-4 mb-3">
        <div class="form-group">
          <label for="proceso"><i class="fas fa-project-diagram"></i> PROCESO:</label>
          <select wire:model="selectedProceso" wire:change="handleProcesoChange($event.target.value)" id="proceso"
            class="form-control">
            <option value="">Seleccione un proceso</option>
            @foreach ($procesos as $proceso)
              <option value="{{ $proceso->idprocesos }}">{{ $proceso->nombre_proceso }}</option>
            @endforeach
          </select>
        </div>
      </div>
  
      @if (!is_null($selectedProceso))
          <div class="col-sm-4 mb-3">
              <div class="form-group">
                  <label for="proceso">MODALIDAD:</label>
                  <select wire:model="selectedModalidad" wire:change="handleModalidadChange($event.target.value)" id="modalidad" name="modalidad" class="form-control">
                      <option value="">Seleccione una modalidad</option>
                      @foreach($modalidad as $modalidades)
                          <option value="{{ $modalidades->idmodalidad }}">{{ $modalidades->nombre_modalidad }}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          @endif
  
      @if (!is_null($selectedModalidad))
        <div class="col-12 mb-3">
          <div class="form-group">
            <CENter>
              <div class="bg-lightblue disabled color-palette">
                <h2>TABLA DE REPORTE INGRESANTES POR CARRERA</h2>
              </div>
            </CENter>
  
            <table class="table">
                <thead>
                    <tr>
                        <th>CARRERA</th>
                        <th>NUMERO DE INGRESANTES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carreras as $reporte1)
                        <tr>
                            <td width="650px">
                                <input type="text" class="form-control" id="carrera" name="carrera" value="{{ $reporte1->nombre_de_carrera }}" readonly>
                            </td>
                            <td width="150px">
                                <input type="text" class="form-control" id="numerocarre" name="numerocarre" value="{{ $reporte1->Numero_de_Ingresantes }}" readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td>
                            <input type="text" class="form-control" id="total" name="total" value="{{ $carreras->sum('Numero_de_Inscritos') }}" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
          </div>
        </div>
      @endif
    </div>
  </div>
  