<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="relative {{ $mode === 'dark' ? 'bg-[#262837] text-white' : 'bg-transparent text-black ' }}">
    
<livewire:digitalclock />
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex mt-2">
                <!-- Logo Section -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}" wire:navigate>
                        <span>
                            <!-- For mobile view -->
                            <h1 class="text-2xl text-black lg:hidden md:hidden block">
                                Hello, {{ auth()->user()->name }} 👋
                            </h1>
                            <p class="text-sm bg-gradient-to-r from-green-500 to-white bg-clip-text text-transparent lg:hidden md:hidden block">
                                Let's check your reservations today!
                            </p>
                        </span>
                    </a>
                </div>

                <!-- For desktop view -->
                <div class="sm:hidden lg:flex block space-x-8 sm:-my-px sm:ms-10 justify-center items-center">
                    <a href="{{ route('welcome') }}">
                        <div class="text-2xl text-black">
                            Hello, {{ auth()->user()->name }} 👋
                        </div>
                        <p class="text-sm bg-gradient-to-r from-green-700 to-green-300 bg-clip-text text-transparent">
                            Let's check your reservations today!
                        </p>
                    </a>
                </div>
            </div>


            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
              <div class="text-black flex flex-row justify-between items-center">
                <livewire:chatmodal />
                @livewire('database-notifications')
              </div>

              <span class="text-black">|</span>
              <a href="{{ url('/profile') }}">
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="h-[2rem] w-[2rem] cursor-pointer object-cover rounded-full backdrop-filter backdrop-grayscale backdrop-blur-md backdrop-contrast-200">
              </a>
              
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-start justify-start">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                                <div class="text-[12px] text-gray-400"  x-data="{{ json_encode(['email' => auth()->user()->email]) }}" x-text="email"></div>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        
                    </x-slot>
                    <x-slot name="content">
                     <livewire:themeswitcher />
                      
                        <x-dropdown-link class="ml-[20px]" :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reservation.new')" :active="request()->routeIs('reservation.new')" wire:navigate>
                {{ __('New Reservation') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('reservations.manage')" :active="request()->routeIs('reservations.manage')" wire:navigate>
                {{ __('My Reservations') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('vehicles')" :active="request()->routeIs('vehicles')" wire:navigate>
                {{ __('My Vehicles') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" href="{{ url('/logout') }}" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
