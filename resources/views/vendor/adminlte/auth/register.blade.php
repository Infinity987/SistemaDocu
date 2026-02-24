@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        @csrf

        {{-- Name field --}}
        {{-- <div class="input-group mb-3">
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name') }}" placeholder="Ingrese sus Apellidos y Nombres" autofocus>

      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
        </div>
      </div>

      @error('name')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div> --}}

        {{-- Email field --}}
        {{-- <div class="input-group mb-3">
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}">

      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
        </div>
      </div>

      @error('email')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div> --}}

        {{-- DNI field --}}
        <div class="input-group mb-3">
            <input type="number" min="0000000001" oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="10"
                name="dni" class="form-control @error('dni') is-invalid @enderror" value="{{ old('dni') }}"
                placeholder="{{ __('N° Dni ó Carnet de extranjeria') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('dni')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div id="password-error" class="text-danger"></div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.retype_password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div id="confirm-password-error" class="text-danger"></div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $login_url }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var passwordInput = document.getElementById("password");
        var confirmPasswordInput = document.getElementById("password_confirmation");
        var submitButton = document.querySelector("button[type='submit']");

        var passwordError = document.getElementById("password-error"); // Nuevo div para el mensaje
        var confirmPasswordError = document.getElementById("confirm-password-error"); // Nuevo div

        passwordInput.addEventListener("input", validatePassword);
        confirmPasswordInput.addEventListener("input", validatePassword);

        function validatePassword() {
            var password = passwordInput.value;
            var regexUpper = /[A-Z]/;
            var regexNumber = /[0-9]/;
            var errorMessage = "";

            if (password.length < 8) {
                errorMessage = "Debe tener al menos 8 caracteres.";
            } else if (!regexUpper.test(password)) {
                errorMessage = "Debe incluir al menos una letra mayúscula.";
            } else if (!regexNumber.test(password)) {
                errorMessage = "Debe incluir al menos un número.";
            }

            // Mostrar error debajo del campo de contraseña
            passwordError.innerHTML = errorMessage;

            // Validar si las contraseñas coinciden
            if (confirmPasswordInput.value !== "" && confirmPasswordInput.value !== password) {
                confirmPasswordError.innerHTML = "Las contraseñas no coinciden.";
            } else {
                confirmPasswordError.innerHTML = "";
            }

            // Habilitar o deshabilitar el botón de registro según la validación
            submitButton.disabled = passwordError.innerHTML !== "" || confirmPasswordError.innerHTML !== "";
        }
    });
</script>
