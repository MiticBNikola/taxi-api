<?php

namespace App\Services;

use App\Events\CustomerCanceledRide;
use App\Events\CustomerChangedEnd;
use App\Events\DriverAcceptedRide;
use App\Events\DriverChangedEnd;
use App\Events\DriverEndedRide;
use App\Events\DriverPositionEvent;
use App\Events\DriverStartedRide;
use App\Events\RideRequested;
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

class RideService implements RideServiceInterface
{
    public function index(IndexRideRequest $request): LengthAwarePaginator
    {
        $query = Ride::query();
        $filter = $request->all();
        if ($filter) {
            if (isset($filter['customer_id'])) {
                $query->where('customer_id', '=', $filter['customer_id']);
            }
            if (isset($filter['driver_id'])) {
                $query->where('driver_id', '=', $filter['driver_id']);
            }
        }
//        $query->where('end_time', '!=', null);
        return $query->with('customer', 'driver')->paginate($filter['per_page'] ?? 10, ['*'], 'page', $filter['page'] ?? 1);
    }

    public function status(CheckRideStatusRequest $request): Ride|null
    {
        $rideID = $request->get('ride_id');
        $userID = $request->get('user_id');
        if (!$rideID && !$userID) {
            return null;
        }
        $query = Ride::query();
        if ($rideID) {
            $query->where('id', '=', $rideID);
        }
        if ($userID) {
            $query->where('customer_id', '=', $userID);
        }
        $query->where('end_time', '=', null);
        return $query->get()->first() ?? null;
    }

    public function store(StoreRideRequest $request): Ride
    {
        $ride = Ride::create($request->all());
        $ride->refresh();
        RideRequested::dispatch($ride);
        return $ride;
    }

    public function customerUpdateEnd(UpdateEndOfRideRequest $request, Ride $ride): Ride
    {
        $updatedRide = $this->update($request, $ride);
        CustomerChangedEnd::dispatch($updatedRide);
        return $updatedRide;
    }

    public function updateEnd(UpdateEndOfRideRequest $request, Ride $ride): Ride
    {
        $updatedRide = $this->update($request, $ride);
        DriverChangedEnd::dispatch($updatedRide);
        return $updatedRide;
    }

    public function acceptRide(AcceptRideRequest $request, Ride $ride): Ride
    {
        $updatedRide = $this->update($request, $ride);
        DriverAcceptedRide::dispatch($updatedRide);
        return $updatedRide;
    }

    public function dispatchDriverPosition(DriverPositionInfoRequest $request, string $driverId, Ride $ride): bool
    {
        DriverPositionEvent::dispatch($request->all(), $driverId, $ride);
        return true;
    }

    public function startRide(StartRideRequest $request, Ride $ride): Ride
    {
        $updatedRide = $this->update($request, $ride);
        DriverStartedRide::dispatch($updatedRide);
        return $updatedRide;
    }

    public function endRide(EndRideRequest $request, Ride $ride): Ride
    {
        $updatedRide = $this->update($request, $ride);
        DriverEndedRide::dispatch($updatedRide);
        return $updatedRide;
    }

    public function update(AcceptRideRequest|UpdateEndOfRideRequest|StartRideRequest|EndRideRequest $request, Ride $ride): Ride
    {
        $ride->update($request->all());
        return $ride->refresh();
    }

    public function cancelRide(Ride $ride): bool
    {
        CustomerCanceledRide::dispatch($ride);
        return $this->destroy($ride);
    }

    public function destroy(Ride $ride): bool
    {
        return $ride->delete();
    }
}
