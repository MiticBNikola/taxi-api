<?php

namespace App\Traits;

use App\Models\Ride;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRides
{
    public function rides() : HasMany
    {
        return $this->hasMany(Ride::class, $this->rideForeignKey());
    }

    abstract protected function rideForeignKey() : string;
}