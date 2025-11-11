<?php

use Illuminate\Support\Facades\Route;
use Alhawari\Rateify\Controllers\RateifyController;

Route::prefix('rateify')->name('rateify.')->middleware(['web', 'auth'])->group(function () {
    // Rate or update a rating
    Route::post('/rate', [RateifyController::class, 'store'])->name('rate');
    
    // Remove a rating
    Route::delete('/rate', [RateifyController::class, 'destroy'])->name('remove');
});
