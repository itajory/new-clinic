<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $user = $this->form->authenticate();

        Session::regenerate();
        if ($user->role->name === 'admin') {
            $this->redirectIntended(route('dashboard', false), true);
        } elseif ($user->role->name === 'doctor') {
            $this->redirectIntended(route('doctors.view', $user->id, false),
                true);
        } elseif ($user->role->name === 'receptionist') {
            $this->redirectIntended(route('appointment.index', false), true);
        } else {
            $this->redirectIntended(default: route('dashboard',
                absolute: false), navigate: true);
        }
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4"
                           :status="session('status')"/>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="login"
                           :value="__('Email or Username')"/>
            <x-text-input wire:model="form.login"
                          id="login"
                          class="block w-full mt-1"
                          type="text"
                          name="login"
                          required
                          autofocus
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('form.login')"
                           class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password"
                           :value="__('Password')"/>

            <x-text-input wire:model="form.password"
                          id="password"
                          class="block w-full mt-1"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password"/>

            <x-input-error :messages="$errors->get('form.password')"
                           class="mt-2"/>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember"
                   class="inline-flex items-center">
                <input wire:model="form.remember"
                       id="remember"
                       type="checkbox"
                       class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span
                        class="text-sm text-gray-600 ms-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   href="{{ route('password.request') }}"
                   wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>