<?php

namespace App\Models;

use App\Models\User\Customer;
use App\Models\User\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_time',
        'start_location',
        'start_lat',
        'start_lng',
        'end_location',
        'end_lat',
        'end_lng',
        'start_time',
        'end_time',
        'customer_id',
        'driver_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_time' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
