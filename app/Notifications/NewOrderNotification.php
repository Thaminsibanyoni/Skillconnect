<?php

namespace App\Notifications;

use App\Models\Order; // Import Order model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Import ShouldBroadcast
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage; // Import BroadcastMessage
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Implement ShouldBroadcast for real-time notifications
class NewOrderNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public Order $order; // Public property to hold the order

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
        // Send via database and broadcast (Reverb/Pusher)
        // Add 'mail' back if email notifications are also needed
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
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Data to store in the notifications table
        return [
            'order_id' => $this->order->id,
            'service_name' => $this->order->service->name ?? 'Unknown Service',
            'seeker_name' => $this->order->seeker->name ?? 'Unknown Seeker',
            'message' => "You have a new service request for {$this->order->service->name} from {$this->order->seeker->name}.",
            // Add link/action URL if needed
            'action_url' => route('admin.orders.index'), // Example: Link to admin orders for now
        ];
    }

     /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Data to broadcast via WebSocket
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'service_name' => $this->order->service->name ?? 'Unknown Service',
            'seeker_name' => $this->order->seeker->name ?? 'Unknown Seeker',
            'message' => "New service request: {$this->order->service->name} from {$this->order->seeker->name}.",
        ]);
    }

    /**
     * Get the array representation of the notification.
     * Used by default if toDatabase/toBroadcast are not defined.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Fallback array representation
        return [
            'order_id' => $this->order->id,
            'message' => "New service request received.",
        ];
    }
}
