<?php

namespace App\Traits;

use App\Models\Number;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Numberable
{
    public function numbers() : MorphMany
    {
        return $this->morphMany(Number::class, 'numberable');
    }
}
