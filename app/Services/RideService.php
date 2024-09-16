<?php

namespace App\Services;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Models\Ride;
use Illuminate\Database\Eloquent\Collection;

class RideService implements RideServiceInterface
{
    public function index(): Collection
    {
        return Ride::all();
    }

    public function store(StoreRideRequest $request): Ride
    {
        return Ride::create($request->all());
    }

    public function update(AcceptRideRequest|StartRideRequest|EndRideRequest $request, Ride $ride): Ride
    {
        $ride->update($request->all());
        return $ride->refresh();
    }

    public function destroy(Ride $ride): bool
    {
        return $ride->delete();
    }
}
