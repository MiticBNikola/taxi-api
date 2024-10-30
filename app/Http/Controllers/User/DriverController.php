<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverPositionInfoRequest;
use App\Http\Requests\IndexDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\User\Driver;
use App\Services\DriverServiceInterface;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    private DriverServiceInterface $driverService;

    public function __construct(DriverServiceInterface $driverService) {
        $this->driverService = $driverService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexDriverRequest $request): JsonResponse
    {
        return response()->json($this->driverService->index($request));
    }

    public function show(Driver $driver): JsonResponse
    {
        return response()->json($driver->load('vehicles', 'numbers'));
    }

    public function storeDriverCurrentLocation(DriverPositionInfoRequest $request, Driver $driver, string $rideId): JsonResponse
    {
        return response()->json($this->driverService->storeDriverPosition($request, $driver, $rideId));
    }

    public function currentShift(): JsonResponse
    {
        return response()->json($this->driverService->currentShift());
    }

    public function available(): JsonResponse
    {
        return response()->json($this->driverService->available());
    }

    public function inDrive(): JsonResponse
    {
        return response()->json($this->driverService->inDrive());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, Driver $driver): JsonResponse
    {
        return response()->json($this->driverService->update($request, $driver));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): JsonResponse
    {
        return response()->json($this->driverService->destroy($driver));
    }
}
