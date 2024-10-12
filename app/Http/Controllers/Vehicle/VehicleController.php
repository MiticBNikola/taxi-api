<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\PrivateVehicle;
use App\Services\VehicleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    private VehicleServiceInterface $vehicleService;

    public function __construct(VehicleServiceInterface $vehicleService) {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function available(): JsonResponse
    {
        return response()->json($this->vehicleService->available());
    }

    /**
     * Display the specified resource.
     */
    public function show(PrivateVehicle $privateVehicle)
    {
        //
    }

    public function update(Request $request, PrivateVehicle $privateVehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrivateVehicle $privateVehicle)
    {
        //
    }
}
