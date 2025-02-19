<?php

namespace App\Providers;

use App\Services\CustomerService;
use App\Services\CustomerServiceInterface;
use App\Services\DriverService;
use App\Services\DriverServiceInterface;
use App\Services\ManagerService;
use App\Services\ManagerServiceInterface;
use App\Services\RideService;
use App\Services\RideServiceInterface;
use App\Services\SteerService;
use App\Services\SteerServiceInterface;
use App\Services\VehicleService;
use App\Services\VehicleServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RideServiceInterface::class, RideService::class);
        $this->app->singleton(CustomerServiceInterface::class, CustomerService::class);
        $this->app->singleton(DriverServiceInterface::class, DriverService::class);
        $this->app->singleton(ManagerServiceInterface::class, ManagerService::class);
        $this->app->singleton(VehicleServiceInterface::class, VehicleService::class);
        $this->app->singleton(SteerServiceInterface::class, SteerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
