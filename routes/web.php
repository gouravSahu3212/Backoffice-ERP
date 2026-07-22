<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AirportTransferRateController;
use App\Http\Controllers\Admin\CityTransferRateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\FullDayTransferRateController;
use App\Http\Controllers\Admin\TourBookingController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TransferBookingController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::middleware('role:Super Admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
            Route::resource('agents', AgentController::class);
            Route::patch('agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');
            Route::resource('tours', TourController::class)->except(['create', 'edit', 'show']);
            Route::patch('tours/{tour}/toggle-status', [TourController::class, 'toggleStatus'])->name('tours.toggle-status');

            // Transfers
            Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
            Route::resource('transfers/bookings', TransferBookingController::class)
                ->only(['index', 'show'])
                ->names('transfers.bookings');

            // Tours
            Route::get('tours', [TourController::class, 'index'])->name('tours.index');
            Route::resource('tours/bookings', TourBookingController::class)
                ->only(['index', 'show'])
                ->names('tours.bookings');

            Route::get('tours/requests', [TourController::class, 'requests'])->name('tour-requests.index');

            Route::post('transfers/city-rates', [CityTransferRateController::class, 'store'])->name('transfers.city-rates.store');
            Route::put('transfers/city-rates/{rate}', [CityTransferRateController::class, 'update'])->name('transfers.city-rates.update');
            Route::patch('transfers/city-rates/{rate}/toggle-status', [CityTransferRateController::class, 'toggleStatus'])->name('transfers.city-rates.toggle-status');
            Route::delete('transfers/city-rates/{rate}', [CityTransferRateController::class, 'destroy'])->name('transfers.city-rates.destroy');
            Route::post('transfers/locations', [CityTransferRateController::class, 'storeLocation'])->name('transfers.locations.store');
            Route::post('transfers/vehicle-types', [CityTransferRateController::class, 'storeVehicleType'])->name('transfers.vehicle-types.store');

            // Airport Transfer Rates
            Route::post('transfers/airport-rates', [AirportTransferRateController::class, 'store'])->name('transfers.airport-rates.store');
            Route::put('transfers/airport-rates/{rate}', [AirportTransferRateController::class, 'update'])->name('transfers.airport-rates.update');
            Route::patch('transfers/airport-rates/{rate}/toggle-status', [AirportTransferRateController::class, 'toggleStatus'])->name('transfers.airport-rates.toggle-status');
            Route::delete('transfers/airport-rates/{rate}', [AirportTransferRateController::class, 'destroy'])->name('transfers.airport-rates.destroy');
            Route::post('transfers/zones', [AirportTransferRateController::class, 'storeZone'])->name('transfers.zones.store');
            Route::post('transfers/airport-vehicle-types', [AirportTransferRateController::class, 'storeVehicleType'])->name('transfers.airport-vehicle-types.store');

            // Full-day Rates
            Route::post('transfers/full-day-rates', [FullDayTransferRateController::class, 'store'])->name('transfers.full-day-rates.store');
            Route::put('transfers/full-day-rates/{rate}', [FullDayTransferRateController::class, 'update'])->name('transfers.full-day-rates.update');
            Route::patch('transfers/full-day-rates/{rate}/toggle-status', [FullDayTransferRateController::class, 'toggleStatus'])->name('transfers.full-day-rates.toggle-status');
            Route::delete('transfers/full-day-rates/{rate}', [FullDayTransferRateController::class, 'destroy'])->name('transfers.full-day-rates.destroy');
            Route::post('transfers/vehicle-models', [FullDayTransferRateController::class, 'storeVehicleModel'])->name('transfers.vehicle-models.store');
        });

    Route::middleware('role:Agent')
        ->prefix('agent')
        ->name('agent.')
        ->group(function () {
            Route::get('/dashboard', AgentDashboard::class)->name('dashboard');
        });

});

Route::middleware('auth')->get('/dashboard', function () {

    $user = auth()->user();

    if ($user->hasRole('Super Admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('Agent')) {
        return redirect()->route('agent.dashboard');
    }

    abort(403);

})->name('dashboard');

require __DIR__.'/auth.php';
