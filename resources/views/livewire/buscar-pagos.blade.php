<div>
    {{-- Card de Búsqueda --}}
    <div class="card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title">Criterio de Búsqueda</h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Input DNI (Siempre visible) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dni_input">Ingrese DNI del Alumno:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="text" id="dni_input" class="form-control" placeholder="Escriba 8 dígitos..." 
                                   wire:model.live="dni" maxlength="8">
                        </div>
                    </div>
                </div>

                {{-- Buscador del TUPA (SOLO se muestra si el alumno existe) --}}
                {{-- Buscador del TUPA con Autofiltro y Lista Completa --}}
<div class="col-md-5">
    @if($alumno)
        <div class="form-group fade-in">
            <label for="tupa_input">Seleccionar o Buscar Concepto TUPA:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search-plus"></i></span>
                </div>
                <input type="text" id="tupa_input" class="form-control" 
                       placeholder="Click para ver todos o escriba para filtrar..." 
                       wire:model.live="searchTupa"
                       list="tupas_list"
                       autocomplete="off">
                
                {{-- Si hay algo escrito, mostramos un botón para limpiar rápido el filtro --}}
                @if(!empty($searchTupa))
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('searchTupa', '')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Este es el catálogo oculto que alimenta al input --}}
            <datalist id="tupas_list">
    @foreach($listaTupas as $tupa)
        {{-- Guardamos solo el código en el value, el texto largo va al lado para que el usuario lo lea --}}
        <option value="{{ $tupa->tupCod }}">{{ $tupa->tupDes }}</option>
    @endforeach
</datalist>
        </div>
    @endif
</div>

                {{-- Alerta en caso de No Resultados --}}
                <div class="col-md-3">
                    @if(strlen($dni) == 8 && !$alumno)
                        <div class="alert alert-warning p-2 mb-0 mt-3 text-sm">
                            <i class="fas fa-exclamation-triangle"></i> DNI no registrado.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bloque de Datos del Alumno --}}
            @if($alumno)
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="callout callout-success mb-0 py-2">
                            <p class="mb-0">
                                <strong><i class="fas fa-user"></i> Alumno:</strong> {{ $alumno->aluNom }} 
                                <span class="text-muted ml-3">|</span> 
                                <strong class="ml-3"><i class="fas fa-fingerprint"></i> DNI:</strong> {{ $alumno->aluDni }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabla de Recibos y Pagos --}}
    @if($alumno)
        <div class="card card-danger card-outline">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title"><i class="fas fa-receipt"></i> Historial de Recibos y Pagos</h3>
                @if(!empty($searchTupa))
                    <span class="badge badge-warning ml-3">Filtrado por: "{{ $searchTupa }}"</span>
                @endif
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th># Código</th>
                            <th>Concepto TUPA</th>
                            <th>Fecha de Pago</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Monto U.</th>
                            <th class="text-right">Tupa Mon</th>
                            <th>Observaciones</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recibos as $recibo)
                            <tr>
                                <td class="align-middle"><strong>{{ $recibo->resCod }}</strong></td>
                                <td class="align-middle">
                                    <small class="badge badge-secondary mr-1">{{ $recibo->tupCod }}</small> 
                                    {{ $recibo->tupDes }}
                                </td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($recibo->resFec)->format('d/m/Y') }}</td>
                                <td class="text-center align-middle">{{ $recibo->resCan }}</td>
                                <td class="text-right align-middle text-bold text-success">S/. {{ number_format($recibo->resMonUni, 2) }}</td>
                                <td class="text-right align-middle text-muted">S/. {{ number_format($recibo->tupMon, 2) }}</td>
                                <td class="align-middle">
                                    <span class="text-muted text-sm">{{ $recibo->resObs ?? '-' }}</span>
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted p-4">
                                    <i class="fas fa-search fa-2x mb-2"></i><br>
                                    No se encontraron pagos que coincidan con "{{ $searchTupa }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>