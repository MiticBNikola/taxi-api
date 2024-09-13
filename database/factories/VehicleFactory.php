<?php

namespace Database\Factories;

use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomValue = rand(0, 1);
        return [
            'license_plate' => strtoupper($this->faker->bothify('??###??')),
            'registration_date' => $this->faker->randomElement(['01-12', '05-23', '03-12']),
            'brand' => $this->faker->randomElement(['Mercedes', 'BMW', 'Peugeot', 'VW']),
            'model' => $this->faker->randomElement(['GLE', 'M5', '308', 'Golf 5']),
            'model_year' => $this->faker->year(),
            'type' => $randomValue ? 'App\Models\Vehicle\PrivateVehicle' : 'App\Models\Vehicle\CompanyVehicle',
            'color' => $randomValue ? $this->faker->colorName() : null,
        ];
    }
}
