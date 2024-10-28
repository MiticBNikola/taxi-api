<?php

namespace App\Services;

use App\Http\Requests\IndexDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\User\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DriverServiceInterface
{
    public function index(IndexDriverRequest $request): LengthAwarePaginator;

    public function currentShift(): Collection;

    public function available(): Collection;

    public function inDrive(): Collection;

    public function update(UpdateDriverRequest $request, Driver $driver): Driver;

    public function changeActivity(array $request, Driver $driver): Driver;

    public function destroy(Driver $driver): bool;
}
