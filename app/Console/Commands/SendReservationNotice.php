<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationNoticeMail;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendReservationNotice extends Command
{
    protected $signature = 'reservations:send-reservation-notice';
    protected $description = 'Send notice 30 minutes and 10 minutes before a reservation starts.';

    public function handle()
    {
        $reservations = Reservation::where('status', 'ongoing')
            ->whereHas('schedule', function ($query) {
                $query->whereNotNull('date')->whereNotNull('time_slot');
            })
            ->get();
    
        if ($reservations->isEmpty()) {
            Log::info("No upcoming reservations found to notify.");
        } else {
            foreach ($reservations as $reservation) {
                $schedule = $reservation->schedule;
              
                $reservationDateTime = Carbon::parse($schedule->date . ' ' . $schedule->time_slot);
                
                broadcast(new \App\Events\ReservationUpcoming($reservation));

                
                Notification::make()
                    ->title('Almost Time for Car Wash')
                    ->success()
                    ->body(
                        'Name: ' . $reservation->user->name .
                        '. Reservation ID: ' . $reservation->id .
                        '. Reserved Item: ' . ($reservation->service->service_name ?? $reservation->package->name) .
                        '. Reservation time is almost there, please head to the said site!'
                    )
                    ->sendToDatabase($reservation->user);
                
                Mail::to($reservation->user->email)->send(new ReservationNoticeMail($reservation, 'Immediate reservation reminder'));
                
                Log::info("Sent reservation notice for reservation {$reservation->id}.");
            }
        }
    }
}



