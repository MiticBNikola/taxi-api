<?php

namespace App\Services;

use App\Http\Requests\UpdateDriverRequest;
use App\Models\User\Driver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class DriverService implements DriverServiceInterface
{
    public function index(): Collection
    {
        return Driver::all()->load('numbers');
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
