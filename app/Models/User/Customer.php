<?php

namespace App\Models\User;

use App\Models\Ride;
use App\Traits\HasRides;
use App\Traits\Numberable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends User
{
    use Numberable, HasRides;

    protected $table = 'customers';

    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    protected function rideForeignKey(): string
    {
        return 'customer_id';
    }
}
