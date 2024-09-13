<?php

namespace App\Models;

use Database\Factories\SteerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Steer extends Pivot
{
    use HasFactory;

    protected $table = 'steers';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SteerFactory::new();
    }
}
