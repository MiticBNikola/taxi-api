<?php

use App\Http\Controllers\RideController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\User\DriverController;
use Illuminate\Http\Request;
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

Route::get('/ride', [RideController::class, 'index']);
Route::post('/ride', [RideController::class, 'makeRequest']);
Route::put('/ride/{ride}/accept', [RideController::class, 'acceptRide']);
Route::put('/ride/{ride}/start', [RideController::class, 'startRide']);
Route::put('/ride/{ride}/end', [RideController::class, 'endRide']);
Route::delete('/ride/{ride}/cancel', [RideController::class, 'cancelRide']);

Route::get('/customer', [CustomerController::class, 'index']);
Route::get('/customer/{customer}', [CustomerController::class, 'show']);
Route::put('/customer/{customer}', [CustomerController::class, 'update']);
Route::delete('/customer/{customer}', [CustomerController::class, 'destroy']);
Route::resource('driver', DriverController::class)->only(['index', 'update', 'destroy']);
Route::prefix('driver')->group(function () {
    Route::get('/current-shift', [DriverController::class, 'currentShift']);
    Route::get('/available', [DriverController::class, 'available']);
    Route::get('/in-drive', [DriverController::class, 'inDrive']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
