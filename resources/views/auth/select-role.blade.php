@extends('adminlte::master')

@section('body')
    <div class="login-bg d-flex align-items-center justify-content-center min-vh-100 p-3">

        <div class="card shadow-lg main-card w-100" style="max-width:500px;">

            <div class="card-header text-center bg-white border-0">
                <h3 style="color: #552e04" class="mb-0 font-weight-bold">Seleccione el rol con el que desea ingresar</h3>
                <small class="text-muted">
                    Sistema de Trámite
                </small>
            </div>

            <div class="card-body">

                <form action="{{ route('set.active.role') }}" method="POST">
                    @csrf

                    @foreach (auth()->user()->roles as $role)
                        <button type="submit" name="role_id" value="{{ $role->id }}"
                            class="btn role-card w-100 d-flex align-items-center justify-content-between mb-3">

                            <div class="d-flex align-items-center">

                                <div class="icon-role mr-3">
                                    <i class="fas fa-user-tag"></i>
                                </div>

                                <div class="text-left">

                                    <div class="font-weight-bold role-name">
                                        {{ strtoupper($role->name) }}
                                    </div>

                                    {{-- <small class="text-muted">
                                        ID Rol: {{ $role->id }}
                                    </small> --}}

                                </div>

                            </div>

                            <i class="fas fa-chevron-right arrow"></i>

                        </button>
                    @endforeach
                </form>

            </div>

            <div class="card-footer text-center bg-white">
                <small class="text-muted">
                    Sus permisos se ajustarán según el rol seleccionado
                </small>
            </div>

        </div>

    </div>

    <style>
        .main-card {
            border: 20px solid #e7e7e7;
            border-radius: 20px;
        }

        /* tarjetas de roles */

        .role-card {

            border: 2px solid #e2d6c9;
            border-radius: 12px;
            background: white;

            transition: all .25s ease;

            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .role-card:hover {

            background: #f3e7db;

            transform: scale(1.05);

            border-color: #c7a17a;

            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        }

        .icon-role {

            width: 45px;
            height: 45px;

            background: #9d7245;
            color: white;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;

            transition: .25s;
        }

        .role-card:hover .icon-role {

            background: #7b5633;

        }

        .role-name {

            color: #4b3423;

        }

        .arrow {

            color: #7b4f21;
            font-size: 16px;

        }

        /* responsive */

        @media (max-width:576px) {

            .icon-role {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .role-name {
                font-size: 14px;
            }

        }

        .login-bg {
            position: relative;
            overflow: hidden;
        }

        .login-bg {
            position: relative;
            min-height: 100vh;
            background-image: url("/foto/gb2.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* capa oscura encima del fondo */

        .login-bg::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background: rgba(127, 65, 3, 0.44);
            /* nivel de oscuridad */

        }

        /* asegurar que el contenido esté encima */

        .login-bg>* {
            position: relative;
            z-index: 1;
        }
    </style>
@endsection
