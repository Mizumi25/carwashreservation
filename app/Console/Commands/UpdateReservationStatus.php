<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Mail\ReservationNotAppeared;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class UpdateReservationStatus extends Command
{
    protected $signature = 'app:update-reservation-status';
    protected $description = 'Automatically updates the status of reservations that are still ongoing after an hour to not_appeared';

    public function handle()
    {
        $reservations = Reservation::where('status', 'ongoing')
            ->where('reservation_date', '<', Carbon::now()->subHour())
            ->get();
            
        if ($reservations->isEmpty()) {
            Log::info("No ongoing reservations found to update.");
        } else {
            foreach ($reservations as $reservation) {
                Mail::to($reservation->user->email)->send(new ReservationNotAppeared($reservation));
                broadcast(new \App\Events\ReservationStatusUpdated($reservation));

                    Notification::make()
                      ->title('Reservation was considered Unattended')
                      ->warning()
                      ->body(
                            'Name: ' . $reservation->user->name . 
                            '. Reservation ID: ' . $reservation->id . 
                            '. Reserved Item: ' . ($reservation->service->service_name ?? $reservation->package->name) . 
                            '. Reservation Missed!!! no further refund.!'
                       )
                    ->sendToDatabase($reservation->user);
                $reservation->update(['status' => 'not_appeared']);
                Log::info("Updated reservation {$reservation->id} to not_appeared.");
            }
        }
    }
}
