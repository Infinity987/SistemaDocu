@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))
@php($password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
    @php($password_reset_url = $password_reset_url ? route($password_reset_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
    @php($password_reset_url = $password_reset_url ? url($password_reset_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')
    <form action="{{ $login_url }}" method="post" id="loginForm">
        @csrf
        {{-- dni field --}}
        @if ($errors->has('message'))
            <div class="alert alert-warning">
                {{ $errors->first('message') }}
            </div>
        @endif
        <div class="input-group mb-3">
            <input type="number" min="0000000001" oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="10"
                name="dni" class="form-control @error('dni') is-invalid @enderror" value="{{ old('dni') }}"
                placeholder="N° Dni ó Carnet de extranjeria" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-money-check {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('dni')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email field --}}
        {{-- <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

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

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
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

        {{-- Campo oculto donde JavaScript guardará el token de Google --}}
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        @error('g-recaptcha-response')
            <div class="text-danger mb-3 text-center" style="font-size: 14px;">
                <strong>{{ $message }}</strong>
            </div>
        @enderror

        {{-- Login field --}}
        <div class="row justify-content-center">
            {{-- <div class="col-7">
            <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

            <label for="remember">
                {{ __('adminlte::adminlte.remember_me') }}
            </label>
            </div>
        </div> --}}

            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    {{-- @if ($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif --}}

    {{-- Register link --}}
    {{-- @if ($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}" style="color: rgb(146, 83, 0);"><i class="fas fa-user-plus"></i>
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif --}}
    @if ($register_url)
        @if ($institucion == 1)
            <p class="my-0">
                <a href="{{ $register_url }}" style="color: rgb(146, 83, 0);"><i class="fas fa-user-plus"></i>
                    {{ __('adminlte::adminlte.register_a_new_membership') }}
                </a>
            </p>
        @endif
    @endif
@stop

{{-- @section('js')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    <script>
        $('#loginForm').submit(function(e) {
            e.preventDefault();

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {action: 'login'}).then(function(token) {
                    $('#g-recaptcha-response').val(token);
                    $('#loginForm').off('submit').submit();
                });
            });
        });
    </script>
@endsection --}}

<script>
    document.getElementById('dniForm').addEventListener('submit', function(event) {
        var dniInput = document.getElementById('dni');
        if (dniInput.value.length !== 10) {
            event.preventDefault();
        }
    });
</script>
