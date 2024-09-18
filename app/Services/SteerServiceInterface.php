<?php

namespace App\Services;

use App\Http\Requests\AssignDriverToVehicleRequest;
use App\Http\Requests\FinalizeSteeringRequest;
use App\Models\Steer;
use App\Models\User\Driver;

interface SteerServiceInterface
{
    public function assign(AssignDriverToVehicleRequest $request): Driver;
    public function release(FinalizeSteeringRequest $request, Steer $steer): Driver;
}
