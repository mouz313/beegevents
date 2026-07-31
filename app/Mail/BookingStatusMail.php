<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $subject;
    public string $greeting;
    public string $body;
    public ?string $actionUrl;
    public string $actionText;

    public function __construct(Booking $booking, string $subject, string $greeting, string $body, ?string $actionUrl = null, string $actionText = 'View Booking')
    {
        $this->booking = $booking;
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->body = $body;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-notification');
    }
}
