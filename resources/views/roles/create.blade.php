@extends('adminlte::page')

@section('content_header')
    @can('admin.users.index')
        <div class="callout callout-danger mb-0 shadow">
            <section class="content-header p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 id="titulo-rol"><i class="fas fa-fw fa-building" aria-hidden="true"></i> - Registrar nueva Oficina
                            </h1>
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
                                <li class="breadcrumb-item active">Registrar</li>
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
        <div class="container-fluid">

            <div class="card office-form-card">
                {{-- Formulario --}}
                <div class="card-body p-4">

                    <form action="{{ route('roles.store') }}" method="POST">

                        @csrf

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
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
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
                                Ingrese el nombre de la oficina o cargo que desea registrar.
                            </small>

                        </div>

                        <hr class="my-4">

                        {{-- Botones --}}
                        <div class="d-flex justify-content-end flex-wrap office-form-buttons">

                            <button type="submit" class="btn btn-success mr-2">
                                <i class="fas fa-save mr-2"></i>
                                Guardar
                            </button>

                            <a href="{{ route('roles.index') }}" class="btn btn-danger">
                                <i class="fas fa-times-circle mr-2"></i>
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    @endcan
@endsection
