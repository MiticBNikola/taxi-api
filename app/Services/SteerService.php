<?php

namespace App\Services;

use App\Http\Requests\AssignDriverToVehicleRequest;
use App\Http\Requests\FinalizeSteeringRequest;
use App\Models\Steer;
use App\Models\User\Driver;
use App\Models\Vehicle\Vehicle;
use Illuminate\Validation\ValidationException;

class SteerService implements SteerServiceInterface
{

    /**
     * @throws ValidationException
     */
    public function assign(AssignDriverToVehicleRequest $request): Driver
    {
        $driver = Driver::findOrFail($request['driver_id']);
        if ($driver->has_vehicle) {
            throw ValidationException::withMessages([
                'driver_id' => 'The Driver already has an assigned Vehicle.'
            ]);
        }
        $vehicle = Vehicle::findOrFail($request['vehicle_id']);
        if ($vehicle->has_driver) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'The Vehicle already has its Driver.'
            ]);
        }

        $driver->vehicles()->attach($vehicle->id,
        [
            'date_from' => $request['date_from'],
        ]);

        $driver->refresh();
        $driver->load('vehicles');
        return $driver;
    }

    public function release(FinalizeSteeringRequest $request, Steer $steer): Driver
    {
        $steer->update($request->all());
        $driver = Driver::findOrFail($steer->driver_id);
        $driver->load('vehicles');
        return $driver;
    }
}
