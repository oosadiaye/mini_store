<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $tenant;
    public $adminUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $tenant)
    {
        $this->order = $order;
        $this->tenant = $tenant;
        $this->adminUrl = config('app.url') . '/' . $tenant->slug . '/admin';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Received - ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.admin-notification',
            with: [
                'order' => $this->order,
                'storeName' => $this->tenant->name,
                'adminUrl' => $this->adminUrl,
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
