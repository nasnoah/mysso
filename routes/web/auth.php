<?php
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {

    // foreach (App\Enums\ProviderName::cases() as $providerName) {
    //     Route::prefix($providerName->value)->name($providerName->value.'.')->group(function () use ($providerName) {  
    //         Route::get('redirect', [$providerName->controller(), 'redirect'])->name('redirect');
    //         Route::get('callback', [$providerName->controller(), 'callback'])->name('callback');
    //         Route::get('confirm', [$providerName->controller(), 'confirm'])->name('confirm');
    //     });
    // }

     Route::prefix('{provider}')->name('provider.')->group(function () {  
        Route::get('redirect', [Auth\IdentityProviderController::class, 'redirect'])->name('redirect');
        Route::get('callback', [Auth\IdentityProviderController::class, 'callback'])->name('callback');
        Route::get('confirm', [Auth\IdentityProviderController::class, 'confirm'])->name('confirm');
    });

});