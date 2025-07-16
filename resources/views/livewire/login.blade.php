<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Administración</b></a>
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Inicia sesión para empezar tu sesión</p>

      <form wire:submit.prevent="login">
        <div class="input-group mb-3">
          <input
            wire:model.defer="email"
            type="email"
            class="form-control"
            placeholder="Correo electrónico"
            required
            autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input
            wire:model.defer="password"
            type="password"
            class="form-control"
            placeholder="Contraseña"
            required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

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

      <p class="mb-1 mt-3">
        <a href="{{ route('password.request') }}">Olvidé mi contraseña</a>
      </p>
      <p class="mb-0">
        <a href="{{ route('register') }}" class="text-center">
          Registrar una nueva cuenta
        </a>
      </p>
    </div>
  </div>
</div>
