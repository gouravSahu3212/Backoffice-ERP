<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Agent\DashboardController as AgentDashboard;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\CityTransferRateController;

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
            Route::post('transfers/city-rates', [CityTransferRateController::class, 'store'])->name('transfers.city-rates.store');
            Route::put('transfers/city-rates/{rate}', [CityTransferRateController::class, 'update'])->name('transfers.city-rates.update');
            Route::patch('transfers/city-rates/{rate}/toggle-status', [CityTransferRateController::class, 'toggleStatus'])->name('transfers.city-rates.toggle-status');
            Route::delete('transfers/city-rates/{rate}', [CityTransferRateController::class, 'destroy'])->name('transfers.city-rates.destroy');
            Route::post('transfers/locations', [CityTransferRateController::class, 'storeLocation'])->name('transfers.locations.store');
            Route::post('transfers/vehicle-types', [CityTransferRateController::class, 'storeVehicleType'])->name('transfers.vehicle-types.store');
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