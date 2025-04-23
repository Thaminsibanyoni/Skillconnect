<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Implement interface
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderLocationUpdated implements ShouldBroadcast // Implement interface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $providerId;
    public float $latitude;
    public float $longitude;
    public string $providerName; // Added provider name

    /**
     * Create a new event instance.
     */
    public function __construct(int $providerId, float $latitude, float $longitude, string $providerName) // Added name to constructor
    {
        $this->providerId = $providerId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->providerName = $providerName; // Assign name
    }

    /**
     * Get the channels the event should broadcast on.
     * We'll use a public channel for simplicity, anyone can listen.
     * Alternatively, use the presence channel 'providers' if auth/presence data is needed.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcasting on a public channel for map updates
        return [
            new Channel('provider-locations'),
        ];
        // If you wanted to broadcast only to admins on the presence channel:
        // return [new PresenceChannel('providers')]; // But this requires careful auth setup
    }

     /**
      * The event's broadcast name.
      * Defaults to the class name, but can be customized.
      *
      * @return string
      */
    // public function broadcastAs(): string
    // {
    //     return 'provider.location.updated';
    // }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'providerId' => $this->providerId, // Changed from provider_id for consistency
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'providerName' => $this->providerName, // Include name
        ];
    }
}
