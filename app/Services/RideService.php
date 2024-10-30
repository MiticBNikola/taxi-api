<?php

namespace App\Services;

use App\Events\CustomerCanceledRide;
use App\Events\CustomerChangedEnd;
use App\Events\DriverAcceptedRide;
use App\Events\DriverChangedEnd;
use App\Events\DriverEndedRide;
use App\Events\DriverPositionEvent;
use App\Events\DriverStartedRide;
use App\Events\RequestDriversLocation;
use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\CheckRideStatusRequest;
use App\Http\Requests\DriverPositionInfoRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\IndexRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Http\Requests\UpdateEndOfRideRequest;
use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;

class RideService implements RideServiceInterface
{
    public function index(IndexRideRequest $request): LengthAwarePaginator
    {
        $query = Ride::query();
        $filter = $request->all();
        if ($filter) {
            if (isset($filter['customer_id']) || isset($filter['driver_id'])) {
                if (isset($filter['customer_id'])) {
                    $query->where('customer_id', '=', $filter['customer_id']);
                }
                if (isset($filter['driver_id'])) {
                    $query->where('driver_id', '=', $filter['driver_id']);
                }
            } else {
                if (!$filter['requested']) {
                    $query->where('driver_id', '!=', null);
                }
                if (!$filter['in_progress']) {
                    $query->where(function ($querySearch) use ($filter) {
                        $querySearch->where('end_time', '!=', null);
                        if ($filter['requested']) {
                            $querySearch->orWhere('driver_id', '=', null);
                        }
                    });
                }
                if (isset($filter['search']) && $filter['search']) {
                    $query->where(function ($querySearch) use ($filter) {
                        $querySearch->where('start_location', 'LIKE', '%' . $filter['search'] . '%');
                        $querySearch->orWhere('end_location', 'LIKE', '%' . $filter['search'] . '%');
                    });
                }
            }
        }
        return $query->with('customer', 'driver')->paginate($filter['per_page'] ?? 10, ['*'], 'page', $filter['page'] ?? 1);
    }

    public function requestedRides(int $driver_id): Collection
    {
        $query = Ride::query();
        $query->where('driver_id', '=', null);
        $query->where('start_time', '=', null);
        $query->where('end_time', '=', null);
        $allRides = $query->get();
        foreach ($allRides as $index => $ride) {
            if ($ride->created_at > Carbon::now()->subSeconds(65)) {
                $key = "ride:$ride->id:driver";
                if (!Redis::exists($key) ||
                    (Redis::exists($key) && Redis::get($key) !== 'all' && Redis::get($key) !== (string)$driver_id)
                ) {
                    $allRides->forget($index);
                }
            }
        }
//        $query->where('created_at', '<', Carbon::now()->subSeconds(90));

        return $allRides;
    }

    public function status(CheckRideStatusRequest $request): Ride|null
    {
        $rideID = $request->get('ride_id');
        $customerID = $request->get('customer_id');
        $driverID = $request->get('driver_id');
        if (!$rideID && !$customerID) {
            return null;
        }
        $query = Ride::query();
        if ($rideID) {
            $query->where('id', '=', $rideID);
        }
        if ($customerID) {
            $query->where('customer_id', '=', $customerID);
        } else if ($driverID) {
            $query->where('driver_id', '=', $driverID);
        }
        $query->where('end_time', '=', null);
        return $query->get()->first() ?? null;
    }

    public function bestMonthDrivers(): Collection
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $maxTimesDriver = Ride::selectRaw('count(driver_id) as times')
            ->where('driver_id', '!=', null)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('driver_id')
            ->orderBy('times', 'desc')
            ->pluck('times')
            ->first() ?? 0;
        return Ride::select('driver_id')
            ->selectRaw('count(driver_id) as times')
            ->where('driver_id', '!=', null)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('driver_id')
            ->having('times', '=', $maxTimesDriver)
            ->with('driver')
            ->get();
    }

    public function store(StoreRideRequest $request): Ride
    {
        $ride = Ride::create($request->all());
        $ride->refresh();
        RequestDriversLocation::dispatch($ride);
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
        $redisRideKeyPattern = "ride:$ride->id:driver*";
        $redisRideKeys = Redis::keys($redisRideKeyPattern);
        if (!empty($redisRideKeys)) {
            Redis::del($redisRideKeys);
        }
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
        $ride_id = $ride['id'];
        $driver_id = $ride['driver_id'];
        $redisRideKeyPattern = "ride:$ride_id:driver*";
        $redisRideKeys = Redis::keys($redisRideKeyPattern);
        if (!empty($redisRideKeys)) {
            Redis::del($redisRideKeys);
        }
        CustomerCanceledRide::dispatch($ride_id, $driver_id);
        return $this->destroy($ride);
    }

    public function destroy(Ride $ride): bool
    {
        return $ride->delete();
    }
}
