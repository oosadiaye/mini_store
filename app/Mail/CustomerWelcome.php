<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $tenant;
    public $storefrontUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $tenant)
    {
        $this->customer = $customer;
        $this->tenant = $tenant;
        $this->storefrontUrl = config('app.url') . '/' . $tenant->slug;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->tenant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer.welcome',
            with: [
                'customerName' => $this->customer->name,
                'storeName' => $this->tenant->name,
                'storefrontUrl' => $this->storefrontUrl,
            ],
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
