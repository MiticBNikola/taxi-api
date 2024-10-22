<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SteerController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\User\DriverController;
use App\Http\Controllers\Vehicle\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('auth/check', [LoginController::class, 'authCheck']);
Auth::routes();
Route::prefix('ride')->group(function () {
    Route::get('status', [RideController::class, 'checkStatus']);
    Route::post('', [RideController::class, 'makeRequest']);
    Route::delete('/{ride}/cancel', [RideController::class, 'cancelRide']);
    Route::put('/{ride}/customer-update-end', [RideController::class, 'customerUpdateEnd']);
});
Route::middleware( 'auth:sanctum')->group(function () {
    Route::resource('customer', CustomerController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::prefix('customer')->group(function () {
        Route::get('/{customer}/ride-status', [CustomerController::class, 'status']);
    });
    Route::resource('driver', DriverController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::prefix('driver')->group(function () {
        Route::get('/current-shift', [DriverController::class, 'currentShift']);
        Route::get('/available', [DriverController::class, 'available']);
        Route::get('/in-drive', [DriverController::class, 'inDrive']);
    });
    Route::prefix('ride')->group(function () {
        Route::get('', [RideController::class, 'index']);
        Route::put('/{ride}/update-end', [RideController::class, 'updateEnd']);
        Route::put('/{ride}/accept', [RideController::class, 'acceptRide']);
        Route::post('/{ride}/driver/{driverId}/position', [RideController::class, 'driverPosition']);
        Route::put('/{ride}/start', [RideController::class, 'startRide']);
        Route::put('/{ride}/end', [RideController::class, 'endRide']);
    });
    Route::prefix('vehicle')->group(function () {
        Route::get('/available', [VehicleController::class, 'available']);
    });
    Route::prefix('steer')->group(function () {
        Route::post('/assign', [SteerController::class, 'assignVehicle']);
        Route::put('/{steer}/release', [SteerController::class, 'releaseVehicle']);
    });
});
