<?php

namespace App\Providers;

use App\Services\CustomerService;
use App\Services\CustomerServiceInterface;
use App\Services\DriverService;
use App\Services\DriverServiceInterface;
use App\Services\RideService;
use App\Services\RideServiceInterface;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
