<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCanceledRide implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ride_id;
    public int|null $driver_id;

    /**
     * Create a new event instance.
     */
    public function __construct(int $ride_id, int|null $driver_id)
    {
        $this->ride_id = $ride_id;
        $this->driver_id = $driver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        if ($this->driver_id) {
            return new Channel('drivers.' . $this->driver_id);
        }
        return new Channel('drivers');
    }

    public function broadcastAs(): string
    {
        return 'ride-canceled';
    }

    public function broadcastWith(): array
    {
        return ['ride_id' => $this->ride_id];
    }
}
