<?php
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {

     Route::prefix('{provider}')->name('provider.')->group(function () {  
        Route::get('redirect', [Auth\IdentityProviderController::class, 'redirect'])->name('redirect');
        Route::get('callback', [Auth\IdentityProviderController::class, 'callback'])->name('callback');
        Route::get('confirm', [Auth\IdentityProviderController::class, 'confirm'])->name('confirm');
    });

});