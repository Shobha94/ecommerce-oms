<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array {
        return ['mail','database'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Order Placed: #'.$this->order->id)
            ->greeting('Hi '.$notifiable->name)
            ->line('Thanks for your order!')
            ->line('Total: ₹'.$this->order->total_amount)
            ->action('View Order', url('/orders/'.$this->order->id))
            ->line('We will notify you on status updates.');
    }

    public function toArray($notifiable): array {
        return [
            'order_id' => $this->order->id,
            'total'    => $this->order->total_amount,
            'status'   => $this->order->status,
        ];
    }
}
