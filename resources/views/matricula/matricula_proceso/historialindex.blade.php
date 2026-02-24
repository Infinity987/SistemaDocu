@extends('adminlte::page')

@section('title', 'Padron')

@section('content_header')
    <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

    @livewireStyles

    {{-- <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-7">
                <h1><i class="fas fa-indent"></i>
                    MODULO DE HISTORIAL MATRICULA</h1>
            </div>
        </div>
    </div> --}}

    <div class="callout callout-danger shadow">
    <section class="content-header p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1><i class="fas fa-indent"></i>
                    MODULO DE HISTORIAL MATRICULA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">inicio</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
</div>


@stop

@section('content')

<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">
  Agregar alumno
</button>


<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalPlantillaNotas">
  📥 Generar plantilla de notas
</button>

<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalCargaNotas">
  📤 Cargar notas desde Excel
</button>


                            <livewire:historial-notas-alumno/>


    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar alumno</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="POST" action="{{ route('historialalumno.agregar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">📋 Registro de Alumno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Apellido Paterno</label>
              <input type="text" name="apellidos_paterno" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Apellido Materno</label>
              <input type="text" name="apellidos_materno" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nombres</label>
              <input type="text" name="nombres" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Malla Curricular</label>
              <select name="id_malla" class="form-select" required>
                <option value="">Seleccione una malla…</option>
                @foreach($mallas as $malla)
                  <option value="{{ $malla->idmalla_curricular }}">{{ $malla->nombre_malla_curricular }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
      </div>
      
    </div>
  </div>
</div>

<!-- 📥 Modal para generar plantilla de notas -->
<div class="modal fade" id="modalPlantillaNotas" tabindex="-1" role="dialog" aria-labelledby="modalPlantillaNotasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="GET" action="{{ route('plantilla.notas') }}">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalPlantillaNotasLabel">📥 Generar plantilla de notas por ciclo</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">📅 Semestre académico</label>
              <select name="semestre_id" class="form-select" required>
                <option value="">-- Seleccione semestre --</option>
                @foreach(DB::connection('mysql_segunda')->table('semestre_academico')->orderByDesc('idsemestre_academico')->get() as $sem)
                  <option value="{{ $sem->idsemestre_academico }}">{{ $sem->año }} - {{ $sem->periodo }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">🌀 Ciclo académico</label>
              <select name="ciclo" class="form-select" required>
                <option value="">-- Seleccione ciclo --</option>
                @for ($i = 1; $i <= 10; $i++)
                  <option value="{{ $i }}">Ciclo {{ $i }}</option>
                @endfor
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">📘 Malla curricular</label>
              <select name="malla_id" class="form-select" required>
                <option value="">-- Seleccione malla --</option>
                @foreach($mallas as $malla)
                  <option value="{{ $malla->idmalla_curricular }}">{{ $malla->nombre_malla_curricular }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">📗 Tipo de matrícula</label>
              <select name="tipo_matricula" class="form-select" required>
                <option value="">-- Seleccione tipo --</option>
                <option value="1">Regular</option>
                <option value="2">Subsanación</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">📥 Generar plantilla Excel</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- 📤 Modal para carga masiva de notas -->
<div class="modal fade" id="modalCargaNotas" tabindex="-1" role="dialog" aria-labelledby="modalCargaNotasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" action="{{ route('notas.carga') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalCargaNotasLabel">📤 Carga masiva de notas por ciclo</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info">
            📘 Asegúrese de usar la plantilla oficial. El archivo debe contener:
            <ul>
              <li>Semestre ID</li>
              <li>Tipo de matrícula (1 = Regular, 2 = Subsanación)</li>
              <li>DNI del alumno</li>
              <li>Apellidos y nombres</li>
              <li>Notas y estados por curso (Aprobado / Desaprobado)</li>
            </ul>
            Las dos primeras filas serán ignoradas automáticamente.
          </div>

          <div class="form-group">
            <label for="archivo_excel">📎 Seleccione archivo Excel (.xlsx)</label>
            <input type="file" name="archivo_excel" class="form-control" accept=".xlsx" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">📤 Cargar notas</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


@if (session('resumen'))
    <div class="alert alert-success">
        <h5>📊 Resumen de carga</h5>
        <ul>
            <li>✅ Insertados: {{ session('resumen')['insertados'] }}</li>
            <li>🔁 Omitidos: {{ session('resumen')['omitidos'] }}</li>
            <li>⚠️ Errores: {{ session('resumen')['errores'] }}</li>
            <li>📘 Cursos registrados: {{ implode(', ', session('resumen')['cursos']) }}</li>
            <li>🧑‍🎓 Alumnos procesados: {{ implode(', ', session('resumen')['alumnos']) }}</li>
        </ul>
    </div>
@endif


@stop

@section('js')

    @livewireScripts
    <script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                title: "BUEN TRABAJO!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error"
            });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('abrirModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('formModal'));
                modal.show();
            });

            Livewire.on('cerrarModal', () => {
                const modalElement = document.getElementById('formModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>

    <script>
        
    </script>
    <script>
        $(document).ready(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let targetId = $(e.target).attr('href');
                Livewire.restart(); // esto fuerza a Livewire a refrescar componentes activos
            });
        });
    </script>
    <script>
    @if (session('resumen'))
        window.scrollTo({ top: 0, behavior: 'smooth' });
    @endif
</script>




@stop
