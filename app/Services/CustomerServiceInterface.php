<?php

namespace App\Services;

use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Collection;

interface CustomerServiceInterface
{
    public function index(): Collection;

    public function rideStatus(Customer $customer): \Illuminate\Support\Collection;

    public function update(UpdateCustomerRequest $request, Customer $customer): Customer;

    public function destroy(Customer $customer): bool;
}
