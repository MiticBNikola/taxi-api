<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Number extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'numberable_type',
        'numberable_id',
    ];

    public function numberable(): MorphTo
    {
        return $this->morphTo();
    }
}
