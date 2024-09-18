<?php

namespace App\Services;

interface VehicleServiceInterface
{
    public function available(): \Illuminate\Support\Collection;
}
