<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignDriverToVehicleRequest;
use App\Http\Requests\FinalizeSteeringRequest;
use App\Models\Steer;
use App\Services\SteerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SteerController extends Controller
{

    private SteerServiceInterface $steerService;

    public function __construct(SteerServiceInterface $steerService) {
        $this->steerService = $steerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function assignVehicle(AssignDriverToVehicleRequest $request): JsonResponse
    {
        return response()->json($this->steerService->assign($request));
    }

    public function releaseVehicle(FinalizeSteeringRequest $request, Steer $steer): JsonResponse
    {
        return response()->json($this->steerService->release($request, $steer));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Steer $steer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Steer $steer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Steer $steer)
    {
        //
    }
}
