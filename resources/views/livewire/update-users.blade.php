<div>
    <form wire:submit.prevent="updateUser">
        @csrf

        <div class="row mb-3">
            <label for="dni" class="col-md-4 col-form-label text-md-end">{{ __('N° Dni') }} <span
                    style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="dni" type="text" name="dni"
                    oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="8"
                    class="form-control @error('dni') is-invalid @enderror" wire:model='dni' required autocomplete="dni"
                    autofocus>

                @error('dni')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="name" class="col-md-5 col-form-label text-md-end">Apellidos y nombres <span style="color: red">*</span></label>

            <div class="col-md-7">
                <input id="namedate" type="text" class="form-control @error('name') is-invalid @enderror"
                    wire:model='name' required autocomplete="name" autofocus>

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="ncelular" class="col-md-4 col-form-label text-md-end">{{ __('N° Celular') }} <span
                    style="color: red">*</span></label>

            <div class="col-md-8">
                <input id="ncelular" type="number" min="00000001"
                    oninput="this.value = this.value.slice(0, this.maxLength);" maxlength="9" name="ncelular"
                    class="form-control @error('ncelular') is-invalid @enderror" wire:model='ncelular' required
                    autocomplete="ncelular" autofocus>

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
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" wire:model='email' autocomplete="email">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-window-close"></i>
                Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
        </div>
    </form>
</div>
