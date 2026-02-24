<div>
  <form wire:submit.prevent="updatePassword">
    @csrf
    <div class="row mb-3">
        <label for="password" class="col-md-5 col-form-label text-md-end">Nueva contraseña</label>
        <div class="col-md-7 position-relative">
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                wire:model="password" id="password">

            <!-- Icono del ojito dentro del input -->
            <i id="toggleConfirmPasswordBtn" class="fas fa-eye"
                onclick="togglePassword('password', 'toggleConfirmPasswordBtn')"
                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            </i>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>


    <div class="row mb-3">
        <label for="password_confirmation" class="col-md-5 col-form-label text-md-end">Confirmar la contraseña</label>
        <div class="col-md-7 position-relative">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                wire:model="password_confirmation" id="password_confirmation">

            <!-- Icono del ojito dentro del input -->
            <i id="toggleConfirmPasswordBtnc" class="fas fa-eye"
                onclick="togglePasswordc('password_confirmation', 'toggleConfirmPasswordBtnc')"
                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            </i>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>


    <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-window-close"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar contraseña</button>
    </div>
  </form>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif
</div>

<script>
  function togglePassword(inputId, iconId) {
    let input = document.getElementById(inputId);
    let icon = document.getElementById(iconId);

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash"); // Cambia el icono a ojo cerrado
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye"); // Cambia el icono a ojo abierto
    }
  }

  function togglePasswordc(inputId, iconId) {
    let input = document.getElementById(inputId);
    let icon = document.getElementById(iconId);

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash"); // Cambia el icono a ojo cerrado
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye"); // Cambia el icono a ojo abierto
    }
  }
</script>
