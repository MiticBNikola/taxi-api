<?php

namespace App\Services;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Models\Ride;

interface RideServiceInterface
{
    public function index();

    public function store(StoreRideRequest $request): Ride;

    public function update(AcceptRideRequest|StartRideRequest|EndRideRequest $request, Ride $ride): Ride;

    public function destroy(Ride $ride): bool;
}
