<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderConfirmation extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('storefront.checkout.success', $this->order->order_number);
        $currency = tenant('data')['currency_symbol'] ?? '₦';

        return (new MailMessage)
            ->subject('Order Confirmed - #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for shopping with us! Your order has been placed successfully.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Amount: ' . $currency . number_format($this->order->total, 2))
            ->action('View Order Status', $url)
            ->line('We will notify you when your items are shipped.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'amount' => $this->order->total,
        ];
    }
}

