<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool   $remember = false;

    public function render()
    {
        // apply your auth layout here
        return view('livewire.login')->layout('components.layouts.login');
    }

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited();

        $persona = Persona::where('email', $this->email)->first();
        if (! $persona) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $usuario = Usuario::where('persona_id', $persona->id)->first();
        if (! $usuario || ! \Hash::check($this->password, $usuario->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        Auth::login($usuario, $this->remember);

        if ($usuario->hasRole('empleado')) {
            $this->redirect(route('user.selectDesk'));
            return;       // early exit, no return value
        }

        $this->redirect(route('dashboard'));
        return;      
    }

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

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
