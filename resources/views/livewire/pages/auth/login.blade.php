<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Validation\ValidationException; 
use Masmerise\Toaster\Toaster;
//use App\Events\UserActivityUpdated;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */

    public function login(): void
    {
        $this->validate([
            'form.email' => 'required|string',
            'form.password' => 'required|string',
        ]);

        $credentials = [
            filter_var($this->form->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number' => $this->form->email,
            'password' => $this->form->password,
        ];

        if (!auth()->attempt($credentials, $this->form->remember)) {
            throw ValidationException::withMessages([
                'form.email' => [__('These credentials do not match our records.')],
            ]);
        }

        auth()->user()->update(['is_active' => true]);
        Toaster::success('Welcome to Car Wash Reservation');

         // Trigger the event to broadcast the updated status
       // event(new UserActivityUpdated(auth()->user()->id, true));

        Session::regenerate();

        $this->redirectIntended(default: route('reservation.new', absolute: false), navigate: true);
    }

}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email or Phone Number')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="text" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>


        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col items-center justify-end mt-4 gap-7">
        <x-primary-button 
            class="ms-3 bg-[#1e77fc] w-full h-[9%] text-white flex justify-center"
            wire:loading.attr="disabled"
            wire:loading.class="bg-gray-400"
            wire:target="login"
            >
            <span wire:loading wire:target="login">Processing...</span>
            <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
        </x-primary-button>

            @if (Route::has('register'))
                <a class="underline text-sm text-blue-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}" wire:navigate>
                    {{ __('register') }}
                </a>
            @endif

           
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
            
        </div>
    </form>
</div>
