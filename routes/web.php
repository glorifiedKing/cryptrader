<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'initialBalance' => config('trading.initial_balance'),
        'supportedSymbols' => config('trading.supported_symbols'),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('wallet', function () {
    return Inertia::render('Wallet', [
        'supportedSymbols' => config('trading.supported_symbols'),
    ]);
})->middleware(['auth', 'verified'])->name('wallet');

Route::get('trading', function () {
    return Inertia::render('Trading', [
        'supportedSymbols' => config('trading.supported_symbols'),
    ]);
})->middleware(['auth', 'verified'])->name('trading');

require __DIR__ . '/settings.php';
