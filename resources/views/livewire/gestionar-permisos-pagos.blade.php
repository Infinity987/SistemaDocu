<div>
   <div>
    {{-- Notificaciones del Sistema --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error</h5>
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulario de Asignación --}}
    <div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-shield"></i> Asignar Permisos de Visualización de Pagos</h3>
    </div>
    <form wire:submit.prevent="guardarPermiso">
        <div class="card-body">
            <div class="row">
                
                {{-- Selector Buscador de Conceptos TUPA --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tupa_input_admin">1. Seleccione o Escriba Concepto TUPA:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="tupa_input_admin" class="form-control" 
                                   placeholder="Click para ver todos o escriba..." 
                                   wire:model="tupa_seleccionado"
                                   list="admin_tupas_list"
                                   autocomplete="off">
                        </div>
                        
                        <datalist id="admin_tupas_list">
                            @foreach($conceptosTupa as $concepto)
                                <option value="{{ $concepto }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                {{-- Selector Buscador de Dependencias (Roles) --}}
                {{-- Selector Buscador de Dependencias (Roles) --}}
<div class="col-md-6">
    <div class="form-group">
        <label for="depen_input_admin">2. Seleccione o Escriba Dependencia Destino:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-building"></i></span>
            </div>
            <input type="text" id="depen_input_admin" class="form-control" 
                   placeholder="Click para ver todas o escriba..." 
                   wire:model="dependencia_seleccionada"
                   list="admin_depen_list"
                   autocomplete="off">
        </div>

        {{-- Catálogo corregido: Ahora el value almacena el Nombre del Rol --}}
        <datalist id="admin_depen_list">
            @foreach($dependencias as $depen)
                @if($depen->id != 9)
                    <option value="{{ $depen->name }}">
                @endif
            @endforeach
        </datalist>
    </div>
</div>

            </div>
        </div>
        <div class="card-footer bg-white text-right">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-save"></i> Guardar Permiso
            </button>
        </div>
    </form>
</div>

    {{-- Tabla de Control de Permisos Existentes --}}
   {{-- Card con Sistema de Pestañas para el Control de Permisos --}}
   {{-- Card con Sistema de Pestañas, Buscador Interno y Paginación --}}
    <div class="card card-danger card-outline card-tabs mt-4">
        <div class="card-header p-0 pt-1 border-bottom-0 row align-items-center">
            
            {{-- Pestañas a la izquierda --}}
            <div class="col-md-8">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-list nav-link active" id="permisos-activos-tab" data-toggle="pill" href="#permisos-activos" role="tab" aria-controls="permisos-activos" aria-selected="true">
                            <i class="fas fa-list-alt text-success"></i> Matriz de Permisos Configurados 
                            <span class="badge badge-success ml-2">{{ $totalActivosCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-list nav-link" id="tupas-sin-asignar-tab" data-toggle="pill" href="#tupas-sin-asignar" role="tab" aria-controls="tupas-sin-asignar" aria-selected="false">
                            <i class="fas fa-exclamation-triangle text-warning"></i> Conceptos TUPA sin Asignar 
                            <span class="badge badge-warning ml-2">{{ count($tupasHuerfanos) }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- 🔍 FILTRO BUSCADOR EN TIEMPO REAL A LA DERECHA --}}
            <div class="col-md-4 p-2 pr-3">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" 
                           placeholder="Buscar por Dependencia o Concepto..." 
                           wire:model.live="searchMatriz"> {{-- .live hace que busque instantáneamente mientras escribe --}}
                    <div class="input-group-append">
                        <span class="input-group-text bg-danger text-white"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                
                {{-- CONTENIDO PESTAÑA 1: PERMISOS ACTIVOS --}}
                <div class="tab-pane fade show active table-responsive" id="permisos-activos" role="tabpanel" aria-labelledby="permisos-activos-tab">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-dark">
                            <tr>
                                <th style="width: 10%">ID Dep.</th>
                                <th style="width: 35%">Dependencia / Oficina</th>
                                <th style="width: 45%">Concepto TUPA Permitido</th>
                                <th style="width: 10%" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permisosActivos as $permiso)
                                <tr>
                                    <td class="align-middle"><span class="badge badge-secondary"># {{ $permiso->id_dependencia }}</span></td>
                                    <td class="align-middle text-bold">{{ $permiso->nombre_dependencia }}</td>
                                    <td class="align-middle text-primary">{{ $permiso->nombre_tupa }}</td>
                                    <td class="text-center align-middle">
                                        <button type="button" 
                                                wire:click="eliminarPermiso({{ $permiso->id_verpagos }})" 
                                                wire:confirm="¿Está seguro de que desea revocar este permiso?"
                                                class="btn btn-xs btn-outline-danger" 
                                                title="Eliminar Permiso">
                                            <i class="fas fa-trash-alt"></i> Quitar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">
                                        <i class="fas fa-search fa-2x mb-2"></i><br>
                                        No se encontraron permisos registrados o que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- 📄 ENLACES DE PAGINACIÓN FLUIDA --}}
                    @if($permisosActivos->hasPages())
                        <div class="card-footer clearfix bg-white text-center d-flex justify-content-center">
                            {{ $permisosActivos->links() }}
                        </div>
                    @endif
                </div>

                {{-- CONTENIDO PESTAÑA 2: CONCEPTOS SIN ASIGNAR --}}
                <div class="tab-pane fade table-responsive" id="tupas-sin-asignar" role="tabpanel" aria-labelledby="tupas-sin-asignar-tab">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-warning text-dark">
                            <tr>
                                <th style="width: 15%">Código Referencia</th>
                                <th style="width: 65%">Concepto TUPA en Riesgo (Alerta: Ninguna oficina puede auditar este cobro)</th>
                                <th style="width: 20%" class="text-center">Acción Rápida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tupasHuerfanos as $huerfano)
                                <tr>
                                    <td class="align-middle"><span class="badge badge-dark">{{ $huerfano->tupCod }}</span></td>
                                    <td class="align-middle text-dark font-weight-normal">{{ $huerfano->tupDes }}</td>
                                    <td class="text-center align-middle">
                                        <button type="button" 
                                                wire:click="$set('tupa_seleccionado', '{{ $huerfano->tupDes }}')"
                                                class="btn btn-xs btn-dark"
                                                onclick="document.getElementById('tupa_input_admin').focus();"
                                                title="Asignar este concepto">
                                            <i class="fas fa-arrow-up"></i> Cargar para Asignar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-success p-4">
                                        <i class="fas fa-check-circle fa-3x mb-2"></i><br>
                                        <strong>¡Excelente control!</strong> Todos los conceptos del TUPA tienen asignada al menos una dependencia responsable.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
