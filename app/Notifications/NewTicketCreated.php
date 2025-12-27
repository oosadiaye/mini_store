<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketCreated extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Support Ticket: ' . $this->ticket->category->name . ' - ' . $this->ticket->subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new support ticket has been assigned to you.')
            ->line('**Category:** ' . $this->ticket->category->name)
            ->line('**Subject:** ' . $this->ticket->subject)
            ->line('**Tenant:** ' . $this->ticket->tenant->name)
            ->line('**Priority:** ' . ucfirst($this->ticket->priority))
            ->action('View Ticket', url('/superadmin/tickets/' . $this->ticket->id))
            ->line('Please respond as soon as possible.');
    }
}
