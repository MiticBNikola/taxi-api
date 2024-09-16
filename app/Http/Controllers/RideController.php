<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Models\Ride;
use App\Services\RideServiceInterface;
use Illuminate\Http\JsonResponse;

class RideController extends Controller
{

    private RideServiceInterface $rideService;

    public function __construct(RideServiceInterface $rideService)
    {
        $this->rideService = $rideService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->rideService->index());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function makeRequest(StoreRideRequest $request): JsonResponse
    {
        return response()->json($this->rideService->store($request));
    }

    public function acceptRide(AcceptRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->update($request, $ride));
    }

    public function startRide(StartRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->update($request, $ride));
    }

    public function endRide(EndRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->update($request, $ride));
    }

    public function cancelRide(Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->destroy($ride));
    }
}
