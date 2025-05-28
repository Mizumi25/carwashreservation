<?php

use App\Livewire\Actions\Logout;
use App\Models\Reservation;
use Livewire\Volt\Component;

new class extends Component
{

    public $latestReservations;

    public function mount()
    {
        $this->latestReservations = Reservation::with(['service', 'package', 'vehicle.vehicleType'])
            ->whereNotIn('status', ['decline', 'cancelled'])
            ->orderBy('reservation_date', 'desc')
            ->take(6)
            ->get();
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>


<nav x-data="{ open: true }" :class="open ? 'w-[13rem]' : 'w-[8rem]'" class="flex z-20 py-6 px-6 bg-transparent h-screen">
    <!-- Primary Navigation Menu -->
    <div :class="open ? 'w-[10rem]' : 'w-16'" class="fixed transition-all duration-300 h-[90%] bg-transparent text-gray-800 rounded-[30px] top-[50%] transform translate-y-[-50%]">
        <div class="flex flex-col justify-between h-full">
            <!-- Toggle Button -->
            <button 
                @click="open = !open; gsap.to($el, { duration: 0.3, width: open ? '13rem' : '4rem' })"
                class="absolute top-4 left-[50%] bg-transparent rounded p-1 focus:outline-none text-white">
                <i class="fa-solid" :class="open ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
            </button>

            <!-- Navigation Links -->
            <div class="flex flex-col space-y-4 px-1 mt-[6rem]" :class="open ? 'items-start' : 'items-center'">
                <a href="{{ route('reservation.new') }}" class="flex text-white items-center space-x-2 p-2 bg-green-500 rounded-2xl">
                    <i class="fa-solid fa-plus"></i>
                    <span x-show="open" class="text-sm text-nowrap">{{ __('Add Reservation') }}</span>
                </a>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'text-green-500' : '' }}">
                    <i class="fa-solid fa-gauge text-lg {{ request()->routeIs('dashboard') ? 'text-green-500' : 'text-white' }}"></i>
                    <span x-show="open" class="text-sm text-white">{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ route('reservations.manage') }}" class="flex items-center space-x-2 {{ request()->routeIs('reservations.manage') ? 'text-green-500' : '' }}">
                    <i class="fa-solid fa-book-open-reader text-lg {{ request()->routeIs('reservations.manage') ? 'text-green-500' : 'text-white' }}"></i>
                    <span x-show="open" class="text-sm text-nowrap text-white">{{ __('My Reservations') }}</span>
                </a>
                <a href="{{ route('vehicles') }}" class="flex items-center space-x-2 {{ request()->routeIs('vehicles') ? 'text-green-500' : '' }}">
                    <i class="fa-solid fa-car text-lg {{ request()->routeIs('vehicles') ? 'text-green-500' : 'text-white' }}"></i>
                    <span x-show="open" class="text-sm text-white">{{ __('My Vehicles') }}</span>
                </a>
            </div>

            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <div x-show="open" @click="open = ! open">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <div class="flex flex-col items-start justify-start">
                            {{ __('Recent') }}
                        </div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </div>

                <div x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 mt-2 w-48 rounded-md ltr:origin-top-right rtl:origin-top-left end-0"
                    style="display: none;" @click="open = false">
                    <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 text-white">
                        @foreach($latestReservations as $reservation)
                            <a href="#" class="block px-4 py-2 text-sm hover:underline">
                                @if($reservation->service)
                                    <p>{{ $reservation->service->service_name }} - {{ $reservation->vehicle->vehicleType->name }}</p>
                                @elseif($reservation->package)
                                    <p>{{ $reservation->package->name }} - {{ $reservation->vehicle->vehicleType->name }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>


            

            <!-- Footer Section -->
            <div class="px-4 mb-8 text-white">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/admin') }}" class="block text-center py-2 bg-gray-600 rounded text-sm">
                          <i class="fas fa-cogs"></i>
                           <span x-show="open" class="text-sm">{{ __('Admin') }}</span>
                        </a>
                    @endif
                @endauth
                <a wire:click="logout" class="block mt-4 text-center py-2 bg-blue-500 rounded text-sm cursor-pointer" 
                   wire:loading.attr="disabled"
                   wire:loading.class="bg-gray-400"
                   wire:target="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span x-show="open" wire:loading wire:target="logout">Signing out...</span>
                    <span x-show="open" wire:loading.remove wire:target="logout">{{ __('Sign Out') }}</span>
                </a>
            </div>
        </div>
    </div>
</nav>

