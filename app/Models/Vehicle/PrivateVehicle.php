<?php

namespace App\Models\Vehicle;

use App\Traits\Vehicable;

class PrivateVehicle extends Vehicle
{
    use Vehicable;

    protected $fillable = [
        'color',
    ];
}
