<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $reminderType;

    public function __construct(Reservation $reservation, $reminderType)
    {
        $this->reservation = $reservation;
        $this->reminderType = $reminderType;  
    }

    public function build()
    {
        $schedule = $this->reservation->schedule;

        return $this->subject('Your Reservation Reminder')
                    ->view('mails.reservation-notice')
                    ->with([
                        'itemName' => $this->reservation->service->service_name ?? $this->reservation->package->name,
                        'user' => $this->reservation->user,
                        'reservation_time' => Carbon::parse($schedule->date . ' ' . $schedule->time_slot)->format('g:i A'),
                        'reminder_type' => $this->reminderType,
                    ]);
    }
}

