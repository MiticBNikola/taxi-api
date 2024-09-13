<?php

namespace App\Models\User;

use App\Models\Steer;
use App\Models\Vehicle\Vehicle;
use App\Traits\HasRides;
use App\Traits\Numberable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Driver extends User
{
    use Numberable, HasRides;

    protected $table = 'drivers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driving_license_category',
        'drivers_license_number',
    ];

    protected function rideForeignKey(): string
    {
        return 'driver_id';
    }

    public function vehicles(): BelongsToMany
    {
        return  $this->belongsToMany(Vehicle::class)
            ->using(Steer::class)
            ->as('steer')
            ->withPivot('date_from', 'date_to')
            ->withTimestamps();
    }
}
