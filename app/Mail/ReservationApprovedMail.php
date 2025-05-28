<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationApprovedMail extends Mailable
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
            subject: 'Reservation Approved Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
{
    $url = route('reservation.continue', [
        'id' => $this->reservation->id,
        'service_name' => $this->reservation->service->service_name ?? $this->reservation->package->name,
    ]);

    return new Content(
        view: 'mails.reservation-approved',
        with: [
            'url' => $url,
            'itemName' => $this->reservation->service->service_name ?? $this->reservation->package->name,
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