<?php

namespace App\Models;

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
}
