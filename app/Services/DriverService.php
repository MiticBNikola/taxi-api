<?php

namespace App\Services;

use App\Http\Requests\DriverPositionInfoRequest;
use App\Http\Requests\IndexDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Ride;
use App\Models\User\Driver;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class DriverService implements DriverServiceInterface
{
    public function index(IndexDriverRequest $request): LengthAwarePaginator
    {
        $query = Driver::query();
        $filter = $request->all();
        if ($filter) {
            if ($filter['active']) {
                $query->where('is_active', '=', true);
            }
            if (isset($filter['search']) && $filter['search']) {
                $query->where(function ($querySearch) use ($filter) {
                    $querySearch->where('first_name', 'LIKE', '%' . $filter['search'] . '%');
                    $querySearch->orWhere('last_name', 'LIKE', '%' . $filter['search'] . '%');
                });
            }
        }
        return $query->with('numbers', 'currentVehicles')->paginate($filter['per_page'] ?? 10, ['*'], 'page', $filter['page'] ?? 1);
    }

    public function currentShift(): Collection
    {
        return Driver::where('is_active', true)->get()->load('numbers');
    }

    public function available(): Collection
    {
        return Driver::where('is_active', '=', true)
            ->whereDoesntHave('rides')
            ->orWhereRelation('rides', 'end_time', '!=', null)
            ->get()->load('numbers');
    }

    public function inDrive(): Collection
    {
        return Driver::where('is_active', '=', true)
            ->whereRelation('rides', 'end_time', '=', null)
            ->get()->load('numbers');
    }

    public function storeDriverPosition(DriverPositionInfoRequest $request, Driver $driver, string $rideId): bool
    {
        $ride = Ride::findOrFail($rideId);
        $timeDifference = Carbon::now()->diffInSeconds($ride->created_at);
        // Probably do not need timeDifference here at all
        if (!$ride->driver_id && $timeDifference <= 10) {
            $redisKey = "ride:$rideId:driver-location";
            Redis::sadd($redisKey, json_encode(['lat' => $request->get('lat'), 'lng' => $request->get('lng'), 'driver_id' => $driver->id]));
            return true;
        }
        return false;
    }

    public function update(UpdateDriverRequest $request, Driver $driver): Driver
    {
        $driver->update($request->all());
        $requestNumbers = $request->get('numbers') ?? [];
        $oldNumbers = $driver->numbers()->pluck('id');
        foreach ($oldNumbers as $oldNumber) {
            if (!in_array($oldNumber, array_column($requestNumbers, 'id'))) {
                $driver->numbers()->findOrFail($oldNumber)->delete();
            }
        }
        foreach ($requestNumbers as $requestNumber) {
            if (Arr::has($requestNumber, 'id')) {
                $oldNumberForUpdate = $driver->numbers()->find($requestNumber['id']);
                if ($oldNumberForUpdate) {
                    $oldNumberForUpdate->update($requestNumber);
                    continue;
                }
                $driver->numbers()->create($requestNumber);
                continue;
            }
            $driver->numbers()->create($requestNumber);
        }
        $driver->refresh();
        $driver->load('numbers');
        return $driver;
    }

    public function changeActivity(array $request, Driver $driver): Driver
    {
        $driver->update($request);
        return $driver;
    }

    public function destroy(Driver $driver): bool
    {
        return $driver->delete();
    }
}
