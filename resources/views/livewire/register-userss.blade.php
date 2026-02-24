<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="register" id="miFormulario">
        @csrf
        <div class="row mb-3 body">
            <label for="role" class="col-md-4 col-form-label text-md-end">Rol <span style="color: red">*</span></label>

            <div class="col-md-6">
                <select class="form-control" id="role" name="role" wire:model = "rol" required>
                    <option value="">Seleccione un rol</option>
                    @foreach (Spatie\Permission\Models\Role::all() as $role)
                        @if ($role->name != 'postulante' && $role->name != 'alumno' && $role->name != 'egresado')
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="dni" class="col-md-4 col-form-label text-md-end">{{ __('N° Dni') }} <span style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="dni" type="text" name="dni" oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="8" class="form-control @error('dni') is-invalid @enderror"
                    wire:model='dni' required autocomplete="dni" autofocus>

                @error('dni')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="name" class="col-md-5 col-form-label text-md-end">{{ __('Apellidos y Nombres') }} <span style="color: red">*</span></label>

            <div class="col-md-7">
                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    wire:model='name' required autocomplete="name" autofocus>

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="ncelular" class="col-md-4 col-form-label text-md-end">{{ __('N° Celular') }} <span style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="ncelular" type="number" min="00000001" oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="9" name="ncelular" class="form-control @error('ncelular') is-invalid @enderror"
                    wire:model='ncelular' required autocomplete="ncelular" autofocus>

                @error('ncelular')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

            <div class="col-md-8">
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    wire:model='email' autocomplete="email">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }} <span style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    wire:model='password' required autocomplete="new-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="password-confirm"
                class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }} <span style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="password-confirm" type="password" class="form-control" wire:model='password_confirmation'
                    required autocomplete="new-password">
            </div>
        </div>

        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i>
                Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
        </div>
    </form>
</div>
