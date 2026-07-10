<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Agent\DashboardController as AgentDashboard;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\TourController;

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