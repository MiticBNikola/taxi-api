<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverPositionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $position_data;
    public string $driver_id;
    public Ride $ride;

    /**
     * Create a new event instance.
     */
    public function __construct(array $position_data, string $driver_id, Ride $ride)
    {
        $this->position_data = $position_data;
        $this->driver_id = $driver_id;
        $this->ride = $ride;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('rides.' . $this->ride['id']);
    }

    public function broadcastAs(): string
    {
        return 'driver-position';
    }

    public function broadcastWith(): array
    {
        return ['driver_location' => $this->position_data, 'driver' => $this->driver_id];
    }
}
