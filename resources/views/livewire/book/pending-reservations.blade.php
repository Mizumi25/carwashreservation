<?php

use Livewire\Volt\Component;

new class extends Component {
    public $reservationId;

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    protected $listeners = ['reservationStatusUpdated' => 'handleReservationStatusUpdate'];

    public function handleReservationStatusUpdate($reservationId)
    {
        $reservation = Reservation::find($reservationId);

        if ($reservation) {
            $this->reservationId = $reservationId;
            $this->emit('refreshComponent');  
        }
    }
}; ?><div class="bg-white w-[90%] flex justify-center items-center min-h-screen">
<div class="relative isolate overflow-hidden py-16 bg-white px-6 shadow-2xl sm:rounded-3xl sm:px-16 md:px-24">
    <svg viewBox="0 0 1024 1024" class="absolute left-1/2 top-1/2 -z-10 size-[64rem] -translate-y-1/2 [mask-image:radial-gradient(closest-side,white,transparent)] sm:left-full sm:-ml-80 lg:left-1/2 lg:ml-0 lg:-translate-x-1/2 lg:translate-y-0" aria-hidden="true">
        <circle cx="512" cy="512" r="512" fill="url(#green-gradient)" fill-opacity="0.7" />
        <defs>
            <radialGradient id="green-gradient">
                <stop stop-color="#4ADE80" />
                <stop offset="1" stop-color="#22C55E" />
            </radialGradient>
        </defs>
    </svg>
    <div class="mx-auto max-w-md text-center">
        <h2 class="text-balance text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl">
            Reservation Pending
        </h2>
        <p class="mt-6 text-lg/8 text-gray-700">
            Your car wash reservation is currently pending. Please wait for the admin to approve or decline your request. Check your email or notifications for updates.
        </p>
        <div class="mt-10">
            <a href="{{ route('reservations.manage') }}" class="rounded-md bg-gray-900 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">
                Go back
            </a>
        </div>
    </div>
</div>
</div>
