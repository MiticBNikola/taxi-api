<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Manager;
use App\Services\ManagerServiceInterface;
use Illuminate\Http\JsonResponse;

class ManagerController extends Controller
{

    private ManagerServiceInterface $managerService;

    public function __construct(ManagerServiceInterface $managerService)
    {
        $this->managerService = $managerService;
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager): JsonResponse
    {
        return response()->json($manager->load('numbers'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manager $manager): JsonResponse
    {
        return response()->json($this->managerService->destroy($manager));
    }
}
