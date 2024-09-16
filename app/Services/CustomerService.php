<?php

namespace App\Services;

use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class CustomerService implements CustomerServiceInterface
{
    public function index(): Collection
    {
        return Customer::all()->load('numbers');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): Customer
    {
        $customer->update($request->all());
        $requestNumbers = $request->get('numbers') ?? [];
        $oldNumbers = $customer->numbers()->pluck('id');
        foreach ($oldNumbers as $oldNumber) {
            if (!in_array($oldNumber, array_column($requestNumbers, 'id'))) {
                $customer->numbers()->findOrFail($oldNumber)->delete();
            }
        }
        foreach ($requestNumbers as $requestNumber) {
            if (Arr::has($requestNumber, 'id')) {
                $oldNumberForUpdate = $customer->numbers()->find($requestNumber['id']);
                if ($oldNumberForUpdate) {
                    $oldNumberForUpdate->update($requestNumber);
                    continue;
                }
                $customer->numbers()->create($requestNumber);
                continue;
            }
            $customer->numbers()->create($requestNumber);
        }
        $customer->refresh();
        $customer->load('numbers');
        return $customer;
    }

    public function destroy(Customer $customer): bool
    {
        return $customer->delete();
    }
}
