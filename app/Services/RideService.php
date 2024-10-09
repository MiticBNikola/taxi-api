<?php

namespace App\Services;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\IndexRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Models\Ride;
use Illuminate\Database\Eloquent\Collection;
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
