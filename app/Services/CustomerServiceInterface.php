<?php

namespace App\Services;

use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User\Customer;

interface CustomerServiceInterface
{
    public function index();

    public function update(UpdateCustomerRequest $request, Customer $customer): Customer;

    public function destroy(Customer $customer): bool;
}
