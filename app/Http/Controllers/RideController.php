<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptRideRequest;
use App\Http\Requests\EndRideRequest;
use App\Http\Requests\IndexRideRequest;
use App\Http\Requests\StartRideRequest;
use App\Http\Requests\StoreRideRequest;
use App\Http\Requests\UpdateEndOfRideRequest;
use App\Models\Ride;
use App\Services\RideServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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
    public function index(IndexRideRequest $request): JsonResponse
    {
        return response()->json($this->rideService->index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function makeRequest(StoreRideRequest $request): JsonResponse
    {
        return response()->json($this->rideService->store($request));
    }

    public function customerUpdateEnd(UpdateEndOfRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->customerUpdateEnd($request, $ride));
    }

    public function updateEnd(UpdateEndOfRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->updateEnd($request, $ride));
    }

    public function acceptRide(AcceptRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->acceptRide($request, $ride));
    }

    public function startRide(StartRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->startRide($request, $ride));
    }

    public function endRide(EndRideRequest $request, Ride $ride): JsonResponse
    {
        return response()->json($this->rideService->endRide($request, $ride));
    }

    /**
     * @throws ValidationException
     */
    public function cancelRide(Ride $ride): JsonResponse
    {
        if ($ride->start_time) {
            throw ValidationException::withMessages([
                'error' => 'Ride in progress.'
            ]);
        }
        if ($ride->end_time) {
            throw ValidationException::withMessages([
                'error' => 'Ride ended.'
            ]);
        }
        return response()->json($this->rideService->cancelRide($ride));
    }
}
