<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Inquiry $inquiry;
    public string $recipientType;

    public function __construct(Inquiry $inquiry, string $recipientType)
    {
        $this->inquiry = $inquiry;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->recipientType) {
            'vendor' => 'New Inquiry for Your Listing - BeeG Events',
            'admin' => 'New Customer Inquiry - BeeG Events',
            default => 'Your Inquiry Sent - BeeG Events',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry-notification',
        );
    }
}
