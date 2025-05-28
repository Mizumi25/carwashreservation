<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Continue Carwash Reservation') }}
        </h2>

        <p class="text-gray-700">Reservation ID: {{ $reservation->id }} - Service: {{ $service_name }}</p>
    </x-slot>

    <div class="py-2" x-data="reservationHandler" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <template x-if="reservation.status === 'pending'">
                <livewire:book.pending-reservations :reservationId="$reservation->id" />
            </template>
            <template x-if="reservation.status === 'approve'">
                <livewire:book.payment-reservations :reservationId="$reservation->id" />
            </template>
            <template x-if="reservation.status === 'ongoing'">
                <livewire:book.confirm-reservations :reservationId="$reservation->id" />
            </template>
            <template x-if="reservation.status === 'in_progress'">
                <livewire:book.progress-reservations :reservationId="$reservation->id" />
            </template>
            <template x-if="reservation.status === 'done'">
                <livewire:book.done-reservations :reservationId="$reservation->id" />
            </template>
            <template x-if="!['pending', 'approve', 'ongoing', 'in_progress', 'done'].includes(reservation.status)">
                <p>No actions available for this reservation status.</p>
            </template>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reservationHandler', () => ({
            reservation: {
                id: {{ $reservation->id }},
                service_name: '{{ $service_name }}',
                status: '{{ $reservation->status }}',
            },

            init() {
                this.listenForStatusChanges();
            },

            listenForStatusChanges() {
                Echo.channel('reservation-status')
                    .listen('ReservationStatusUpdated', (event) => {
                        if (event.id === this.reservation.id) {
                            this.reservation.status = event.status;
                        }
                    });
            },
        }));
    });
</script>
