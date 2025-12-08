<?php
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {

    Route::prefix('google')->name('google.')->group(function () {  
        Route::get('redirect', [Auth\GoogleController::class, 'redirect'])->name('redirect');
        Route::get('callback', [Auth\GoogleController::class, 'callback'])->name('callback');
        Route::get('confirm', [Auth\GoogleController::class, 'confirm'])->name('confirm');
    });

});