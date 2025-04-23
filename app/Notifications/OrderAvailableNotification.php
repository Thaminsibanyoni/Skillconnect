<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderAvailableNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
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
        // Notify provider via database and broadcast
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

     /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'service_name' => $this->order->service->name ?? 'Unknown Service',
            'address' => $this->order->address,
            'message' => "New service request available near you: {$this->order->service->name} at {$this->order->address}.",
            'action_url' => route('provider.orders.index'), // Link to provider's orders page
        ];
    }

     /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Include enough data for the provider UI to potentially display the order
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'service_name' => $this->order->service->name ?? 'Unknown Service',
            'address' => $this->order->address,
            'message' => "New service request available: {$this->order->service->name}.",
        ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
         return [
            'order_id' => $this->order->id,
            'message' => "New service request available.",
        ];
    }
}
