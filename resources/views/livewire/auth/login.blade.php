<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.login')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        // Find persona by email
        $persona = \App\Models\Persona::where('email', $this->email)->first();

        if (!$persona) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Find usuario by persona_id
        $usuario = \App\Models\Usuario::where('persona_id', $persona->id)->first();

        if (!$usuario) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Verify password
        if (!\Illuminate\Support\Facades\Hash::check($this->password, $usuario->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        \Illuminate\Support\Facades\Session::regenerate();

        // Log the usuario in
        \Illuminate\Support\Facades\Auth::login($usuario, $this->remember);

        // Redirect to dashboard
        if ($usuario->hasRole('empleado')) {
            $this->redirect(route('user.selectDesk'), navigate: true);
            return;
        }

        $this->redirect(route('dashboard'), navigate: true);

    }


    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

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
