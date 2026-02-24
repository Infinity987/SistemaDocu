@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<link rel="stylesheet" href="{{ asset('bs-stepper/css/bs-stepper.min.css') }}" />
<link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
<link rel="stylesheet" href="{{ asset('sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    @livewireStyles
    
@stop

@section('content')

    <div class="alert alert-info">
        {{ $mensaje }}
    </div>

    @if ($mostrarFormulario)
        <form >
            @csrf
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" class="form-control" id="nombres" required>
            </div>
            <div class="form-group">
                <label for="apellidos_paternos">Apellido Paterno</label>
                <input type="text" name="apellidos_paternos" class="form-control" id="apellidos_paternos" required>
            </div>
            <div class="form-group">
                <label for="apellidos_maternos">Apellido Materno</label>
                <input type="text" name="apellidos_maternos" class="form-control" id="apellidos_maternos" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    @endif
@stop

@section('js')
<script src="{{ asset('bs-stepper/js/bs-stepper.min.js') }}"></script>
<script src="{{ asset('select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>

@stop