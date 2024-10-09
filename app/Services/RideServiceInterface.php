<?php

namespace App\Services;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\IndexRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Models\Ride;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RideServiceInterface
{
    public function index(IndexRideRequest $request): LengthAwarePaginator;

    public function store(StoreRideRequest $request): Ride;

    public function update(AcceptRideRequest|StartRideRequest|EndRideRequest $request, Ride $ride): Ride;

    public function destroy(Ride $ride): bool;
}
