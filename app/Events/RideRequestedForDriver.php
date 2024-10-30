<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideRequestedForDriver implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ride $ride;
    public int $driverId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $driverId, Ride $ride)
    {
        $this->ride = $ride;
        $this->driverId = $driverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('drivers.' . $this->driverId);
    }

    public function broadcastAs(): string
    {
        return 'ride-requested';
    }

    public function broadcastWith(): array
    {
        return ['ride' => $this->ride];
    }
}
