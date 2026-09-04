<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
USE App\Http\Controllers\TickerSearchController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/DesignSystem', function () {
    return Inertia::render('DesignSystem/DesignSystem');
})->middleware(['auth', 'verified'])->name('design-system');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ações e FIIs
Route::resource('stocks', StockController::class)
    ->middleware(['auth', 'verified']);


// pesquisa de ações
Route::get('/tickers/search', [TickerSearchController::class, 'search'])
    ->middleware('auth')
    ->name('tickers.search');

require __DIR__.'/auth.php';
