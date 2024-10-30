<?php

namespace App\Jobs;

use App\Events\RideRequested;
use App\Models\Ride;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class CheckIfDriverHasAccepted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ride $ride;

    /**
     * Create a new job instance.
     */
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->ride->refresh();
        if (!$this->ride->driver_id) {
            $redisRideKey = "ride:$this->ride->id:driver";
            Redis::set($redisRideKey, "all");
            RideRequested::dispatch($this->ride);
        }
    }
}
