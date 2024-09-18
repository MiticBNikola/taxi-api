<?php

namespace App\Models\User;

use App\Models\Steer;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasRides;
use App\Traits\Numberable;
use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends User
{
    use Numberable, HasRides, SoftDeletes;

    protected $table = 'drivers';

    protected $appends = ['has_vehicle'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeFillable([
            'driving_license_category',
            'driving_license_number',
        ]);
        $this->mergeCasts([
            'is_active' => 'boolean',
        ]);
    }

    protected function rideForeignKey(): string
    {
        return 'driver_id';
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'steers')
            ->using(Steer::class)
            ->as('steer')
            ->withPivot('id', 'date_from', 'date_to')
            ->withTimestamps();
    }

    public function getHasVehicleAttribute(): bool
    {
        return $this->vehicles()->wherePivotNull('date_to')->exists();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return DriverFactory::new();
    }
}
