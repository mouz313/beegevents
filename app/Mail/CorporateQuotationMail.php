<?php

namespace App\Mail;

use App\Models\CorporateQuotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorporateQuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public CorporateQuotation $quotation;

    public function __construct(CorporateQuotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Corporate Quotation '.$this->quotation->quote_no.' from '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.corporate-quotation-notification');
    }
}
