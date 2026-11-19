@extends('layouts.app')

@section('content_header')
    @can('admin.users.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fas fa-fw fa-building" aria-hidden="true"></i> - Editar nombre de una
                                Oficina</h1>
                        </div>
                        <style>
                            @media (max-width: 767px) {
                                #titulo-rol {
                                    font-size: 20px !important;
                                    margin-top: 10px;
                                }
                            }
                        </style>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Editar</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
    @endcan
@stop

@section('content')
    @can('admin.users.index')
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('roles.index') }}" class="btn btn-warning">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver atras
            </a>
        </div>
        {{-- Formulario --}}
        <div class="card-body p-4">

            <form action="{{ route('roles.update', $role->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="form-group">

                    <label for="name" class="office-label">
                        <i class="fas fa-building mr-2"></i>
                        Nombre de la Oficina
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group office-input-group">

                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-briefcase"></i>
                            </span>
                        </div>

                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}"
                            placeholder="Ingrese el nombre de la oficina" required>

                    </div>

                    @error('name')
                        <div class="invalid-feedback d-block mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </div>
                    @enderror

                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ingrese el nombre de la oficina o cargo correspondiente.
                    </small>

                </div>

                <hr class="my-4">

                {{-- Botones --}}
                <div class="d-flex justify-content-end flex-wrap office-form-buttons">

                    <button type="submit" class="btn btn-success mr-2">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar
                    </button>

                    <a href="{{ route('roles.index') }}" class="btn btn-danger">
                        <i class="fas fa-times-circle mr-2"></i>
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

        {{-- <div class="container">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nombre de la Oficina</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div> --}}
    @endcan
@endsection

@section('css')
    <style>
        @media (max-width: 576px) {

            .office-form-header {
                padding: 17px;
            }

            .office-form-header h3 {
                font-size: 16px;
            }

            .office-form-buttons {
                flex-direction: column;
            }

            .office-form-buttons .btn {
                width: 100%;
                margin: 5px 0;
            }

            .office-form-card .card-body {
                padding: 20px !important;
            }
        }
    </style>
@stop
