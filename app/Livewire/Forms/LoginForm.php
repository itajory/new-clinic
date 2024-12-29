<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $login = ''; // Can be either email or username

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'password' => $this->password,
            $this->isEmail() ? 'email' : 'username' => $this->login
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => __('failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return  Auth::user();
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => __('throttle', [
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
        return Str::transliterate(Str::lower($this->login) . '|' . request()->ip());
    }

    protected function isEmail(): bool
    {
        return filter_var($this->login, FILTER_VALIDATE_EMAIL) !== false;
    }
}
