<?php

namespace App\Services;

use App\Http\Requests\UpdateDriverRequest;
use App\Models\User\Driver;
use Illuminate\Database\Eloquent\Collection;

interface DriverServiceInterface
{
    public function index(): Collection;

    public function currentShift(): Collection;

    public function available(): Collection;

    public function inDrive(): Collection;

    public function update(UpdateDriverRequest $request, Driver $driver): Driver;

    public function changeActivity(array $request, Driver $driver): Driver;

    public function destroy(Driver $driver): bool;
}
