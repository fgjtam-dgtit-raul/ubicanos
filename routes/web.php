<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
    HomeController,
    MapController,
    ProfileController,
    MunicipalityController
};
use App\Http\Controllers\API\APIMunicipalityController;

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

Route::prefix('api')->name('api.')->group(function(){
    Route::get('municipalities', [APIMunicipalityController::class, 'getMumicipalities'])->name('municipalities');
    Route::get('municipalities/{municipalityId}', [APIMunicipalityController::class, 'getMunicipality'])->name('municipalities.index');
    Route::get('municipalities/{municipalityId}/locations', [APIMunicipalityController::class, 'getLocations'])->name('municipalities.locations');
});

require __DIR__.'/auth.php';