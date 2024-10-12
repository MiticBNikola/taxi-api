<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User\Customer;
use App\Services\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{

    private CustomerServiceInterface $customerService;

    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->customerService->index());
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer->load('numbers', 'rides'));
    }

    public function status(Customer $customer): JsonResponse
    {
        return response()->json($this->customerService->rideStatus($customer));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        return response()->json($this->customerService->update($request, $customer));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        return response()->json($this->customerService->destroy($customer));
    }
}
