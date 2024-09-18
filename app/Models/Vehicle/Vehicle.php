<?php

namespace App\Models\Vehicle;

use App\Models\Steer;
use App\Models\User\Driver;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'registration_date',
        'brand',
        'model',
        'model_year',
    ];

    protected $table = 'vehicles';

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'steers')
            ->using(Steer::class)
            ->as('steer')
            ->withPivot('date_from', 'date_to')
            ->withTimestamps();
    }

    protected static function newFactory(): Factory
    {
        return VehicleFactory::new();
    }

//    /**
//     * Create a new instance of the given model.
//     *
//     * @param array $attributes
//     * @param bool $exists
//     * @return static
//     */
//    public function newInstance($attributes = [], $exists = false): static
//    {
//        // This method just provides a convenient way for us to generate fresh model
//        // instances of this current model. It is particularly useful during the
//        // hydration of new objects via the Eloquent query builder instances.
//        $model = is_null($attributes['type']) ?
//            new static($attributes) :
//            new $attributes['type']($attributes);
//
//        $model->exists = $exists;
//
//        $model->setConnection(
//            $this->getConnectionName()
//        );
//
//        $model->setTable($this->getTable());
//
//        $model->mergeCasts($this->casts);
//
//        $model->fill((array)$attributes);
//
//        return $model;
//    }
//
//    /**
//     * Create a new model instance that is existing.
//     *
//     * @param array $attributes
//     * @param string|null $connection
//     * @return static
//     */
//    public function newFromBuilder($attributes = [], $connection = null): static
//    {
//        $attributes = (array)$attributes;
//
//        $model = $this->newInstance([
//            'type' => $attributes['type'] ?? null,
//        ], true);
//
//        $model->setRawAttributes((array)$attributes, true);
//
//        $model->setConnection($connection ?: $this->getConnectionName());
//
//        $model->fireModelEvent('retrieved', false);
//
//        return $model;
//    }
}
