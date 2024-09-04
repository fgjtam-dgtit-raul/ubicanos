<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
    HomeController,
    MapController,
    ProfileController,
    MunicipalityController
};

Route::get('/', [HomeController::class, 'index'])->named("home");

Route::get('/fiscalia-digital', [HomeController::class, 'redirectFiscaliaDigital'])->name('fiscalia-digital');

Route::middleware('auth')->group(function () {

    Route::get('/admin', fn() => Inertia::render('Dashboard') )->name('dashboard');

    Route::prefix('municipality')->name('municipality.')->group(function(){
        Route::get('', [MunicipalityController::class, 'index'])->name('index');
        Route::get('/{municipality_id}', [MunicipalityController::class, 'edit'])->name('edit');
        Route::patch('/{municipality_id}', [MunicipalityController::class, 'update'])->name('update');
    });

    Route::get('/map', [MapController::class, 'index'])->name('map');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';