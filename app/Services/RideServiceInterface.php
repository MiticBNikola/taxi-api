<?php

namespace App\Services;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\CheckRideStatusRequest;
use App\Http\Requests\DriverPositionInfoRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\IndexRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Http\Requests\UpdateEndOfRideRequest;
use App\Models\Ride;
use Illuminate\Pagination\LengthAwarePaginator;

interface RideServiceInterface
{
    public function index(IndexRideRequest $request): LengthAwarePaginator;

    public function status(CheckRideStatusRequest $request): Ride|null;

    public function store(StoreRideRequest $request): Ride;

    public function customerUpdateEnd(UpdateEndOfRideRequest $request, Ride $ride): Ride;

    public function updateEnd(UpdateEndOfRideRequest $request, Ride $ride): Ride;

    public function acceptRide(AcceptRideRequest $request, Ride $ride): Ride;

    public function dispatchDriverPosition(DriverPositionInfoRequest $request, string $driverId, Ride $ride): bool;

    public function startRide(StartRideRequest $request, Ride $ride): Ride;

    public function endRide(EndRideRequest $request, Ride $ride): Ride;

    public function update(AcceptRideRequest|UpdateEndOfRideRequest|StartRideRequest|EndRideRequest $request, Ride $ride): Ride;

    public function destroy(Ride $ride): bool;
}
