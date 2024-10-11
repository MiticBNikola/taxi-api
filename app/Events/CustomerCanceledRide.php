<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCanceledRide implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ride $ride;

    /**
     * Create a new event instance.
     */
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        if ($this->ride['driver_id']) {
            return new Channel('drivers.' . $this->ride['driver_id']);
        }
        return new Channel('drivers');
    }

    public function broadcastAs(): string
    {
        return 'ride-canceled';
    }

    public function broadcastWith(): array
    {
        return ['ride' => $this->ride];
    }
}
