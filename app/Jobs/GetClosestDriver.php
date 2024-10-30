<?php

namespace App\Jobs;

use App\Events\RideRequested;
use App\Events\RideRequestedForDriver;
use App\Models\Ride;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class GetClosestDriver implements ShouldQueue
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
        $rideId = $this->ride->id;
        $customerLocation = [
            'lat' => $this->ride->start_lat,
            'lng' => $this->ride->start_lng
        ];
        $redisRideKey = "ride:$rideId:driver";
        $redisDriversLocationsKey = "ride:$rideId:driver-location";
        $driversLocationData = Redis::smembers($redisDriversLocationsKey);
        if ($driversLocationData && count($driversLocationData) > 0) {
            $closestDriver = ['driver_id' => -1, 'distance' => -1];
            foreach ($driversLocationData as $index => $driver) {
                $driver = json_decode($driver, true);
                $distance = $this->haversineDistance(
                    $customerLocation['lat'], $customerLocation['lng'],
                    $driver['lat'], $driver['lng']
                );
                if ($index === 0 || $distance < $closestDriver['distance']) {
                    $closestDriver = ['driver_id' => $driver['driver_id'], 'distance' => $distance];
                }
            }
            if ($closestDriver['driver_id'] > 0) {
                Redis::set($redisRideKey, $closestDriver['driver_id']);
                RideRequestedForDriver::dispatch($closestDriver['driver_id'], $this->ride);
                return;
            }
        }
        Redis::set($redisRideKey, "all");
        RideRequested::dispatch($this->ride);
    }

    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float|int
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Calculate the distance
        return $earthRadius * $c;
    }
}
