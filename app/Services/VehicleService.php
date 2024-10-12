<?php

namespace App\Services;

use App\Models\Vehicle\Vehicle;

class VehicleService implements VehicleServiceInterface
{

    public function available(): \Illuminate\Support\Collection
    {
        $allVehicles = Vehicle::all();
        $availableVehicles = collect();
        foreach ($allVehicles as $vehicle) {
            if ($vehicle->drivers()->wherePivotNull('date_to')->doesntExist()) {
                $availableVehicles->push($vehicle->unsetRelation('drivers'));
            }
        }
        return $availableVehicles;
    }
}
