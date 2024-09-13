<?php

namespace App\Models\User;

use App\Models\Ride;
use App\Traits\HasRides;
use App\Traits\Numberable;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }
}
