<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Administración</b></a>
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Inicia sesión para empezar tu sesión</p>

      {{-- Error message --}}
      @if ($errors->any())
        <div class="alert alert-danger text-center mb-3 p-2">
          Credenciales incorrectas
        </div>
      @endif

      <form wire:submit.prevent="login">
        {{-- Email --}}
        <div class="input-group mb-3">
          <input
            wire:model.defer="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="Correo electrónico"
            required
            autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
          <input
            wire:model.defer="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="Contraseña"
            required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        {{-- Remember me & Submit --}}
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input
                wire:model="remember"
                type="checkbox"
                id="remember">
              <label for="remember">Recordarme</label>
            </div>
          </div>

          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">
              Entrar
            </button>
          </div>
        </div>
      </form>

      {{-- Hidden links removed --}}
    </div>
  </div>
</div>
