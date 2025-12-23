<?php

namespace App\Mail;

use App\Models\ProductEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

    public function __construct(ProductEnquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for your enquiry - ' . tenant('name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enquiry-received',
        );
    }
}
