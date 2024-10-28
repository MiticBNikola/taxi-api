<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Steer;
use App\Models\User\Customer;
use App\Models\User\Driver;
use App\Models\User\Manager;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Manager::factory(10)->create();
        $drivers = Driver::factory(10)->create();
        Customer::factory(10)->create();
        $vehicles = Vehicle::factory(10)->create();

        // Go like this as for seeding there is no returned vehicle
        $drivers->each(function ($driver) use ($vehicles) {
            $vehicle = $vehicles->shift();
            Steer::factory()->create([
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
            ]);
        });
    }
}
