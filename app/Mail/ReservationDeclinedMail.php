<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reservation Declined Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Dynamically determine the relevant name (service or package)
        $name = $this->reservation->service->service_name ?? $this->reservation->package->name;

        return new Content(
            view: 'mails.reservation-declined', 
            with: [
                'declinedMessage' => $this->reservation->declined_message,
                'name' => $name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
