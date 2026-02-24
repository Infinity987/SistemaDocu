@extends('adminlte::page')

@section('title', 'Inscripción')

@section('content_header')
  <link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />

  @livewireStyles

        <div class="callout callout-danger mb-0">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-users"></i> - MODULO DE INSCRIPCION</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">inicio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
@stop

@section('content')
  <div class="container-fluid pb-2">
    <div class="row justify-content-between">
      <div class="col-sm-4 d-flex justify-content-start">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal">
          <i class="fas fa-plus-square"></i> Inscribir nuevo postulante
        </button>
      </div>
      <div class="col-sm-4 d-flex justify-content-end">
      </div>
    </div>
  </div>

  <div class="container-fluid pt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-info">
          <div class="card-header ">
            <h3 class="card-title"><i class="fas fa-table"></i> TABLA DE INSCRITOS</h3>
          </div>
          <div class="card-body">
            <livewire:selectinscri />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="far fa-list-alt"></i> Formulario de Inscripción
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{ route('inscripcion.agregarinscripcion') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="form-group">
            <label for="postulante"><i class="fas fa-user"></i> Postulante:</label>
            <select id="postulante" name="postulante" class="form-control select2" required>
              <span id="usuario_error" class="text-danger"></span>
            </select>
          </div>

          <div class="form-group">
            <label><i class="fas fa-file-alt"></i> Documentos entregados:</label>
            <div class="d-flex flex-wrap gap-3 mt-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="documento_dni" name="documento_dni" value="1">
                <label class="form-check-label" for="documento_dni">
                  <i class="fas fa-id-card"></i> DNI
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="documento_certificado" name="documento_certificado" value="1">
                <label class="form-check-label" for="documento_certificado">
                  <i class="fas fa-file-signature"></i> Certificado de Estudios
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="boleta"><i class="fas fa-receipt"></i> Número de boleta:</label>
            <input type="text" class="form-control border-info" id="boleta" name="boleta" required placeholder="Ej. 2025-00123">
          </div>

          <div class="col-sm-12 mb-3">
            @livewire('selectvacantes')
          </div>

          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-window-close"></i> Cerrar
            </button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i> Inscribir
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@stop

@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
  <style>
    .select2-container--default .select2-selection--single {
      height: calc(2.25rem + 2px) !important;
      padding: 0.375rem 0.75rem;
      font-size: 1rem;
      line-height: 1.5;
    }

    .select2-container {
      width: 100% !important;
    }
  </style>
  @livewireStyles
@stop

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
  <!-- Select2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  @livewireScripts

  <script>
    // BS-Stepper Init
    // document.addEventListener('DOMContentLoaded', function() {
    //   window.stepper = new Stepper(document.querySelector('.bs-stepper'))
    // })
  </script>

  <script>
    $(function() {
      $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>

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
  @stack('scripts')
  <script>
    $(document).ready(function() {
      $('#postulante').select2({
        language: {
          noResults: function() {
            return "No se encontraron resultados";
          },
          searching: function() {
            return "Buscando...";
          }
        },
        placeholder: 'Buscar por DNI O APELLIDOS Y NOMBRES...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#formModal'),
        ajax: {
          url: '{{ route('inscripcion.buscarPostul') }}',
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              q: params.term
            };
          },
          processResults: function(data) {
            return {
              results: data.map(function(user) {
                return {
                  id: user.idpostulante,
                  text: user.texto
                }
              })
            };
          },
          cache: true
        }
      }).on('select2:open', function(e) {
        e.stopPropagation();
      });;
    });

    function initDataTable() {
      const table = $('#tablainscritos');

      if ($.fn.DataTable.isDataTable(table)) {
        console.log("🧹 Destruyendo instancia previa de DataTable");
        table.DataTable().destroy();
      }

      if (table.length && table.find('tbody tr').length > 0) {
        console.log("📊 Inicializando DataTable");
        table.DataTable({
          destroy: true,
          responsive: true,
          language: {
            decimal: "",
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            lengthMenu: "Mostrar _MENU_ registros",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron registros que coincidan",
            paginate: {
              first: "Primero",
              last: "Último",
              next: "Siguiente",
              previous: "Anterior"
            }
          },
          columnDefs: [{
            targets: 1, // Ajusta el índice según tu tabla
            type: "string"
          }]
        });
      } else {
        console.log("⚠️ Tabla vacía o no encontrada");
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      Livewire.on('tablaActualizada', () => {
        console.log('📢 Evento Livewire: tablaActualizada');

        // Esperamos un poco para que Livewire termine de renderizar el DOM
        setTimeout(() => {
          initDataTable();
        }, 300);
      });

      initDataTable(); // por si ya está cargada la tabla inicialmente
    });
  </script>

@stop
